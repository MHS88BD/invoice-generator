<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Invoice Generator</title>
    <link rel="stylesheet" href="/assets/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 flex items-center justify-center h-screen">

    <div class="login-container">
        <h1 class="logo">SimpleInvoice</h1>
        <p class="subtitle">Enter your mobile number to create professional invoices.</p>

        <div id="step-mobile">
            <div class="form-group">
                <label for="mobile">Mobile Number</label>
                <input type="tel" id="mobile" placeholder="e.g. +1234567890" required>
            </div>
            <button id="send-otp-btn" class="btn btn-primary">Get Verification Code</button>
        </div>

        <div id="step-otp" class="hidden">
            <div class="form-group">
                <label for="otp">Verification Code</label>
                <input type="text" id="otp" placeholder="e.g. 1234" maxlength="4">
            </div>
            <button id="verify-otp-btn" class="btn btn-primary">Verify & Login</button>
            <p class="resend-text">Sent to <span id="display-mobile"></span>. <a href="#" id="change-mobile">Change?</a></p>
        </div>

        <p id="error-msg" class="error-text hidden"></p>
    </div>

    <script src="/assets/script.js"></script>
</body>
</html>
