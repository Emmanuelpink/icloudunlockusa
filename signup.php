<?php
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom_utilisateur']) && isset($_POST['email']) && isset($_POST['mot_de_passe']) && isset($_POST['confirmer_mot_de_passe'])) {
    $nomUtilisateur = trim($_POST['nom_utilisateur']);
    $email = trim($_POST['email']);
    $motDePasse = trim($_POST['mot_de_passe']);
    $confirmerMotDePasse = trim($_POST['confirmer_mot_de_passe']); // Récupérer la confirmation du mot de passe

    // Validation des champs (ajoutez des validations plus robustes)
    if (empty($nomUtilisateur) || empty($email) || empty($motDePasse) || empty($confirmerMotDePasse)) {
        $erreur = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Adresse e-mail invalide.";
    } elseif (strlen($motDePasse) < 6) {
        $erreur = "Password must contain at least 6 characters.";
    } elseif ($motDePasse !== $confirmerMotDePasse) { // Vérifier si les mots de passe correspondent
        $erreur = "Passwords do not match.";
    } else {
        try {
            // Vérifier si le nom d'utilisateur ou l'e-mail existe déjà
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE nom_utilisateur = :nom_utilisateur OR email = :email");
            $stmt->bindParam(':nom_utilisateur', $nomUtilisateur);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->fetchColumn() > 0) {
                $erreur = "This username or email address is already in use.";
            } else {
                // Hacher le mot de passe
                $motDePasseHache = password_hash($motDePasse, PASSWORD_DEFAULT);

                // Préparer la requête d'insertion
                $insertStmt = $pdo->prepare("INSERT INTO utilisateurs (nom_utilisateur, email, mot_de_passe) VALUES (:nom_utilisateur, :email, :mot_de_passe)");
                $insertStmt->bindParam(':nom_utilisateur', $nomUtilisateur);
                $insertStmt->bindParam(':email', $email);
                $insertStmt->bindParam(':mot_de_passe', $motDePasseHache);
                $insertStmt->execute();

                $succes = "Registration successful! You can now log in.";
                // Rediriger vers la page de connexion après un délai
                header("refresh:3;url=login.php"); // Redirection après 3 secondes
                exit(); // Assurez-vous d'appeler exit() après header()
            }
        } catch (PDOException $e) {
            $erreur = "Sign in error : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create an Account</title>
    <style>
        body {
    font-family: sans-serif;
    background-color: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
}

.signup-container {
    background-color: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 400px;
}

.signup-container h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: #555;
    font-size: 0.9em;
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="password"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 1em;
}

.form-group button {
    background-color: #5cb85c;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    width: 100%;
    font-size: 1em;
}

.form-group button:hover {
    background-color: #4cae4c;
}

.signup-link {
    text-align: center;
    margin-top: 15px;
    font-size: 0.9em;
    color: #777;
}

.signup-link a {
    color: #007bff;
    text-decoration: none;
}

.signup-link a:hover {
    text-decoration: underline;
}

.error-message {
    color: red;
    font-size: 0.8em;
    margin-top: 5px;
    text-align: center; /* Added for alignment */
}

.success-message {
    color: green;
    font-size: 0.8em;
    margin-top: 5px;
    text-align: center; /* Added for alignment */
}
</style>
</head>
<body>
    <div class="signup-container">
        <h2>Registration</h2>
        <?php if (isset($erreur)): ?>
            <p class="error-message"><?php echo $erreur; ?></p>
        <?php endif; ?>
        <?php if (isset($succes)): ?>
            <p class="success-message"><?php echo $succes; ?></p>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label for="nom_utilisateur">Username:</label>
                <input type="text" id="nom_utilisateur" name="nom_utilisateur" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="mot_de_passe">Password:</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>
            </div>
            <div class="form-group">
                <label for="confirmer_mot_de_passe">Confirm Password:</label>
                <input type="password" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe" required>
            </div>
            <div class="form-group">
                <button type="submit">Sign up</button>
            </div>
        </form>
        <p class="signup-link">Already have an account? <a href="login.php">Log in</a></p>
    </div>
</body>
</html>