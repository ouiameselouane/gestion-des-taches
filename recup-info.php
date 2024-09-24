<?php
session_start(); // Démarrer la session
require 'connection.php';

if (isset($_POST['Infos'])) {
    $ligne = $_POST['ligne'] ?? '';
    $mat_couturiere = $_POST['matricule'] ?? '';
    $mat_agent = $_POST['agent'] ?? '';

    // Vérification des valeurs
    if (empty($ligne) || empty($mat_couturiere) || empty($mat_agent)) {
        echo "Tous les champs doivent être remplis.";
    } else {
        // Insertion des informations générales dans la base de données
        if ($conn) {
            try {
                $requete = "INSERT INTO info_générale (ligne, mat_couturiere, mat_agent) VALUES (:ligne, :mat_couturiere, :mat_agent)";
                $stmt = $conn->prepare($requete);

                $stmt->bindParam(':ligne', $ligne, PDO::PARAM_STR);
                $stmt->bindParam(':mat_couturiere', $mat_couturiere, PDO::PARAM_STR);
                $stmt->bindParam(':mat_agent', $mat_agent, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    // Récupérer l'ID de l'information générale insérée
                    $id_info = $conn->lastInsertId();

                    // Stocker l'ID dans la session
                    $_SESSION['id_info'] = $id_info;

                    // Rediriger vers la page todo.php
                    header("Location: todo.php");
                    exit();
                } else {
                    echo "Erreur lors de l'insertion des informations générales : " . implode(", ", $stmt->errorInfo());
                }
            } catch (PDOException $e) {
                echo "Erreur lors de l'insertion : " . $e->getMessage();
            }
        } else {
            // Si pas de connexion à la base de données, générer un ID fictif
            $_SESSION['id_info'] = uniqid();
            echo "Impossible de se connecter à la base de données. ID fictif généré : {$_SESSION['id_info']}.";
            header("Location: todo.php");
            exit();
        }
    }
} else {
    echo "Formulaire non soumis.";
}
?>
