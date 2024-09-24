<?php
require 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Vérifier si l'ID de la tâche et le statut sont présents
    if (isset($_POST['id']) && isset($_POST['status'])) {
        $taskId = $_POST['id'];
        $newStatus = $_POST['status'];

        if (is_array($newStatus)) {
            $newStatus = reset($newStatus);
        }

        $currentDateTime = date('Y-m-d H:i:s');

        try {
            $sql = "UPDATE tache SET status = :status";

            if ($newStatus === 'en_cours') {
                $sql .= ", date_debut = :date_debut";
            }

            if ($newStatus === 'fait') {
                $sql .= ", date_fin = :date_fin";
            }

            $sql .= " WHERE id = :id";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':status', $newStatus);
            $stmt->bindParam(':id', $taskId);

            if ($newStatus === 'en_cours') {
                $stmt->bindParam(':date_debut', $currentDateTime);
            }
            if ($newStatus === 'fait') {
                $stmt->bindParam(':date_fin', $currentDateTime);
            }

            $result = $stmt->execute();
            if ($result) {
                    header("Location:affiche-tache.php");
                    
            } else {
                echo "Échec de la mise à jour du statut";
                print_r($stmt->errorInfo());
            }
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    } else {
        echo "Aucun statut ou ID de tâche trouvé.";
    }
} else {
    echo "Requête invalide.";
}
?>
