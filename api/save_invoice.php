<?php
// api/save_invoice.php

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

// Validation
$sender_email = $input['sender']['email'] ?? '';
$recipient_mobile = $input['recipient']['mobile'] ?? '';
$recipient_email = $input['recipient']['email'] ?? '';

if (empty($sender_email) || empty($recipient_mobile) || empty($recipient_email)) {
    echo json_encode(['success' => false, 'message' => 'Missing mandatory fields']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO invoices (
        user_id, invoice_number, invoice_date, 
        sender_mobile, sender_email, sender_address,
        recipient_mobile, recipient_email, recipient_name, recipient_address,
        items, subtotal, tax_rate, tax_amount, total
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $_SESSION['user_id'],
        $input['invoice_number'],
        $input['date'],
        $input['sender']['mobile'],
        $input['sender']['email'],
        $input['sender']['address'],
        $input['recipient']['mobile'],
        $input['recipient']['email'],
        $input['recipient']['name'],
        $input['recipient']['address'],
        json_encode($input['items']),
        $input['subtotal'],
        $input['tax_rate'],
        $input['tax_amount'],
        $input['total']
    ]);

    echo json_encode(['success' => true, 'invoice_id' => $pdo->lastInsertId()]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}
