<?php
session_start();

// Inclure la configuration de la base de données
require_once 'db_config.php';

// Vérifier si le formulaire de connexion a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom_utilisateur']) && isset($_POST['mot_de_passe'])) {
    $nomUtilisateur = trim($_POST['nom_utilisateur']);
    $motDePasse = trim($_POST['mot_de_passe']);

    // Vérifier si les champs sont vides
    if (empty($nomUtilisateur) || empty($motDePasse)) {
        $erreur = "Please fill in all fields.";
    } else {
        try {
            // Préparer la requête pour récupérer l'utilisateur par nom d'utilisateur
            $stmt = $pdo->prepare("SELECT id, nom_utilisateur, mot_de_passe FROM utilisateurs WHERE nom_utilisateur = :nom_utilisateur");
            $stmt->bindParam(':nom_utilisateur', $nomUtilisateur);
            $stmt->execute();

            // Récupérer l'utilisateur
            $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

            // Vérifier si l'utilisateur existe et si le mot de passe est correct
            if ($utilisateur && password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
                // Authentification réussie, créer la session
                $_SESSION['utilisateur_id'] = $utilisateur['id'];
                $_SESSION['nom_utilisateur'] = $utilisateur['nom_utilisateur'];
                $_SESSION['est_connecte'] = true;

                // Mettre à jour la date de dernière connexion (exemple)
                $updateStmt = $pdo->prepare("UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id = :id");
                $updateStmt->bindParam(':id', $utilisateur['id']);
                $updateStmt->execute();

                // Rediriger l'utilisateur vers la page d'accueil ou une autre page sécurisée
                header("Location: index2.php"); // Redirection vers profile.php
                exit();
            } else {
                $erreur = "Incorrect username or password.";
            }
        } catch (PDOException $e) {
            $erreur = "Erreur de connexion : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="index.css">
    <style>
        .login-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 400px;
            margin: 20px auto;
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
        .form-group input[type="password"] {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 1em;
        }

        .login-button {
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1.1em;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .login-button:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            margin-top: 10px;
            text-align: center;
        }

        .signup-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
        }

        .signup-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <?php if (isset($erreur)): ?>
            <p class="error-message"><?php echo $erreur; ?></p>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label for="nom_utilisateur">Username:</label>
                <input type="text" id="nom_utilisateur" name="nom_utilisateur" value="<?php echo isset($_SESSION['tentative_nom_utilisateur']) ? htmlspecialchars($_SESSION['tentative_nom_utilisateur']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="mot_de_passe">Password:</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>
            </div>
            <button type="submit" class="login-button">Sign in</button>
        </form>
        <a href="signup.php" class="signup-link">Create an Account</a>
        <div class="forgot-password">
            <a href="/mot-de-passe-oublie">Forgot password?</a>
        </div>
        <div class="signup-link">
            New here? <a href="/signup.php">Create an account</a>
        </div>
    </div>
</body>
</html>