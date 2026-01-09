<?php
// generate_pdf.php

// Check if composer dependencies are installed
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    die("Composer dependencies not installed. Please run 'composer require dompdf/dompdf'.");
}

require 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_GET['id'])) {
    die("Invoice ID required.");
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$invoice = $stmt->fetch();

if (!$invoice) {
    die("Invoice not found or unauthorized.");
}

$items = json_decode($invoice['items'], true);

// Render HTML
// Note: We duplicate some styles inline for PDF compatibility
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 14px; color: #333; }
        .invoice-box { width: 100%; margin: auto; padding: 20px; }
        .header { width: 100%; margin-bottom: 40px; }
        .footer { width: 100%; position: absolute; bottom: 0; text-align: center; font-size: 12px; color: #777; }
        table { width: 100%; border-collapse: collapse; }
        
        .title { text-align: right; text-transform: uppercase; font-size: 30px; color: #0F172A; }
        
        .meta-table { margin-bottom: 20px; }
        .meta-table td { padding: 5px; }

        .items-table { width: 100%; margin-top: 30px; border: 1px solid #eee; }
        .items-table th { background: #f8fafc; padding: 10px; text-align: left; border-bottom: 2px solid #ddd; }
        .items-table td { padding: 10px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        
        .totals { width: 300px; float: right; margin-top: 20px; }
        .totals-row { padding: 5px; border-bottom: 1px solid #eee; }
        .total-final { font-weight: bold; font-size: 16px; border-top: 2px solid #333; border-bottom: none; margin-top: 10px; padding-top: 10px; }

        .clearfix { clear: both; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table class="header">
            <tr>
                <td style="vertical-align: top;">
                    <!-- Logo Placeholder -->
                    <div style="background: #f1f5f9; width: 80px; height: 80px; text-align: center; line-height: 80px; color: #999; border: 1px dashed #ccc;">
                        Logo
                    </div>
                    <br>
                    <strong>Billed From:</strong><br>
                    <?php echo htmlspecialchars($invoice['sender_email']); ?><br>
                    <?php echo nl2br(htmlspecialchars($invoice['sender_address'])); ?><br>
                    <?php echo htmlspecialchars($invoice['sender_mobile']); ?>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <div class="title">INVOICE</div>
                    <br>
                    <strong>Invoice #:</strong> <?php echo htmlspecialchars($invoice['invoice_number']); ?><br>
                    <strong>Date:</strong> <?php echo htmlspecialchars($invoice['invoice_date']); ?><br>
                    <br>
                    <strong>Billed To:</strong><br>
                    <?php echo htmlspecialchars($invoice['recipient_name']); ?><br>
                    <?php echo htmlspecialchars($invoice['recipient_email']); ?><br>
                    <?php echo nl2br(htmlspecialchars($invoice['recipient_address'])); ?><br>
                    <?php echo htmlspecialchars($invoice['recipient_mobile']); ?>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th style="width: 50px;">Qty</th>
                    <th style="width: 80px;">Rate</th>
                    <th style="width: 80px; text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['desc']); ?></td>
                    <td><?php echo htmlspecialchars($item['qty']); ?></td>
                    <td><?php echo number_format($item['rate'], 2); ?></td>
                    <td class="text-right"><?php echo htmlspecialchars($item['amount']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-row">
                Sub Total: <span style="float:right"><?php echo number_format($invoice['subtotal'], 2); ?></span>
            </div>
            <div class="totals-row">
                Tax (<?php echo floatval($invoice['tax_rate']); ?>%): <span style="float:right"><?php echo number_format($invoice['tax_amount'], 2); ?></span>
            </div>
            <div class="totals-row total-final">
                Total: <span style="float:right"><?php echo number_format($invoice['total'], 2); ?></span>
            </div>
        </div>

        <div class="clearfix"></div>

        <br><br>
        <strong>Notes:</strong><br>
        It was great doing business with you.
    </div>
</body>
</html>
<?php
$html = ob_get_clean();

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true); // Needed if loading images from URL

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream("Invoice-" . $invoice['invoice_number'] . ".pdf", ["Attachment" => true]);
