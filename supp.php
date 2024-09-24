<?php
session_start(); // Démarrer la session
require "connection.php"; // Assurez-vous que la connexion est bien établie

if(isset($_POST['delete-id'])){
    $idTache = $_POST['delete-id'];

    // Préparez la requête SQL pour supprimer la tâche
    $sql = "DELETE FROM tache WHERE id = :id";
    $stmt = $conn->prepare($sql);

    try {
        // Exécutez la requête avec le paramètre fourni
        $result = $stmt->execute([':id' => $idTache]);
        
        if ($result) {
            header("Location: todo.php"); // Redirigez après la suppression
            exit(); // Arrêtez l'exécution après la redirection
        } else {
            echo "Erreur lors de la suppression de la tâche.";
        }
    } catch(PDOException $e) {
        echo "Erreur lors de la suppression : " . $e->getMessage();
    }
} else {
    echo "Aucune tâche à supprimer.";
}
?>
