<!-- views/editor.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Invoice - SimpleInvoice</title>
    <link rel="stylesheet" href="/assets/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50">

    <!-- Toolbar -->
    <div class="w-full bg-white shadow-sm p-4 flex justify-between items-center sticky top-0 z-10">
        <div class="font-bold text-lg text-slate-800">SimpleInvoice</div>
        <div>
            <span class="mr-4 text-sm text-gray-500">Logged in as <?php echo $_SESSION['mobile']; ?></span>
            <a href="/logout" class="btn btn-danger text-sm">Logout</a>
        </div>
    </div>

    <div class="editor-wrapper">
        <div class="invoice-paper">
            
            <div class="header-grid">
                <div>
                   <div style="width: 100px; height: 100px; background: #f1f5f9; border: 1px dashed #cbd5e1; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 0.8rem; border-radius: 4px;">
                       Logo (Optional)
                   </div>
                </div>
                <div>
                    <h1 class="invoice-title">INVOICE</h1>
                    <div class="text-right">
                        <div class="mb-2">
                            <label>Invoice #</label>
                            <input type="text" id="invoice-number" value="INV-<?php echo strtoupper(uniqid()); ?>" style="text-align: right; width: auto;">
                        </div>
                        <div>
                            <label>Date</label>
                            <input type="date" id="invoice-date" value="<?php echo date('Y-m-d'); ?>" style="text-align: right; width: auto;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="details-grid">
                <div>
                    <h3 class="font-bold mb-4 text-slate-700">billed from</h3>
                    <div class="form-group">
                        <input type="text" id="sender-mobile" value="<?php echo $_SESSION['mobile']; ?>" readonly class="bg-gray-50" title="Login mobile">
                    </div>
                    <div class="form-group">
                        <input type="email" id="sender-email" placeholder="Sender Email (Required)" required>
                    </div>
                    <div class="form-group">
                        <textarea id="sender-address" placeholder="Sender Address / Company Details" rows="3"></textarea>
                    </div>
                </div>
                <div class="text-right">
                    <h3 class="font-bold mb-4 text-slate-700">billed to</h3>
                    <div class="form-group">
                        <input type="text" id="recipient-name" placeholder="Client Name" class="text-right">
                    </div>
                    <div class="form-group">
                        <input type="tel" id="recipient-mobile" placeholder="Client Mobile (Required)" required class="text-right">
                    </div>
                    <div class="form-group">
                        <input type="email" id="recipient-email" placeholder="Client Email (Required)" required class="text-right">
                    </div>
                    <div class="form-group">
                        <textarea id="recipient-address" placeholder="Client Address" rows="3" class="text-right"></textarea>
                    </div>
                </div>
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th class="col-desc">Item Description</th>
                        <th class="col-qty">Qty</th>
                        <th class="col-rate">Rate</th>
                        <th class="col-amount">Amount</th>
                        <th class="col-action"></th>
                    </tr>
                </thead>
                <tbody id="items-table-body">
                    <tr>
                        <td><input type="text" class="item-desc" placeholder="Description of service or product"></td>
                        <td><input type="number" class="item-qty" value="1" min="1"></td>
                        <td><input type="number" class="item-rate" value="0.00" step="0.01"></td>
                        <td class="text-right item-amount">0.00</td>
                        <td class="text-center"><button class="btn btn-danger remove-row">&times;</button></td>
                    </tr>
                </tbody>
            </table>

            <button id="add-item-btn" class="btn btn-secondary text-sm">+ Add Line Item</button>

            <div class="totals-section">
                <div class="total-row">
                    <span>Sub Total</span>
                    <span id="subtotal-display">0.00</span>
                </div>
                <div class="total-row">
                    <div class="flex items-center">
                        <span>Tax (%)</span>
                        <input type="number" id="tax-input" value="0" min="0" max="100" style="width: 60px; margin-left: 10px; padding: 2px;">
                    </div>
                    <span id="tax-amount-display">0.00</span>
                </div>
                <div class="total-row final">
                    <span>Total</span>
                    <span id="total-display">0.00</span>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100">
                <label>Notes</label>
                <textarea placeholder="It was great doing business with you." class="w-full text-sm text-gray-600 border-none resize-none"></textarea>
            </div>

        </div> <!-- End Paper -->

        <div class="flex justify-end mt-4 gap-4 pb-10">
            <button class="btn btn-secondary">Save Draft</button>
            <button id="download-pdf-btn" class="btn btn-primary">Save & Download PDF</button>
        </div>
    </div>

    <script src="/assets/script.js"></script>
</body>
</html>
