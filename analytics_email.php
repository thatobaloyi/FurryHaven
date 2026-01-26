<?php

if (session_status() === PHP_SESSION_NONE) session_start();

// ensure errors are logged and return JSON
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/analytics_error.log');
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

// capture fatal errors and return JSON
register_shutdown_function(function() {
    $err = error_get_last();
    if ($err !== null) {
        @ob_end_clean();
        error_log("Analytics fatal error: " . print_r($err, true));
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'fatal error', 'error_detail' => $err['message'] ?? 'unknown']);
        flush();
    }
});

try {
    require_once __DIR__ . '/analytics_common.php'; // must provide DB $conn and helpers
    // read JSON body
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        echo json_encode(['success' => false, 'error' => 'invalid request', 'error_detail' => 'expected JSON body']);
        exit;
    }

    $email = trim($input['email'] ?? '');
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'invalid email']);
        exit;
    }

    // analytic range params (optional) - passed to CSV builder in analytics_common
    $analytic = $input['analytic'] ?? 'monthlyTrends';
    $preset = $input['preset'] ?? ($input['range'] ?? 'last30');
    $start = $input['start'] ?? null;
    $end = $input['end'] ?? null;
    if (!$start && in_array($preset, ['last7','last30','ytd'])) {
        if (function_exists('dateRangeFromPreset')) {
            list($s,$e) = dateRangeFromPreset($preset);
            $start = $s; $end = $e;
        }
    }

    // Build CSV rows using helper from analytics_common.php
    if (!function_exists('build_csv_rows')) {
        // fallback simple CSV if helper missing
        $rows = [['note','no csv builder found','']];
    } else {
        $rows = build_csv_rows($conn, $analytic, $start, $end);
    }

    // convert rows to CSV string
    $fp = fopen('php://memory', 'r+');
    foreach ($rows as $r) fputcsv($fp, $r);
    rewind($fp);
    $csv = stream_get_contents($fp);
    fclose($fp);

    $subject = 'Analytics Export - ' . date('Y-m-d H:i:s');
    $filename = 'analytics_' . date('Ymd_His') . '.csv';

    // Force Donations SMTP credentials (always)
    $smtpHost   = 'smtp.gmail.com';
    $smtpUser   = 'furryhavendonations@gmail.com';
    $smtpPass   = 'oanj gsxj qumr voni';
    $smtpPort   = 587;
    $smtpSecure = 'tls';

    // Visible sender as requested
    $fromAddr = 'furryhavenanalytics@gmail.com';
    $fromName = 'FurryHaven Analytics';
    $replyToAddr = 'furryhavendonations@gmail.com';
    $replyToName = 'FurryHaven Donations';

    // Prefer PHPMailer via Composer
    if (file_exists(__DIR__ . '/vendor/autoload.php')) {
        require_once __DIR__ . '/vendor/autoload.php';
    }
    $phpMailerClass = 'PHPMailer\\PHPMailer\\PHPMailer';

    if (class_exists($phpMailerClass)) {
        $mail = new $phpMailerClass(true);
        try {
            // SMTP setup matching test_smtp
            $mail->isSMTP();
            $mail->Host       = $smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtpUser;
            $mail->Password   = $smtpPass;
            $mail->SMTPSecure = $smtpSecure;
            $mail->Port       = $smtpPort;

            // keep debug quiet in normal use; change to 2 for verbose logging
            $mail->SMTPDebug  = 0;
            $mail->Debugoutput = function($str, $level) {
                error_log("PHPMailer debug [{$level}]: {$str}");
            };

            $mail->setFrom($fromAddr, $fromName);
            $mail->addReplyTo($replyToAddr, $replyToName);
            $mail->addAddress($email);
            $mail->Subject = $subject;
            $mail->Body = "Please find attached the requested analytics export.";

            // If client provided a PDF blob, attach it; otherwise attach CSV
            if (!empty($input['pdf_base64']) && !empty($input['pdf_name'])) {
                $pdfData = base64_decode(preg_replace('#^data:application/[^;]+;base64,#', '', $input['pdf_base64']));
                if ($pdfData !== false) {
                    $mail->addStringAttachment($pdfData, basename($input['pdf_name']), 'base64', 'application/pdf');
                    $mail->Body = "Please find attached the requested analytics PDF.";
                } else {
                    // fall back to CSV attachment if pdf decode fails
                    $mail->addStringAttachment($csv, $filename, 'base64', 'text/csv');
                }
            } else {
                $mail->addStringAttachment($csv, $filename, 'base64', 'text/csv');
            }

            $mail->send();
            echo json_encode(['success' => true]);
            exit;
        } catch (Exception $e) {
            error_log('Analytics PHPMailer Exception: ' . $e->getMessage());
            if (isset($mail) && property_exists($mail, 'ErrorInfo')) {
                error_log('Analytics PHPMailer ErrorInfo: ' . $mail->ErrorInfo);
            }
            echo json_encode([
                'success' => false,
                'error' => 'smtp error (PHPMailer)',
                'error_detail' => $mail->ErrorInfo ?? $e->getMessage()
            ]);
            exit;
        }
    }

    // PHPMailer not available - fallback to mail() using visible From/reply-to
    $boundary = md5(time());
    $headers  = "From: {$fromName} <{$fromAddr}>\r\n";
    $headers .= "Reply-To: {$replyToName} <{$replyToAddr}>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

    $body  = "--{$boundary}\r\n";
    $body .= "Content-Type: text/plain; charset=ISO-8859-1\r\n\r\n";
    $body .= "Please find attached the requested analytics export.\r\n\r\n";
    $body .= "--{$boundary}\r\n";
    $body .= "Content-Type: text/csv; name=\"{$filename}\"\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n";
    $body .= "Content-Disposition: attachment; filename=\"{$filename}\"\r\n\r\n";
    $body .= chunk_split(base64_encode($csv));
    $body .= "\r\n--{$boundary}--";

    $sent = @mail($email, $subject, $body, $headers);
    if ($sent) {
        echo json_encode(['success' => true]);
    } else {
        $last = error_get_last();
        error_log('Analytics mail() failed: ' . print_r($last, true));
        echo json_encode([
            'success' => false,
            'error' => 'mail failed - server not configured to send mail',
            'error_detail' => $last['message'] ?? 'unknown'
        ]);
    }
    exit;

} catch (Throwable $e) {
    error_log('Analytics exception: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'exception', 'error_detail' => $e->getMessage()]);
    exit;
}

?>
