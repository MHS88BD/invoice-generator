<?php
// api/auth.php

header('Content-Type: application/json');

$action = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if ($action === '/api/send-otp') {
        $mobile = $data['mobile'] ?? '';
        if (!$mobile) {
            echo json_encode(['success' => false, 'message' => 'Mobile number required']);
            exit;
        }

        // Generate Mock OTP
        $otp = '1234'; 
        // In real app: $otp = rand(1000, 9999);
        $expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE mobile_number = ?");
        $stmt->execute([$mobile]);
        $user = $stmt->fetch();

        if ($user) {
            $stmt = $pdo->prepare("UPDATE users SET otp_code = ?, otp_expiry = ? WHERE id = ?");
            $stmt->execute([$otp, $expiry, $user['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (mobile_number, otp_code, otp_expiry) VALUES (?, ?, ?)");
            $stmt->execute([$mobile, $otp, $expiry]);
        }

        // Mock sending
        // In production: send_sms($mobile, $otp);
        
        echo json_encode(['success' => true, 'message' => 'OTP sent (Use 1234)']);
        exit;
    }

    if ($action === '/api/verify-otp') {
        $mobile = $data['mobile'] ?? '';
        $otp = $data['otp'] ?? '';

        $stmt = $pdo->prepare("SELECT id, otp_code, otp_expiry FROM users WHERE mobile_number = ?");
        $stmt->execute([$mobile]);
        $user = $stmt->fetch();

        if ($user) {
            if ($user['otp_code'] === $otp && strtotime($user['otp_expiry']) > time()) {
                // Success
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['mobile'] = $mobile;
                
                // Clear OTP
                $pdo->prepare("UPDATE users SET otp_code = NULL WHERE id = ?")->execute([$user['id']]);

                echo json_encode(['success' => true, 'redirect' => '/dashboard']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid or expired OTP']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
        exit;
    }
}
