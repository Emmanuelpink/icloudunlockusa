<?php
session_start();

// Include database configuration (if needed for recording transactions)
require_once 'db_config.php'; // Removed database connection
require_once 'profile_fonctions.php';

// Check if the user is logged in (you should have your authentication in place)
if (!isset($_SESSION['est_connecte']) || $_SESSION['est_connecte'] !== true) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['utilisateur_id']; // Get user ID from session
$binance_address = "574484331"; // Replace with your actual Binance address!

// Placeholder for QR code generation (replace with a library like phpqrcode)
$qr_code_path = "/imgs/qrcode.jpg"; // Corrected path relative to the web root

$success_message = "";
$error_message = "";
$loading = false; // Add a variable to track loading state

// Process form submission (if any - for amount input, etc.)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input (VERY IMPORTANT!)
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    if ($amount === false || $amount <= 0) {
        $error_message = "Please enter a valid amount.";
    } else {
        $loading = true; // Set loading to true when form is submitted
        // ... (rest of your PHP code) ...
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Funds</title>
    <style>
        /* Innovative CSS */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom, #f0f2f5, #e2eafc);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 90%;
            max-width: 500px;
            text-align: center;
        }

        h1 {
            color: #4c6ef5;
            margin-bottom: 20px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.05);
        }

        .input-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-bottom: 25px;
        }

        .input-group label {
            font-weight: bold;
            color: #555;
            margin-bottom: 8px;
            transition: color 0.3s ease;
        }

        .binance-id-display {
            font-weight: bold;
            color: green;
            padding: 12px 15px;
            border-radius: 24px;
            background-color: #f3f4f6;
            box-shadow: inset 2px 2px 5px #d1d5db, inset -2px -2px 5px #fff;
            margin-bottom: 10px;
            display: block; /* Make it a block-level element */
        }

        .copy-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-top: 10px;
        }

        .copy-button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .qr-code {
            margin: 30px 0;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 16px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .qr-code img {
            max-width: 60%;
            height: auto;
            border-radius: 8px;
        }

        button[type="submit"] {
            background: linear-gradient(to right, #4a90e2, #63b8ff);
            color: white;
            padding: 14px 28px;
            border: none;
            border-radius: 28px;
            cursor: pointer;
            font-size: 1em;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }

        button[type="submit"]:hover {
            box-shadow: 0 7px 15px rgba(0, 0, 0, 0.2);
            transform: translateY(-2px);
        }

        .success {
            color: #4caf50;
            background-color: #e8f5e9;
            padding: 10px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .error {
            color: #f44336;
            background-color: #ffebee;
            padding: 10px;
            border-radius: 8px;
            margin-top: 20px;
        }

        /* Responsive adjustments */
        @media screen and (max-width: 480px) {
            .container {
                padding: 30px;
            }

            .input-group {
                margin-bottom: 20px;
            }

            .copy-button {
                margin-top: 8px;
            }

            .qr-code {
                margin: 20px 0;
                padding: 15px;
            }
        }

        /* Loading Spinner */
        .loader-container {
            display: <?php echo $loading ? 'flex' : 'none'; ?>; /* Show/hide based on $loading */
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
        }

        .loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-text {
            margin-top: 10px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add Funds to Your Account</h1>

        <?php if ($success_message): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <div class="input-group">
            <label for="binance-address">Binance ID:</label>
            <span id="binance-address" class="binance-id-display"><?php echo htmlspecialchars($binance_address); ?></span>
            <button class="copy-button" onclick="copyToClipboard('binance-address')">Copy</button>
        </div>

        <div class="qr-code">
            <p>Scan this QR code to send payment:</p>
            <img src="<?php echo $qr_code_path; ?>" alt="Binance QR Code">
        </div>

        <form method="post">
            <div class="input-group">
                <label for="amount">Amount (USDT):</label>
                <input type="number" id="amount" name="amount" step="0.01" required>
            </div>
            <?php if (!$loading): ?>
                <button type="submit">Proceed to Payment</button>
            <?php else: ?>
                <div class="loader-container">
                    <div class="loader"></div>
                    <p class="loading-text">Waiting for payment... scan QR code or copie ID to continue on binance</p>
                </div>
            <?php endif; ?>
        </form>

    </div>

    <script>
        function copyToClipboard(elementId) {
            const element = document.getElementById(elementId);
            // For a span, get the text content
            const textToCopy = element.textContent;
            const tempInput = document.createElement('input');
            tempInput.value = textToCopy;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            alert('Binance ID copied to clipboard!');
        }
    </script>
</body>
</html>