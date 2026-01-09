// assets/script.js

document.addEventListener('DOMContentLoaded', () => {
    
    // --- Login Logic ---
    const sendOtpBtn = document.getElementById('send-otp-btn');
    const verifyOtpBtn = document.getElementById('verify-otp-btn');
    
    if (sendOtpBtn) {
        sendOtpBtn.addEventListener('click', async () => {
            const mobile = document.getElementById('mobile').value;
            const errorMsg = document.getElementById('error-msg');
            
            if (!mobile) {
                errorMsg.textContent = "Please enter a mobile number.";
                errorMsg.classList.remove('hidden');
                return;
            }

            try {
                const res = await fetch('/api/send-otp', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ mobile })
                });
                const data = await res.json();
                
                if (data.success) {
                    document.getElementById('step-mobile').classList.add('hidden');
                    document.getElementById('step-otp').classList.remove('hidden');
                    document.getElementById('display-mobile').textContent = mobile;
                    errorMsg.classList.add('hidden');
                    alert(data.message); // Show mock OTP
                } else {
                    errorMsg.textContent = data.message;
                    errorMsg.classList.remove('hidden');
                }
            } catch (err) {
                console.error(err);
            }
        });

        document.getElementById('change-mobile').addEventListener('click', (e) => {
            e.preventDefault();
            document.getElementById('step-mobile').classList.remove('hidden');
            document.getElementById('step-otp').classList.add('hidden');
        });

        verifyOtpBtn.addEventListener('click', async () => {
            const mobile = document.getElementById('display-mobile').textContent;
            const otp = document.getElementById('otp').value;
            const errorMsg = document.getElementById('error-msg');

            try {
                const res = await fetch('/api/verify-otp', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ mobile, otp })
                });
                const data = await res.json();
                
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    errorMsg.textContent = data.message;
                    errorMsg.classList.remove('hidden');
                }
            } catch (e) {
                console.error(e);
            }
        });
    }


    // --- Invoice Editor Logic ---
    const invoiceTable = document.getElementById('items-table-body');
    const addItemBtn = document.getElementById('add-item-btn');
    
    if (invoiceTable) {
        // Initial Calculation
        calculateTotals();

        addItemBtn.addEventListener('click', () => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" class="item-desc" placeholder="Description"></td>
                <td><input type="number" class="item-qty" value="1" min="1"></td>
                <td><input type="number" class="item-rate" value="0.00" step="0.01"></td>
                <td class="text-right item-amount">0.00</td>
                <td class="text-center"><button class="btn btn-danger remove-row">&times;</button></td>
            `;
            invoiceTable.appendChild(row);
        });

        invoiceTable.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-row')) {
                e.target.closest('tr').remove();
                calculateTotals();
            }
        });

        invoiceTable.addEventListener('input', (e) => {
            if (e.target.classList.contains('item-qty') || e.target.classList.contains('item-rate')) {
                const row = e.target.closest('tr');
                const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
                const rate = parseFloat(row.querySelector('.item-rate').value) || 0;
                const amount = qty * rate;
                row.querySelector('.item-amount').textContent = amount.toFixed(2);
                calculateTotals();
            }
        });

        function calculateTotals() {
            let subtotal = 0;
            document.querySelectorAll('.item-amount').forEach(el => {
                subtotal += parseFloat(el.textContent) || 0;
            });

            const taxRate = parseFloat(document.getElementById('tax-input').value) || 0;
            const taxAmount = subtotal * (taxRate / 100);
            const total = subtotal + taxAmount;

            document.getElementById('subtotal-display').textContent = subtotal.toFixed(2);
            document.getElementById('tax-amount-display').textContent = taxAmount.toFixed(2);
            document.getElementById('total-display').textContent = total.toFixed(2);
        }

        document.getElementById('tax-input').addEventListener('input', calculateTotals);

        // Save & Download
        document.getElementById('download-pdf-btn').addEventListener('click', async () => {
             // 1. Gather Data
             const invoiceData = {
                 invoice_number: document.getElementById('invoice-number').value,
                 date: document.getElementById('invoice-date').value,
                 sender: {
                     mobile: document.getElementById('sender-mobile').value,
                     email: document.getElementById('sender-email').value,
                     address: document.getElementById('sender-address').value
                 },
                 recipient: {
                     name: document.getElementById('recipient-name').value,
                     email: document.getElementById('recipient-email').value,
                     mobile: document.getElementById('recipient-mobile').value,
                     address: document.getElementById('recipient-address').value
                 },
                 items: Array.from(document.querySelectorAll('#items-table-body tr')).map(row => ({
                     desc: row.querySelector('.item-desc').value,
                     qty: row.querySelector('.item-qty').value,
                     rate: row.querySelector('.item-rate').value,
                     amount: row.querySelector('.item-amount').textContent
                 })),
                 subtotal: document.getElementById('subtotal-display').textContent,
                 tax_rate: document.getElementById('tax-input').value,
                 tax_amount: document.getElementById('tax-amount-display').textContent,
                 total: document.getElementById('total-display').textContent
             };

             // Validation
             if(!invoiceData.sender.mobile || !invoiceData.sender.email || !invoiceData.recipient.mobile || !invoiceData.recipient.email) {
                 alert("Sender and Recipient Mobile & Email are Mandatory.");
                 return;
             }
             
             // 2. Save
             const res = await fetch('/api/save-invoice', {
                 method: 'POST',
                 headers: {'Content-Type': 'application/json'},
                 body: JSON.stringify(invoiceData)
             });
             const data = await res.json();

             if (data.success) {
                 // 3. Trigger Download
                 window.location.href = `/download-pdf?id=${data.invoice_id}`;
             } else {
                 alert("Error saving invoice: " + data.message);
             }
        });
    }
});
