<?php
require "connection.php";
session_start(); // Démarrer la session

if (isset($_POST['taches'])) {
    $id_info = $_POST['id_info'];
    $noms = $_POST['titre'];
    $statuses = isset($_POST['status']) ? $_POST['status'] : [];
    $descriptions = $_POST['commentaire'];
    $fichiers = $_FILES['file'];

    // Vérification de l'ID d'information générale
    if (empty($id_info)) {
        echo "ID d'information générale invalide.";
        exit();
    }

    // Vérifier que les tableaux ont la même longueur
    $countNoms = count($noms);
    $countDescriptions = count($descriptions);
    $countFiles = count($fichiers['name']);

    if ($countNoms !== $countDescriptions || $countNoms !== $countFiles) {
        echo "Erreur : Les données du formulaire sont incohérentes.";
        exit();
    }

    // Traiter les fichiers
    $file_names = [];
    $target_dir = "uploads/"; // Assurez-vous que ce répertoire existe et est accessible en écriture

    foreach ($fichiers['name'] as $index => $file_name) {
        if (!empty($file_name)) {
            $target_file = $target_dir . basename($file_name);

            // Vérifiez que le fichier n'existe pas déjà
            if (file_exists($target_file)) {
                echo "Le fichier $file_name existe déjà.<br>";
                $file_names[$index] = basename($file_name);
                continue;
            }

            // Vérifiez la taille du fichier (par exemple, 10 Mo maximum)
            if ($fichiers['size'][$index] > 10 * 1024 * 1024) {
                echo "Le fichier $file_name est trop volumineux.<br>";
                $file_names[$index] = ''; // Utilisez une chaîne vide en cas d'échec
                continue;
            }

            // Déplacez le fichier vers le répertoire de destination
            if (move_uploaded_file($fichiers['tmp_name'][$index], $target_file)) {
                $file_names[$index] = basename($file_name);
            } else {
                echo "Erreur lors du téléchargement du fichier $file_name.<br>";
                $file_names[$index] = ''; // Utilisez une chaîne vide en cas d'échec
            }
        } else {
            $file_names[$index] = ''; // Utilisez une chaîne vide si aucun fichier n'est fourni
        }
    }

    // Insérer les tâches dans la base de données
    $insertion_reussie = true; // Indicateur de succès des insertions
    for ($i = 0; $i < $countNoms; $i++) {
        try {
            $nom = $noms[$i];
            $status = isset($statuses[$i]) ? $statuses[$i] : 'non_fait'; // Défaut à 'non_fait' si aucune valeur n'est cochée
            $description = $descriptions[$i];
            $fichier = isset($file_names[$i]) ? $file_names[$i] : '';

            // Insertion des tâches dans la base de données
            $requete = "INSERT INTO tache (id_info, nom, status, description, fichier) VALUES (:id_info, :nom, :status, :description, :fichier)";
            $stmt = $conn->prepare($requete);

            $stmt->bindParam(':id_info', $id_info, PDO::PARAM_INT);
            $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':fichier', $fichier, PDO::PARAM_STR);

            if (!$stmt->execute()) {
                echo "Erreur d'insertion de la tâche {$i}: " . implode(", ", $stmt->errorInfo()) . "<br>";
                $insertion_reussie = false;
            }
        } catch (PDOException $e) {
            echo "Erreur pour la tâche {$i} : " . $e->getMessage() . "<br>";
            $insertion_reussie = false;
        }
    }

    // Redirection après toutes les insertions
    if ($insertion_reussie) {
        header("Location: todo.php");
        exit(); // Assurez-vous d'utiliser exit() après header()
    } else {
        echo "<h2>Des erreurs ont eu lieu lors de l'insertion des tâches.</h2>";
    }

} else {
    echo "<h2>Formulaire non soumis</h2>";
}
?>
