<?php
require 'connection.php';

// Récupérer les données de la table 'tache'
try {
    $requete = "SELECT * FROM tache";
    $stmt = $conn->prepare($requete);
    $stmt->execute();
    $taches = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi des Actions de Couture</title>
    <link rel="stylesheet" href="style.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>
    <header id="task">
        <h1>Suivi des Actions de Couture</h1>
    </header>
    
    <div class="page-indication">
        <h1>Tableau de Bord</h1>
        <div id="logout">
            <a href="page.html">
                <ion-icon name="log-out-outline"></ion-icon>
            </a>
        </div>
    </div>

    <div class="statis">
        <div class="div d1">
            <span>
                Totale des taches
                <?php
                   $rowCount = $stmt->rowCount();
                   echo '<h1>' . $rowCount . '</h1>';
                ?>
            </span>
        </div>

        <div class="div d2">
            <span>
                Totale des agents
                <?php
                try {
                    $requete = "SELECT COUNT(DISTINCT mat_agent) AS agent_count FROM info_générale";
                    $stmt = $conn->prepare($requete);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo '<h1>' . $result['agent_count'] . '</h1>';
                } catch (PDOException $e) {
                    echo 'Erreur : ' . $e->getMessage();
                }
                ?>
            </span>
        </div>

        <div class="div d2">
            <span>
                Totale des lignes
                <?php
                try {
                    $requete = "SELECT COUNT(DISTINCT ligne) AS ligne_count FROM info_générale";
                    $stmt = $conn->prepare($requete);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo '<h1>' . $result['ligne_count'] . '</h1>';
                } catch (PDOException $e) {
                    echo 'Erreur : ' . $e->getMessage();
                }
                ?>
            </span>
        </div>

        <div class="div d2">
            <span>
                Totale des couturiere
                <?php
                try {
                    $requete = "SELECT COUNT(DISTINCT mat_couturiere) AS couturier_count FROM info_générale";
                    $stmt = $conn->prepare($requete);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo '<h1>' . $result['couturier_count'] . '</h1>';
                } catch (PDOException $e) {
                    echo 'Erreur : ' . $e->getMessage();
                }
                ?>
            </span>
        </div>
    </div>
    <script src="./assets/js/script.js"></script>
</body>
