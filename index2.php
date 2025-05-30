<?php
session_start();

// Activer l'affichage des erreurs PHP pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclure la configuration de la base de données
require_once 'db_config.php';
require_once 'profile_icon.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['est_connecte']) || $_SESSION['est_connecte'] !== true) {
    header("Location: login.php");
    exit();
}

$imei = $_POST['imei'] ?? '';

// Get user ID from the session
$utilisateur_id = $_SESSION['utilisateur_id'];

// Initialize balance variable
$solde_usdt = 0;
$erreur = '';
$show_insufficient_balance = false;

try {
    // Retrieve user balance
    $stmt = $pdo->prepare("SELECT solde_usdt FROM utilisateurs WHERE id = :id");
    $stmt->bindParam(':id', $utilisateur_id);
    $stmt->execute();
    $utilisateur_info = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($utilisateur_info && isset($utilisateur_info['solde_usdt'])) {
        $solde_usdt = $utilisateur_info['solde_usdt'];
    } else {
        $erreur = "Error retrieving user balance.";
    }
} catch (PDOException $e) {
    $erreur = "Database error: " . $e->getMessage();
}

// Fetch iPhone models and prices (keeping these as they seem necessary for the form)
$models = [
    "iPhone-15-Pro-Max", "iPhone-15-Pro", "iPhone-15-plus", "iPhone-15",
    "iPhone-14-PRO-Max", "iPhone-14-PRO", "iPhone-14-plus", "iPhone-14",
    "iPhone-13-PRO-Max", "iPhone-13-PRO", "iPhone-13-Mini", "iPhone-13",
    "iPhone-12-PRO-MAX", "iPhone-12-PRO", "iPhone-12-Mini", "iPhone-12",
    "iPhone-11-PRO-Max", "iPhone-11-PRO", "iPhone-11", "iPhone-XR",
    "iPhone-XS-Max", "iPhone-XS", "iPhone-SE-3", "iPhone-SE-2",
    "older-devices", "iPad", "iPad-Pro", "iPad-Air", "iPad-mini",
];

$priceModeles = [
    "iPhone-15-Pro-Max" => 60, "iPhone-15-Pro" => 60, "iPhone-15-plus" => 55, "iPhone-15" => 50,
    "iPhone-14-PRO-Max" => 50, "iPhone-14-PRO" => 50, "iPhone-14-plus" => 45, "iPhone-14" => 40,
    "iPhone-13-PRO-Max" => 40, "iPhone-13-PRO" => 35, "iPhone-13-Mini" => 25, "iPhone-13" => 30,
    "iPhone-12-PRO-Max" => 30, "iPhone-12-PRO" => 30, "iPhone-12-Mini" => 25, "iPhone-12" => 25,
    "iPhone-11-PRO-Max" => 30, "iPhone-11-PRO" => 25, "iPhone-11" => 20, "iPhone-XR" => 20,
    "iPhone-XS-Max" => 20, "iPhone-XS" => 20, "iPhone-SE-3" => 25, "iPhone-SE-2" => 20,
    "older-devices" => 10, "iPad" => 15, "iPad-Pro" => 20, "iPad-Air" => 15, "iPad-mini" => 15,
];

$model = $_POST['model'] ?? ''; // Keep model for price display if selected
$price = isset($priceModeles[$model]) ? $priceModeles[$model] : 0;

