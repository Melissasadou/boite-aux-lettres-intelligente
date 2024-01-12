<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boîte de réception - Mon Mail</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Crete+Round:ital@1&display=swap" rel="stylesheet">
    <script>
        function markAsRead(messageId) {
            const message = document.getElementById(messageId);
            if (message) {
                message.classList.add('read');
            }
        }

        function filterMessages() {
            const searchInput = document.getElementById("searchInput").value.toLowerCase();
            const messages = document.querySelectorAll('.message');
            messages.forEach(function (message) {
                const subject = message.querySelector('.subject').textContent.toLowerCase();
                if (subject.includes(searchInput)) {
                    message.style.display = 'table-row'; // Utilisez 'table-row' pour afficher la ligne
                } else {
                    message.style.display = 'none';
                }
            });
        }
    </script>
</head>

<body>
    <header>
        <div class="wrapper">
            <h1>Smart Mail<span class="orange">.</span></h1>
            <nav>
                <ul>
                    <li><a href="index.html"> Accueil </a></li>
                    <li><a href="parametres.php"> Paramètres </a></li>
                    <li><a href="index.html#contact"> Contact </a></li>
                </ul>
            </nav>
        </div>
    </header>
    <?php
    session_start();

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['utilisateur_email'])) {
        header("Location: connexion.php");
        exit();
    }

    // Informations de la base de données PostgreSQL
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
    $requete = "SELECT user_id, nom, prenom, email, telephone FROM utilisateur WHERE email = :utilisateur_email";
    $stmt = $connexion->prepare($requete);
    $stmt->bindParam(':utilisateur_email', $utilisateur_email);
    $stmt->execute();

    // Vérifier si l'utilisateur existe
    if ($stmt->rowCount() > 0) {
        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
        $utilisateur_id = $utilisateur['user_id'];
    }
    ?>
    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Rechercher par sujet">
        <button onclick="filterMessages()">Rechercher</button>
    </div>

    <div class="container">
        <div class="message-table">
            <table>
                <thead>
                    <tr>
                        <th>Date Notification</th>
                        <th>Sujet</th>
                        <th>Date Arrivée</th>
                        <th>Poids Courrier</th>
                        <th>Type Notification</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Récupérer les notifications de l'utilisateur
                    $requete_notifications = "SELECT s.date_notification, s.type_notification, s.titre, s.date_arrivee, s.poid_courrier_gramme
                    FROM (
                        SELECT n.date_notification, n.type_notification, c.titre, c.date_arrivee, c.poid_courrier_gramme
                        FROM notification n
                        JOIN courrier c ON n.courrier_id = c.courrier_id
                        WHERE n.user_id = :utilisateur_id
                        ORDER BY n.date_notification DESC
                    ) AS s;
                    ";
                    $stmt_notifications = $connexion->prepare($requete_notifications);
                    $stmt_notifications->bindParam(':utilisateur_id', $utilisateur_id);
                    $stmt_notifications->execute();

                    // Vérifier s'il y a des notifications
                    if ($stmt_notifications->rowCount() > 0) {
                        // Afficher les notifications dans le tableau
                        while ($notification = $stmt_notifications->fetch(PDO::FETCH_ASSOC)) {
                            echo '<tr class="message">';
                            echo '<td>' . htmlspecialchars($notification['date_notification']) . '</td>';
                            echo '<td class="subject">' . htmlspecialchars($notification['titre']) . '</td>';
                            echo '<td>' . htmlspecialchars($notification['date_arrivee']) . '</td>';
                            echo '<td>' . htmlspecialchars($notification['poid_courrier_gramme']) . '</td>';
                            echo '<td>';
                            if ($notification['type_notification'] == 0) {
                                echo 'Mise à jour';
                            } elseif ($notification['type_notification'] == 1) {
                                echo 'Arrivée courrier';
                            }
                            echo '</td>';
                            echo '<td><button class="view-button" onclick="markAsRead(\'message\')">Vu</button></td>';
                            echo '</tr>';
                        }
                    } else {
                        // S'il n'y a pas de notifications, vous pouvez afficher un message ou laisser le tableau vide
                        // echo '<tr><td colspan="5">Aucune notification pour le moment.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
