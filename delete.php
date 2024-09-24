<?php
session_start(); // Démarrer la session
require 'connection.php';

// Vérifiez si l'ID de la tâche à supprimer est présent dans la requête POST
if (isset($_POST['delete-id'])) {
    $idTache = $_POST['delete-id'];
   

    // Préparez la requête de suppression
    $requete = "DELETE FROM tache WHERE id = :id";
    $stmt = $conn->prepare($requete);

    // Exécutez la requête avec l'ID de la tâche
    try {
        $result = $stmt->execute([':id' => $idTache]);

        // Vérifiez le résultat de l'exécution
        if ($result) {
            // Redirigez vers la page des tâches après la suppression
            header("Location: affiche-tache.php"); // Assurez-vous que la redirection est correcte
            exit(); // Assurez-vous de sortir après la redirection
        } else {
            echo "Erreur lors de la suppression de la tâche.";
        }
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression : " . $e->getMessage();
    }
} else {
    echo "Aucune tâche à supprimer.";
}
?>