// Check if the unlock button was clicked
if (isset($_POST['unlock_now'])) {
    if ($solde_usdt < $price) {
        $show_insufficient_balance = true;
        // No redirection here, we'll display the message on the same page
    } else {
        // Rediriger vers unlockkk.php si le solde est suffisant
        header("Location: unlockkk.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official iCloud Unlock</title>
    <style>
         body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background-color: #007bff;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        header img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 10px;
            object-fit: cover;
        }

        header h1 {
            color: white;
            margin: 0;
            font-size: 2em;
            font-weight: bold;
        }

        main {
            padding: 30px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .intro {
            text-align: center;
            margin-bottom: 30px;
        }

        .intro h1 {
            color: #28a745;
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .intro p {
            font-size: 1.1em;
            color: #555;
        }

        .steps {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            width: 80%;
            max-width: 600px;
        }

        .steps h3 {
            color: #007bff;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .steps ol {
            padding-left: 20px;
        }

        .steps li {
            margin-bottom: 10px;
            font-size: 1em;
        }

        .steps li ul {
            list-style: none;
            padding-left: 0;
            margin-top: 5px;
        }

        .steps li ul li {
            margin-bottom: 5px;
        }

        .steps li a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .steps li a:hover {
            text-decoration: underline;
        }

        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            width: 80%;
            max-width: 500px;
        }

        .form-container h3 {
            color: #007bff;
            margin-top: 0;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }

        .form-group input[type="text"],
        .form-group select {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 1em;
        }

        .form-group p#price {
            color: #28a745;
            font-weight: bold;
            margin-top: 10px;
        }

        .form-container button[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1.1em;
            width: 30%;
            transition: background-color 0.3s ease;
        }
        .form-container button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .form-container p.warning {
            color: darkred;
            font-size: 0.9em;
            margin-top: 10px;
            text-align: center;
        }

        .form-container p.error-message {
            color: red;
            font-size: 1.1em;
            margin-top: 15px;
            text-align: center;
        }

        .unlock-info {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            width: 80%;
            max-width: 700px;
            text-align: center;
        }

        .unlock-info h2 {
            color: #007bff;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .unlock-info p {
            font-size: 1.1em;
            color: #555;
            margin-bottom: 10px;
        }

        .why-choose {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            width: 80%;
            max-width: 500px;
        }

        .why-choose h2 {
            color: #007bff;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .why-choose ul {
            list-style: none;
            padding-left: 0;
        }

        .why-choose li {
            margin-bottom: 10px;
            font-size: 1em;
        }

        .why-choose li::before {
            content: '➡️';
            margin-right: 8px;
            color: #28a745;
        }

        .contact {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 30px;
            width: 80%;
            max-width: 600px;
        }

        .contact h2 {
            color: #007bff;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .contact p {
            font-size: 1.1em;
            color: #555;
            margin-bottom: 10px;
        }

        .contact ul {
            list-style: none;
            padding-left: 0;
        }

        .contact li {
            margin-bottom: 8px;
        }

        .contact li a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .contact li a:hover {
            text-decoration: underline;
        }

        footer {
            background-color: #343a40;
            color: #fff;
            text-align: center;
            padding: 15px;
            font-size: 0.9em;
        }

        footer a {
            color: #fff;
            text-decoration: none;
            margin-left: 10px;
        }

        footer a:hover {
            text-decoration: underline;
        }

        /* Suppression du carrousel d'images */
        .container, .image-container {
            display: none !important;
        }

        .form-container button[type="button"] { /* Original style for other buttons */
    background-color: #007bff;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1.1em;
    width: 30%;
    transition: background-color 0.3s ease;
}

.form-container button[type="button"]:hover { /* Original hover effect */
    background-color: #0056b3;
}

.form-container button[type="submit"] { /* Style for "Unlock Now" */
    background-color: #007bff;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1.1em;
    width: 30%;
    transition: background-color 0.3s ease;
}

.form-container button[type="submit"]:hover { /* Hover for "Unlock Now" */
    background-color: #0056b3;
}


.form-container button[onclick*="addmoney.php"] { /* Style for "Add Money" */
    background-color: #28a745; /* A green color for "Add Money" */
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1.1em;
    width: auto; /* Adjust width to content */
    margin-top: 10px; /* Add some space above the button */
    transition: background-color 0.3s ease;
}

.form-container button[onclick*="addmoney.php"]:hover { /* Hover for "Add Money" */
    background-color: #218838; /* Darker green on hover */
}
    </style>
</head>
<body>

    <main>
        <section class="intro">
            <h1>Unlock your iPhone in 15 minutes!</h1>
            <p>Official iCloud Unlock is a simple and secure service to unlock your iCloud locked iPhone.</p>
        </section>

        <section class="steps">
            <h3>How it works:</h3>
            <ol>
                <li>Enter your IMEI number.</li>
                <li>Select your iPhone model.</li>
                <li>Click on "Unlock Now".</li>
                <li>If your balance is sufficient, you will be redirected to the unlocking page.</li>
                <li>If your balance is insufficient, you will be prompted to add funds.</li>
            </ol>
        </section>

        <section class="form-container">
            <h3>Unlock Your iPhone</h3>
            <?php if ($show_insufficient_balance): ?>
                <p class="error-message">Insufficient balance. Please add funds to your account.</p>
                <button type="button" onclick="window.location.href='addmoney.php'">Add Funds</button>
            <?php else: ?>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="imei">IMEI Number:</label>
                        <input type="text" id="imei" name="imei" value="<?php echo htmlspecialchars($imei); ?>" placeholder="Enter your IMEI number" required>
                    </div>
                    <div class="form-group">
                        <label for="model">iPhone Model:</label>
                        <select id="model" name="model">
                            <option value="">Choose your model</option>
                            <?php foreach ($models as $option) : ?>
                                <option value="<?php echo htmlspecialchars($option); ?>" <?php if ($model == $option) echo "selected"; ?>>
                                    <?php echo htmlspecialchars(str_replace('-', ' ', $option)); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p id="price"><strong>Price:</strong> $<?php echo htmlspecialchars($price); ?></p>
                    <button type="submit" name="unlock_now" class="unlock-button">Unlock Now</button>
                    <p class="warning">⚠️: During the unlocking, please keep your phone connected to the internet via Wi-Fi 🛜 or cellular data 📶</p>
                </form>
            <?php endif; ?>
        </section>

        <section class="unlock-info">
            <h2>Unlock your iPhone with ease</h2>
            <p>Official iCloud Unlock offers a simple and fast service to unlock your iCloud locked iPhone.</p>
            <p>Our dedicated team of experts is here to help you regain access to your device quickly and efficiently.</p>
        </section>

        <section class="why-choose">
            <h2>Why choose Official iCloud Unlock?</h2>
            <ul>
                <li>Fast and secure service</li>
                <li>Satisfaction guarantee</li>
                <li>24/7 customer support</li>
                <li>Affordable prices</li>
            </ul>
        </section>

        <section class="contact">
            <h2>Contact Us</h2>
            <p>For more information or to place an order, please contact us via:</p>
            <ul>
                <li><a href="https://wa.me/16265874699" target="_blank">WhatsApp: +1(626)587-4699</a></li>
                <li><a href="mailto:cameronwalter77@gmail.com">📩: cameronwalter77@gmail.com</a></li>
                <li><a href="https://t.me/official24bypassiphone\" target=\"_blank\">Telegram: https://t.me/official24bypassiphone</a></li>
            </ul>
        </section>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Official iCloud Unlock</p>
        <a href="mentions-legales.php">Legal Notice</a>
    </footer>

    <script>
        const priceModeles = {
    "iPhone-15-Pro-Max": 60,
    "iPhone-15-Pro": 60,
    "iPhone-15-plus": 55,
    "iPhone-15": 50,
    "iPhone-14-PRO-Max": 50,
    "iPhone-14-PRO": 50,
    "iPhone-14-plus": 45,
    "iPhone-14": 40,
    "iPhone-13-PRO-Max": 40,
    "iPhone-13-PRO": 35,
    "iPhone-13-Mini": 25,
    "iPhone-13": 30,
    "iPhone-12-PRO-Max": 30,
    "iPhone-12-PRO": 30,
    "iPhone-12-Mini": 25,
    "iPhone-12": 25,
    "iPhone-11-PRO-Max": 30,
    "iPhone-11-PRO": 25,
    "iPhone-11": 20,
    "iPhone-XR": 20,
    "iPhone-XS-Max": 20,
    "iPhone-XS": 20,
    "iPhone-SE-3": 25,
    "iPhone-SE-2": 20,
    "older-devices": 10,
    "iPad": 15,
    "iPad-Pro": 20,
    "iPad-Air": 15,
    "iPad-mini": 15,
        };

        const modeleSelect = document.getElementById("model");
        const priceElement = document.getElementById("price");

        if (modeleSelect && priceElement) {
            modeleSelect.addEventListener("change", () => {
                const model = modeleSelect.value;
                const price = priceModeles[model] || 0; // Default to 0 if not found
                priceElement.textContent = `Price: $${price}`;
            });
        }
    </script>
</body>
</html>