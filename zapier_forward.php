<?php
// Simple server-side forwarder for Zapier webhook
// Accepts JSON POST from the client and forwards it to the configured Zapier hook.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

$raw = file_get_contents('php://input');
if (!$raw) {
    http_response_code(400);
    echo json_encode(array('ok' => false, 'error' => 'Empty request')); exit;
}

$payload = json_decode($raw, true);
if ($payload === null && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(array('ok' => false, 'error' => 'Invalid JSON')); exit;
}

// Basic size guard: limit total payload to ~6MB to avoid abuse
if (strlen($raw) > 6 * 1024 * 1024) {
    http_response_code(413);
    echo json_encode(array('ok' => false, 'error' => 'Payload too large')); exit;
}

// Zapier hook - keep same value as front-end used previously
$zap = 'https://hooks.zapier.com/hooks/catch/24893651/u9np8q2/';

// If payload contains base64 images, save them to uploads/facebook/ and replace with public URLs
if (isset($payload['images']) && is_array($payload['images']) && count($payload['images'])>0) {
    $savedUrls = array();
    $uploadDir = __DIR__ . '/uploads/facebook';
    if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);

    foreach ($payload['images'] as $img) {
        if (!is_string($img) || strpos($img, 'data:') !== 0) continue;
        // format: data:[mime];base64,AAA...
        if (preg_match('#^data:(image/[^;]+);base64,(.+)$#', $img, $m)) {
            $mime = $m[1];
            $b64 = $m[2];
            $ext = 'jpg';
            if (strpos($mime,'png')!==false) $ext = 'png';
            elseif (strpos($mime,'gif')!==false) $ext = 'gif';

            // guard file size (decoded)
            $decoded = base64_decode($b64);
            if ($decoded === false) continue;
            if (strlen($decoded) > 3 * 1024 * 1024) {
                // skip files larger than ~3MB
                continue;
            }

            $filename = 'fb_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            $path = $uploadDir . '/' . $filename;
            if (file_put_contents($path, $decoded) !== false) {
                // build a public URL relative to the current host
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'];
                $publicPath = $protocol . '://' . $host . dirname($_SERVER['SCRIPT_NAME']) . '/uploads/facebook/' . $filename;
                // normalize double slashes
                $publicPath = preg_replace('#([^:])/+#','\1/',$publicPath);
                $savedUrls[] = $publicPath;
            }
        }
    }
    if (count($savedUrls)>0) {
        // replace images with public URLs
        $payload['images'] = $savedUrls;
        // re-encode raw body for forwarding
        $raw = json_encode($payload);
        // update size guard
        if (strlen($raw) > 6 * 1024 * 1024) {
            http_response_code(413);
            echo json_encode(array('ok' => false, 'error' => 'Payload too large after processing images')); exit;
        }
    }
}

$ch = curl_init($zap);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
// set a sensible timeout
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

// First try with default SSL verification
$resp = curl_exec($ch);
$err = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($resp === false) {
    // Detect SSL certificate errors and attempt a temporary insecure fallback (for testing only)
    if (stripos($err, 'SSL certificate problem') !== false || stripos($err, 'unable to get local issuer certificate') !== false) {
        // retry with verification disabled
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $resp2 = curl_exec($ch);
        $err2 = curl_error($ch);
        $httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($resp2 === false) {
            http_response_code(502);
            echo json_encode(array('ok' => false, 'error' => 'Upstream request failed (fallback)', 'details' => $err2));
            exit;
        }
        // return a warning that the insecure fallback was used
        http_response_code($httpCode2 ?: 200);
        echo json_encode(array('ok' => true, 'status' => $httpCode2, 'response' => $resp2, 'warning' => 'SSL verification disabled for this request (testing only)'));
        exit;
    }
    curl_close($ch);
    http_response_code(502);
    echo json_encode(array('ok' => false, 'error' => 'Upstream request failed', 'details' => $err));
    exit;
}

// Forward Zapier response back to client
curl_close($ch);
http_response_code($httpCode ?: 200);
echo json_encode(array('ok' => true, 'status' => $httpCode, 'response' => $resp));

?>
