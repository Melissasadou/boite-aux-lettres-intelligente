<?php
//la base de données PostgreSQL
$hostname = "postgresql-bali.alwaysdata.net";
$username = "bali";
$port = "5432";
$password = "projetbdreseau";
$dbname = "bali_bdreseau";

// Tentative de connexion à la base de données
try {
    $connexion = new PDO("pgsql:host=$hostname;port=$port;dbname=$dbname", $username, $password);
    // Configurer PDO pour lever des exceptions en cas d'erreur
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    die();
}

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $email = $_POST["email"];
    $mot_de_passe = $_POST["mot_de_passe"];

    // Requête SQL préparée pour vérifier l'existence de l'utilisateur
    $requete = "SELECT * FROM utilisateur WHERE email = :email AND mot_de_passe = :mot_de_passe";
    $stmt = $connexion->prepare($requete);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':mot_de_passe', $mot_de_passe);
    $stmt->execute();

    // Vérification de l'existence de l'utilisateur
    if ($stmt->rowCount() > 0) {
        // Utilisateur trouvé, rediriger vers la page principale.php
        session_start();
        $_SESSION['utilisateur_email'] = $email;
        header("Location: principale.php");
        exit();
    } else {
        // Utilisateur non trouvé, afficher un message d'erreur
        $messageErreur = "Identifiants invalides. Veuillez réessayer.";
    }
}

// Fermer la connexion à la base de données
$connexion = null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Smart Mail</title>
    <link href="styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Crete+Round:ital@1&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="wrapper">
            <h1>Smart Mail<span class="orange">.</span></h1>
            <nav>
                <ul>
                    <li><a href="index.html"> Accueil </a></li>
                    <li><a href="connexion.php"> Se connecter </a></li>
                    <li><a href="inscription.html"> S'inscrire </a></li>
                    <li><a href="index.html#contact"> Contact </a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="login-box">
            <h1>Connexion</h1>
            <p>email : etu@cyu.fr</p>
            <p>mot de passe : A123456*</p>
            <form method="post" action="connexion.php">
                <div class="input-group">
                    <label for="email">Adresse e-mail</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="input-group">
                    <label for="mot_de_passe">Mot de passe</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                </div>
                <button type="submit" name="submit">Se connecter</button>
            </form>
            <?php
                // Afficher le message d'erreur s'il existe
                if (isset($messageErreur)) {
                    echo "<p>{$messageErreur}</p>";
                }
            ?>
            <p>Pas encore inscrit ? <a href="inscription.html">S'inscrire</a></p>
        </div>
    </div>
</body>
</html>
