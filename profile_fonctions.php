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

try {
    // Récupérer les informations de l'utilisateur
    $stmt = $pdo->prepare("SELECT nom_utilisateur, email, solde_usdt FROM utilisateurs WHERE id = :id");
    $stmt->bindParam(':id', $utilisateur_id);
    $stmt->execute();
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$utilisateur) {
        $erreur = "Utilisateur non trouvé.";
    } else {
        $nom_utilisateur = htmlspecialchars($utilisateur['nom_utilisateur']);
        $email = htmlspecialchars($utilisateur['email']);
        $solde_usdt = htmlspecialchars($utilisateur['solde_usdt']);
    }
} catch (PDOException $e) {
    $erreur = "Erreur de base de données : " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre Page</title>
    <style>
        /* Styles Généraux */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Assure que le body prend au moins toute la hauteur de la fenêtre */
        }

        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Styles de l'Icône de Profil */
        .profile-container {
            display: flex;
            justify-content: flex-end; /* Aligne à droite */
            padding-right: 20px; /* Ajoute un peu d'espace à droite */
            margin-top: 10px; /* Espace après l'en-tête */
            align-items: center; /* Centre verticalement les éléments */
            flex-direction: column; /* Place l'icône au-dessus du nom */
        }

        .profile-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #007bff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            /* Utilisation d'un caractère Unicode pour une icône de compte */
            font-size: 3.5em; /* Ajustez la taille de l'icône */
            color: #fff;
            margin-bottom: 5px; /* Ajoute un espace entre l'icône et le nom */
            margin-left: auto; /* Pousse l'icône légèrement à droite */
        }

        .profile-username {
            color: #007bff; /* Couleur du texte (exemple: bleu) */
            font-size: 0.9em;
            text-align: center;
            width: 80px; /* Empêche le nom de devenir trop large */
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            margin-left: auto; /* Aligne le nom avec l'icône */
            font-weight: bold; /* Met le texte en gras */
        }

        /* Styles des Informations de Profil (Initialement Cachées) */
        .profile-info {
            display: none;
            position: absolute;
            top: 100px; /* Ajusté pour être sous l'icône et le nom */
            right: 20px;
            background-color: #f0f8ff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            width: 250px; /* Ajustez la largeur selon le contenu */
        }

        .profile-info h2 {
            color: #007bff;
            margin-top: 0;
        }

        .profile-info p {
            margin: 5px 0;
        }

        .balance { /* Ajout de la classe pour le style du solde */
            color: green;
            font-weight: bold;
        }

        /* Afficher les Informations de Profil */
        .profile-info.show {
            display: block;
        }

        .error {
            color: red;
        }

        /* Styles du Main */
        main {
            flex: 1; /* Prend l'espace restant */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center; /* Centre verticalement */
            padding: 20px;
        }

        .main-content {
            width: 80%;
            max-width: 800px;
            text-align: center;
        }

    </style>
    <script>
        function toggleProfile() {
            const profileInfo = document.querySelector('.profile-info');
            profileInfo.classList.toggle('show');
        }
    </script>
</head>
<body>

    <div class="profile-container" onclick="toggleProfile()">
        <div class="profile-icon">
            <span style="font-family: sans-serif;">&#128100;</span>
        </div>
        <div class="profile-username"><?php echo htmlspecialchars($nom_utilisateur); ?></div>
    </div>

    <div class="profile-info">
        <h2>Your account</h2>
        <?php if (isset($erreur)): ?>
            <p class="error"><?php echo $erreur; ?></p>
        <?php else: ?>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($nom_utilisateur); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Balance:</strong> <span class="balance"><?php echo htmlspecialchars($solde_usdt); ?> USDT</span></p>
        <?php endif; ?>
    </div>

</body>
</html>