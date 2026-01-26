<?php
require_once __DIR__.'/analytics_common.php';
header('Content-Type: application/json');

$analytic = $_GET['analytic'] ?? 'monthlyTrends';
$preset = $_GET['preset'] ?? 'last30';
$start = $_GET['start'] ?? null;
$end = $_GET['end'] ?? null;

if(!$start && in_array($preset,['last7','last30','ytd'])){ list($s,$e)=dateRangeFromPreset($preset); $start=$s; $end=$e; }

$payload = build_analytics_payload($conn, $analytic, $start, $end);
echo json_encode($payload);
?>