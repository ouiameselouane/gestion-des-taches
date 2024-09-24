<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require "connection.php"; // Inclure votre fichier de connexion

    try {
        // Récupérer toutes les informations générales
        $stmt_info = $conn->prepare("SELECT * FROM info_générale");
        $stmt_info->execute();
        $infos_generales = $stmt_info->fetchAll(PDO::FETCH_ASSOC);

        // Récupérer toutes les tâches
        $stmt_taches = $conn->prepare("SELECT * FROM tache");
        $stmt_taches->execute();
        $taches = $stmt_taches->fetchAll(PDO::FETCH_ASSOC);

        // Créer un tableau associatif pour lier les informations générales aux tâches
        $infos_generales_map = [];
        foreach ($infos_generales as $info_generale) {
            $infos_generales_map[$info_generale['id']] = $info_generale;
        }

        // Préparer les données en format texte avec les nouvelles colonnes
        $data = "ID\tID Info\tLigne\tCouturier\tAgent\tNom\tDescription\tStatus\tVidéo\tDate Début\tDate Fin\n";
        $data .= str_repeat('-', 120) . "\n"; // Ligne de séparation plus longue pour s'adapter aux nouvelles colonnes

        foreach ($taches as $tache) {
            $id_info = $tache['id_info'];
            if (isset($infos_generales_map[$id_info])) {
                $info_generale = $infos_generales_map[$id_info];
                $data .= implode("\t", [
                    $tache['id'],
                    $tache['id_info'],
                    $info_generale['ligne'] ?? 'N/A',
                    $info_generale['mat_couturiere'] ?? 'N/A',
                    $info_generale['mat_agent'] ?? 'N/A',
                    $tache['nom'] ?? 'N/A',
                    $tache['description'] ?? 'N/A',
                    $tache['status'] ?? 'N/A',
                    $tache['fichier'] ?? 'N/A',
                    $tache['date_debut'] ?? 'N/A', // Ajouter date_debut
                    $tache['date_fin'] ?? 'N/A'    // Ajouter date_fin
                ]) . "\n";
            } else {
                // Si aucune info générale correspondante n'est trouvée, afficher 'N/A' pour ces champs
                $data .= implode("\t", [
                    $tache['id'],
                    $tache['id_info'],
                    'N/A',
                    'N/A',
                    'N/A',
                    $tache['nom'] ?? 'N/A',
                    $tache['description'] ?? 'N/A',
                    $tache['status'] ?? 'N/A',
                    $tache['fichier'] ?? 'N/A',
                    $tache['date_debut'] ?? 'N/A', // Ajouter date_debut
                    $tache['date_fin'] ?? 'N/A'    // Ajouter date_fin
                ]) . "\n";
            }
        }

        // Enregistrer les données dans un fichier texte
        $filename = __DIR__ . "/infos_taches.txt"; // Utilisation du chemin absolu
        if (file_put_contents($filename, $data) === false) {
            echo "Erreur lors de l'écriture du fichier.";
        } else {
            echo "Les données ont été transférées avec succès dans $filename.";
            header("location:affiche-tache.php");
        }

    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
    }
}
?>
