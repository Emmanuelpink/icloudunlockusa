<?php
session_start();

// Inclure la configuration de la base de données
require_once 'db_config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['est_connecte']) || $_SESSION['est_connecte'] !== true) {
    header("Location: login.php");
    exit();
}

$utilisateur_id = $_SESSION['utilisateur_id'];
$nom_utilisateur = $_SESSION['nom_utilisateur'];
$email = '';
$solde_usdt = 0;
$erreur = '';
$message = '';

try {
    // Récupérer les informations de l'utilisateur et le solde
    $stmt = $pdo->prepare("SELECT email, solde_usdt FROM utilisateurs WHERE id = :id");
    $stmt->bindParam(':id', $utilisateur_id);
    $stmt->execute();
    $utilisateur_info = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($utilisateur_info) {
        $email = $utilisateur_info['email'];
        if (isset($utilisateur_info['solde_usdt'])) {
            $solde_usdt = $utilisateur_info['solde_usdt'];
        }
    } else {
        $erreur = "Error retrieving user information.";
    }
} catch (PDOException $e) {
    $erreur = "Database error: " . $e->getMessage();
}

// Traitement du changement de mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ancien_mot_de_passe']) && isset($_POST['nouveau_mot_de_passe']) && isset($_POST['confirmer_nouveau_mot_de_passe'])) {
    $ancienMotDePasse = trim($_POST['ancien_mot_de_passe']);
    $nouveauMotDePasse = trim($_POST['nouveau_mot_de_passe']);
    $confirmerNouveauMotDePasse = trim($_POST['confirmer_nouveau_mot_de_passe']);

    if (empty($ancienMotDePasse) || empty($nouveauMotDePasse) || empty($confirmerNouveauMotDePasse)) {
        $erreur = "Please fill in all password fields.";
    } elseif ($nouveauMotDePasse !== $confirmerNouveauMotDePasse) {
        $erreur = "The new password and confirmation do not match.";
    } elseif (strlen($nouveauMotDePasse) < 6) {
        $erreur = "The new password must be at least 6 characters long.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT mot_de_passe FROM utilisateurs WHERE id = :id");
            $stmt->bindParam(':id', $utilisateur_id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && password_verify($ancienMotDePasse, $row['mot_de_passe'])) {
                $nouveauMotDePasseHache = password_hash($nouveauMotDePasse, PASSWORD_DEFAULT);
                $updateStmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = :nouveau_mot_de_passe WHERE id = :id");
                $updateStmt->bindParam(':nouveau_mot_de_passe', $nouveauMotDePasseHache);
                $updateStmt->bindParam(':id', $utilisateur_id);
                if ($updateStmt->execute()) {
                    $message = "Password updated successfully!";
                } else {
                    $erreur = "Error updating password.";
                }
            } else {
                $erreur = "Incorrect old password.";
            }
        } catch (PDOException $e) {
            $erreur = "Error updating password: " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Management</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; background-color: #f4f4f4; color: #333; }
        h1, h2 { color: #007bff; }
        .profile-info { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #555; font-weight: bold; }
        .form-group input[type="password"] { width: calc(100% - 12px); padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; transition: background-color 0.3s ease; }
        button:hover { background-color: #0056b3; }
        .error { color: red; margin-top: 10px; }
        .success { color: green; margin-top: 10px; }
        .account-actions a { color: #007bff; text-decoration: none; margin-right: 10px; }
        .account-actions a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>Account Management</h1>

    <div class="profile-info">
        <h2>Account Information</h2>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['nom_utilisateur']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
        <p><strong>Balance:</strong> <span style="color: green;"><?php echo htmlspecialchars($solde_usdt); ?> USDT</span>
        <button onclick="window.location.href='addmoney.php'">Add Money</button>
    </div>

    <div class="account-actions">
        <p><a href="logout.php">Log out</a></p>
        <button onclick="window.location.href='index2.php'">
            Go to Unlock
        </button>
    </div>
</body>
</html>