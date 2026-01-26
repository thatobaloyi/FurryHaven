<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__.'/analytics_common.php';

$analytic = $_GET['analytic'] ?? 'monthlyTrends';
$preset = $_GET['preset'] ?? 'last30';
$start = $_GET['start'] ?? null;
$end = $_GET['end'] ?? null;

if(!$start && in_array($preset,['last7','last30','ytd'])){ list($s,$e)=dateRangeFromPreset($preset); $start=$s; $end=$e; }

$rows = build_csv_rows($conn, $analytic, $start, $end);
$filename = 'analytics_export_'.date('Ymd_His').'.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="'.$filename.'"');
$out = fopen('php://output','w');
// optional UTF-8 BOM for Excel: uncomment if needed
// fwrite($out, "\xEF\xBB\xBF");
foreach($rows as $r) fputcsv($out, $r);
fclose($out);
exit;
?>