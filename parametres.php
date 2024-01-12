<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Smart Mail</title>
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
                    <li><a href="principale.php"> Mon Profil </a></li>
                    <li><a href="index.html#contact"> Contact </a></li>
                </ul>
            </nav>
        </div>
    </header>

    <?php
    session_start();

    // Vérificatiob si l'utilisateur est connecté
    if (!isset($_SESSION['utilisateur_email'])) {
        header("Location: connexion.php");
        exit();
    }

    // Informations de la base de données PostgreSQL du projet
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
        die("La connexion à la base de données a échoué : " . $e->getMessage());
    }

    // Récupérer les informations de l'utilisateur depuis la base de données
    $utilisateur_email = $_SESSION['utilisateur_email'];
    $requete = "SELECT nom, prenom, email, telephone FROM utilisateur WHERE email = :utilisateur_email";
    $stmt = $connexion->prepare($requete);
    $stmt->bindParam(':utilisateur_email', $utilisateur_email);
    $stmt->execute();

    // Vérifier si l'utilisateur existe
    if ($stmt->rowCount() > 0) {
        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

        // Traitement du formulaire de mise à jour
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Mise à jour du nom
            if (isset($_POST['update_nom'])) {
                $new_nom = $_POST['new_nom'];
                $requete = "UPDATE utilisateur SET nom = :new_nom WHERE email = :utilisateur_email";
                $stmt = $connexion->prepare($requete);
                $stmt->bindParam(':new_nom', $new_nom);
                $stmt->bindParam(':utilisateur_email', $utilisateur_email);
                $stmt->execute();

                // Mettre à jour la variable de session si le nom a été modifié
                if ($stmt->rowCount() > 0) {
                    $_SESSION['utilisateur_nom'] = $new_nom;
                    $nom_success_message = "Nom mis à jour avec succès.";
                } else {
                    $nom_error_message = "Erreur lors de la mise à jour du nom.";
                }
            }

            // Mise à jour du prénom
            if (isset($_POST['update_prenom'])) {
                $new_prenom = $_POST['new_prenom'];
                $requete = "UPDATE utilisateur SET prenom = :new_prenom WHERE email = :utilisateur_email";
                $stmt = $connexion->prepare($requete);
                $stmt->bindParam(':new_prenom', $new_prenom);
                $stmt->bindParam(':utilisateur_email', $utilisateur_email);
                $stmt->execute();

                // Mettre à jour la variable de session si le prénom a été modifié
                if ($stmt->rowCount() > 0) {
                    $_SESSION['utilisateur_prenom'] = $new_prenom;
                    $prenom_success_message = "Prénom mis à jour avec succès.";
                } else {
                    $prenom_error_message = "Erreur lors de la mise à jour du prénom.";
                }
            }

            // Mise à jour du téléphone
            if (isset($_POST['update_telephone'])) {
                $new_telephone = $_POST['new_telephone'];
                $requete = "UPDATE utilisateur SET telephone = :new_telephone WHERE email = :utilisateur_email";
                $stmt = $connexion->prepare($requete);
                $stmt->bindParam(':new_telephone', $new_telephone);
                $stmt->bindParam(':utilisateur_email', $utilisateur_email);
                $stmt->execute();

                // Mettre à jour la variable de session si le téléphone a été modifié
                if ($stmt->rowCount() > 0) {
                    $_SESSION['utilisateur_telephone'] = $new_telephone;
                    $telephone_success_message = "Téléphone mis à jour avec succès.";
                } else {
                    $telephone_error_message = "Erreur lors de la mise à jour du téléphone.";
                }
            }

            // Mise à jour de l'email
            if (isset($_POST['update_email'])) {
                $new_email = $_POST['new_email'];
                $requete = "UPDATE utilisateur SET email = :new_email WHERE email = :utilisateur_email";
                $stmt = $connexion->prepare($requete);
                $stmt->bindParam(':new_email', $new_email);
                $stmt->bindParam(':utilisateur_email', $utilisateur_email);
                $stmt->execute();

                // Mettre à jour la variable de session si l'email a été modifié
                if ($stmt->rowCount() > 0) {
                    $_SESSION['utilisateur_email'] = $new_email;
                    $email_success_message = "Email mis à jour avec succès.";
                } else {
                    $email_error_message = "Erreur lors de la mise à jour de l'email.";
                }
            }

            // Mise à jour du mot de passe
            if (isset($_POST['update_password'])) {
                $new_password = $_POST['new_password'];
                $requete = "UPDATE utilisateur SET mot_de_passe = :new_password WHERE email = :utilisateur_email";
                $stmt = $connexion->prepare($requete);
                $stmt->bindParam(':new_password', $new_password);
                $stmt->bindParam(':utilisateur_email', $utilisateur_email);
                $stmt->execute();

                // Afficher un message de succès ou d'erreur
                if ($stmt->rowCount() > 0) {
                    $password_success_message = "Mot de passe mis à jour avec succès.";
                } else {
                    $password_error_message = "Erreur lors de la mise à jour du mot de passe.";
                }
            }
        }
        ?>
        <div class="container3">
            <div class="signin-box">
                <h1>Informations personnelles</h1>
                <h4>Nom : <?php echo $utilisateur['nom']; ?></h4>
                <h4>Prénom : <?php echo $utilisateur['prenom']; ?></h4>
                <h4>Email : <?php echo $utilisateur['email']; ?></h4>
                <h4>Téléphone : <?php echo $utilisateur['telephone']; ?></h4>

                <!-- Affichage des messages de succès ou d'erreur -->
                <?php if (isset($nom_success_message)): ?>
                    <p class="success-message"><?php echo $nom_success_message; ?></p>
                <?php elseif (isset($nom_error_message)): ?>
                    <p class="error-message"><?php echo $nom_error_message; ?></p>
                <?php endif; ?>

                <?php if (isset($prenom_success_message)): ?>
                    <p class="success-message"><?php echo $prenom_success_message; ?></p>
                <?php elseif (isset($prenom_error_message)): ?>
                    <p class="error-message"><?php echo $prenom_error_message; ?></p>
                <?php endif; ?>

                <?php if (isset($telephone_success_message)): ?>
                    <p class="success-message"><?php echo $telephone_success_message; ?></p>
                <?php elseif (isset($telephone_error_message)): ?>
                    <p class="error-message"><?php echo $telephone_error_message; ?></p>
                <?php endif; ?>

                <?php if (isset($email_success_message)): ?>
                    <p class="success-message"><?php echo $email_success_message; ?></p>
                <?php elseif (isset($email_error_message)): ?>
                    <p class="error-message"><?php echo $email_error_message; ?></p>
                <?php endif; ?>

                <?php if (isset($password_success_message)): ?>
                    <p class="success-message"><?php echo $password_success_message; ?></p>
                <?php elseif (isset($password_error_message)): ?>
                    <p class="error-message"><?php echo $password_error_message; ?></p>
                <?php endif; ?>

                <!-- Formulaire de mise à jour des informations -->
                <h2>Mettre à jour les informations</h2>

                <form action="parametres.php" method="post">
                    <div class="half-width">
                        <label for="new_nom">Nouveau Nom:</label>
                        <input type="text" id="new_nom" name="new_nom" required>
                        <button type="submit" name="update_nom" class="button-3">Modifier Nom</button>
                    </div>
                </form>

                <form action="parametres.php" method="post">
                    <div class="half-width">
                        <label for="new_prenom">Nouveau Prénom:</label>
                        <input type="text" id="new_prenom" name="new_prenom" required>
                        <button type="submit" name="update_prenom" class="button-3">Modifier Prénom</button>
                    </div>
                </form>

                <form action="parametres.php" method="post">
                    <div class="half-width">
                        <label for="new_telephone">Nouveau Téléphone:</label>
                        <input type="tel" id="new_telephone" name="new_telephone" required>
                        <button type="submit" name="update_telephone" class="button-3">Modifier Téléphone</button>
                    </div>
                </form>

                <form action="parametres.php" method="post">
                    <div class="half-width">
                        <label for="new_email">Nouvel Email:</label>
                        <input type="email" id="new_email" name="new_email" required>
                        <button type="submit" name="update_email" class="button-3">Modifier Email</button>
                    </div>
                </form>

                <form action="parametres.php" method="post">
                    <div class="half-width">
                        <label for="new_password">Nouveau Mot de passe:</label>
                        <input type="password" id="new_password" name="new_password" required>
                        <button type="submit" name="update_password" class="button-3">Modifier Mot de passe</button>
                    </div>
                </form>
            </div>
        </div>
        <?php
    } else {
        echo "Aucun utilisateur trouvé.";
    }

    // Fermer la connexion à la base de données
    $connexion = null;
    ?>
</body>
</html>
