<?php
require_once('config/db.php');
require_once('fpdf/fpdf.php'); // adjust path to match where FPDF lives on your setup

// ---- 1. Get and validate the date range ----
$start_date = $_GET['start_date'] ?? '';
$end_date   = $_GET['end_date'] ?? '';

if ($start_date === '' || $end_date === '') {
    die('Please provide both start_date and end_date, e.g. export_report.php?start_date=2026-07-01&end_date=2026-07-13');
}

// Basic sanity check on format (YYYY-MM-DD)
$dateFormatOk = function ($d) {
    $parsed = DateTime::createFromFormat('Y-m-d', $d);
    return $parsed && $parsed->format('Y-m-d') === $d;
};

if (!$dateFormatOk($start_date) || !$dateFormatOk($end_date)) {
    die('Dates must be in YYYY-MM-DD format.');
}

if ($start_date > $end_date) {
    die('start_date must be before end_date.');
}

// ---- 2. Pull the records ----
$stmt = $conn->prepare("
    SELECT u.name, a.date, a.clock_in, a.lunch_out, a.lunch_in, a.clock_out, a.is_late, a.lunch_late_minutes
    FROM attendance a
    JOIN users u ON u.id = a.user_id
    WHERE a.date BETWEEN ? AND ?
    ORDER BY a.date ASC, u.name ASC
");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('No attendance records found for that date range.');
}

// ---- 3. Build the PDF ----
class AttendancePDF extends FPDF
{
    public $rangeLabel = '';

    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 8, 'Vital Consultants - Attendance Report', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, $this->rangeLabel, 0, 1, 'C');
        $this->Ln(4);

        // Table header
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(255, 215, 0); // gold, matches the kiosk theme
        $this->Cell(35, 7, 'Name', 1, 0, 'C', true);
        $this->Cell(22, 7, 'Date', 1, 0, 'C', true);
        $this->Cell(20, 7, 'Clock In', 1, 0, 'C', true);
        $this->Cell(20, 7, 'Lunch Out', 1, 0, 'C', true);
        $this->Cell(20, 7, 'Lunch In', 1, 0, 'C', true);
        $this->Cell(20, 7, 'Clock Out', 1, 0, 'C', true);
        $this->Cell(15, 7, 'Late', 1, 0, 'C', true);
        $this->Cell(28, 7, 'Lunch Late (min)', 1, 1, 'C', true);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function Row($name, $date, $clockIn, $lunchOut, $lunchIn, $clockOut, $isLate, $lunchLate)
    {
        $this->SetFont('Arial', '', 8);
        $this->Cell(35, 6, $name, 1);
        $this->Cell(22, 6, $date, 1, 0, 'C');
        $this->Cell(20, 6, $clockIn ?: '-', 1, 0, 'C');
        $this->Cell(20, 6, $lunchOut ?: '-', 1, 0, 'C');
        $this->Cell(20, 6, $lunchIn ?: '-', 1, 0, 'C');
        $this->Cell(20, 6, $clockOut ?: '-', 1, 0, 'C');
        $this->Cell(15, 6, $isLate ? 'Yes' : 'No', 1, 0, 'C');
        $this->Cell(28, 6, $lunchLate !== null ? $lunchLate : '-', 1, 1, 'C');
    }
}

$pdf = new AttendancePDF();
$pdf->rangeLabel = 'Period: ' . date('d M Y', strtotime($start_date)) . ' - ' . date('d M Y', strtotime($end_date));
$pdf->AliasNbPages();
$pdf->AddPage('L'); // landscape fits the columns better

while ($row = $result->fetch_assoc()) {
    $pdf->Row(
        $row['name'],
        date('d M', strtotime($row['date'])),
        $row['clock_in'] ? date('h:i A', strtotime($row['clock_in'])) : null,
        $row['lunch_out'] ? date('h:i A', strtotime($row['lunch_out'])) : null,
        $row['lunch_in'] ? date('h:i A', strtotime($row['lunch_in'])) : null,
        $row['clock_out'] ? date('h:i A', strtotime($row['clock_out'])) : null,
        $row['is_late'],
        $row['lunch_late_minutes']
    );
}

// ---- 4. Output ----
$filename = "attendance_{$start_date}_to_{$end_date}.pdf";
$pdf->Output('D', $filename); // 'D' forces browser download