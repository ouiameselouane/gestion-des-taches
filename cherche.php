<?php
session_start();
require 'connection.php';

$search_results = [];

if (isset($_POST['search']) && !empty($_POST['search'])) {
    $search_term = $_POST['search'];

    try {
        // Requête pour rechercher les tâches contenant le terme de recherche
        $requete = "SELECT * FROM tache WHERE nom LIKE :search";
        $stmt = $conn->prepare($requete);
        $search_term = "%" . $search_term . "%";
        $stmt->bindParam(':search', $search_term, PDO::PARAM_STR);
        $stmt->execute();
        $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur lors de la recherche : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de Recherche</title>
    <link rel="stylesheet" href="style.css">
     <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>
    <header>
        <h1>Résultats de Recherche</h1>
    </header>

    <main>
        <div id="task-results">
            <?php if (!empty($search_results)): ?>
                <?php foreach ($search_results as $tache): ?>
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
            <?php else: ?>
                <p>Aucune tâche correspondante trouvée.</p>
            <?php endif; ?>
        </div>
    </main>

    <script src="./assets/js/script.js"></script>
</body>
</html>
