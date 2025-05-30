<?php
session_start();
require_once 'db_config.php';
require_once 'profile_icon.php';

// You might use PHP for dynamic pricing or fetching data later
$usdt_conversion_rate = 1; // Example: 1 USD = 1 USDT (You could fetch this dynamically)

// Example of using $_SESSION (you can adapt this to your needs)
if (!isset($_SESSION['page_visits'])) {
    $_SESSION['page_visits'] = 1;
} else {
    $_SESSION['page_visits']++;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official iCloud Unlock</title>
    <link rel="stylesheet" href="innovative-style.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
         /* innovative-style.css - Additions for this page */

        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            color: #333;
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
        }

        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .unlock-steps {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            width: 80%;
            max-width: 600px;
        }

        .unlock-steps h2 {
            color: #007bff;
        }

        .unlock-steps ol {
            list-style-type: decimal;
            padding-left: 20px;
        }

        .unlock-steps li {
            margin-bottom: 10px;
        }

        .payment-section {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 80%;
            max-width: 600px;
            text-align: center;
        }

        .payment-button {
            display: inline-flex;
            align-items: center;
            background-color: #28a745;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1.1em;
            text-decoration: none;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        .payment-button img {
            margin-right: 10px;
            border-radius: 50%;
        }

        .payment-button:hover {
            background-color: #218838;
        }

        .contact-info {
            margin-top: 20px;
            text-align: center;
            color: #777;
        }

        .contact-info a {
            color: #007bff;
            text-decoration: none;
            margin: 0 10px;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #f0f0f0;
            border-top: 1px solid #ddd;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <main>
        <section class="unlock-steps">
            <h2>How to Unlock Your iCloud</h2>
            <ol>
                <li>
                    Click the "Pay with Binance" button below to proceed with the payment.
                    <p style="font-weight: bold;">
                        <i class="fas fa-sync-alt"></i> 
                        <span style="color: red;">(1 USD = <?php echo $usdt_conversion_rate; ?> USDT)</span>
                        <span style="color: #1d8e1d;">Payment corrections are accepted.</span>
                    </p>
                </li>
                <li>
                    Once the payment is confirmed, the unlock process will start automatically. Please allow a few minutes for completion.
                </li>
            </ol>
        </section>

        <section class="payment-section">
            <h1>Secure Cryptocurrency Payment</h1>
            <p>Ready to unlock? Click the button below to pay securely.</p>
            <a href="https://s.binance.com/k1bEcoco" class="payment-button">
                <img src="img/coin.gif" alt="Binance Pay" width="60" height="60">
                Pay with Binance
            </a>

            <?php if (isset($_SESSION['page_visits'])): ?>
                <p>You have visited this page <?php echo $_SESSION['page_visits']; ?> times.</p>
            <?php endif; ?>

        </section>

        <section class="contact-info">
            <p style="color: #cc1313;">
                <i class="fas fa-exclamation-triangle"></i> 
                Need help? Contact us:
            </p>
            <a href="https://wa.me/16265874699" target="_blank">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a> |
            <a href="mailto:cameronwalter77@gmail.com">
                <i class="far fa-envelope"></i> Email
            </a>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Official iCloud Unlock</p>
        <a href="mentions-legales.html">Legal Notice</a>
    </footer>

</body>
</html>