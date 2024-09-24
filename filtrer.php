<?php
session_start();
require 'connection.php';

$taches = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $critere = $_POST['filtrer'] ?? '';
    $valeur = $_POST['search'] ?? '';

    if ($conn) {
        try {
            $requete_id = '';
            switch ($critere) {
                case 'agent':
                    $requete_id = 'SELECT id FROM info_générale WHERE mat_agent LIKE :valeur';
                    break;
                case 'ligne':
                    $requete_id = 'SELECT id FROM info_générale WHERE ligne LIKE :valeur';
                    break;
                case 'operatrice':
                    $requete_id = 'SELECT id FROM info_générale WHERE mat_couturiere LIKE :valeur';
                    break;
                default:
                    throw new Exception("Critère de filtrage invalide.");
            }

            // Préparer et exécuter la requête pour obtenir l'ID
            $stmt_id = $conn->prepare($requete_id);
            $stmt_id->bindValue(':valeur', '%' . $valeur . '%', PDO::PARAM_STR);
            $stmt_id->execute();
            $id_result = $stmt_id->fetch(PDO::FETCH_ASSOC);

            if ($id_result) {
                $id_info = $id_result['id'];

                // Rechercher les tâches associées à cet ID
                $requete_taches = 'SELECT * FROM tache WHERE id_info = :id_info';
                $stmt_taches = $conn->prepare($requete_taches);
                $stmt_taches->bindValue(':id_info', $id_info, PDO::PARAM_INT);
                $stmt_taches->execute();
                $taches = $stmt_taches->fetchAll(PDO::FETCH_ASSOC);
            } else {
                echo 'Aucun résultat trouvé dans info_générale.';
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des données : " . $e->getMessage();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    } else {
        echo "Connexion à la base de données impossible.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats du Filtrage</title>
    <link rel="stylesheet" href="style.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>
    <header>
        <h1>Résultats du Filtrage des Actions de Couture</h1>
    </header>

    <main>
        <section id="taches-section">
            <h2>Tâches filtrées</h2>
            <?php if (!empty($taches)): ?>
                <form action="update_tache.php" method="POST">
                    <?php foreach ($taches as $tache): ?>
                        <div class="tache">
                    <div class="checkbox">
                        <input type="text" name="titre[]" placeholder="Nom de la Tâche" value="<?= htmlspecialchars($tache['nom']) ?>" >
                        <form id="statusForm" action="modifier1-status.php" method="POST">
                            <input type="hidden" name="origin" value="todo.php">
                            <input type="radio" class="status-radio" data-id="<?= htmlspecialchars($tache['id']) ?>" name="status[<?= htmlspecialchars($tache['id']) ?>]" value="fait" <?= $tache['status'] == 'fait' ? 'checked' : '' ?>> Fait
                            <input type="radio" class="status-radio" data-id="<?= htmlspecialchars($tache['id']) ?>" name="status[<?= htmlspecialchars($tache['id']) ?>]" value="en_cours" <?= $tache['status'] == 'en_cours' ? 'checked' : '' ?>> En cours
                            <input type="radio" class="status-radio" data-id="<?= htmlspecialchars($tache['id']) ?>" name="status[<?= htmlspecialchars($tache['id']) ?>]" value="non_fait" <?= $tache['status'] == 'non_fait' ? 'checked' : '' ?>> Non fait
                        </form>
                        <form action="delete.php" method="post" style="display:inline;">
                             <input type="hidden" name="delete-id" value="<?= htmlspecialchars($tache['id']) ?>">
                              <div class="delete-div">
                                  <button type="submit" class="delete-btn">
                                     <ion-icon name="trash-outline"></ion-icon>
                                  </button>
                             </div>
                    </form>

                    </div>
                    <div class="inputs">
                    <textarea class="commentaire" name="commentaire[]" placeholder="Commentaire" rows="3" style="width: 100%;"><?= htmlspecialchars($tache['description']) ?></textarea>
                    <?php if (!empty($tache['fichier'])): ?>
                    <a href="uploads/<?= htmlspecialchars($tache['fichier']) ?>" target="_blank">
                    <?= htmlspecialchars($tache['fichier']) ?>
                     </a>
                    <?php endif; ?>
                    </div>

                </div>
                    <?php endforeach; ?>
                </form>
            <?php else: ?>
                <p>Aucune tâche correspondante trouvée.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
