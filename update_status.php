<?php
session_start();

if (isset($_POST['courrierId'])) {
    // Informations de la base de données PostgreSQL
    $hostname = "postgresql-bali.alwaysdata.net";
    $username = "bali";
    $port = "5432";
    $password = "projetbdreseau";
    $dbname = "bali_bdreseau";

    try {
        $connexion = new PDO("pgsql:host=$hostname;port=$port;dbname=$dbname", $username, $password);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Mettre à jour le statut dans la base de données
        $courrierId = $_POST['courrierId'];
        $stmt = $connexion->prepare("UPDATE notification SET statut_notification = true WHERE courrier_id = :courrierId");
        $stmt->bindParam(':courrierId', $courrierId);
        $stmt->execute();

        // Vous pouvez envoyer une réponse si nécessaire
        echo 'Statut mis à jour avec succès.';
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
} else {
    echo "Paramètre manquant.";
}
?>
