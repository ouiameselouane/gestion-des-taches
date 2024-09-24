<?php
session_start(); // Démarrer la session
require 'connection.php';

$taches = [];
$id_info = isset($_POST['id_info']) ? $_POST['id_info'] : (isset($_SESSION['id_info']) ? $_SESSION['id_info'] : 0);
$_SESSION['id_info'] = $id_info;
if ($conn) {
    // Récupérer toutes les tâches existantes
    try {
        $requete = "SELECT * FROM tache";
        $stmt = $conn->prepare($requete);
        $stmt->execute();
        $taches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération des tâches : " . $e->getMessage();
    }
} else {
    echo "Connexion à la base de données impossible. Affichage des tâches désactivé.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="style.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <style>
        .video-link {
            color: blue;
            text-decoration: underline;
            cursor: pointer;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const statusRadios = document.querySelectorAll(".status-radio");

            statusRadios.forEach(function(radio) {
                radio.addEventListener("change", function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'modifier1-status.php';

                    const hiddenInputId = document.createElement('input');
                    hiddenInputId.type = 'hidden';
                    hiddenInputId.name = 'id';
                    hiddenInputId.value = this.getAttribute('data-id');
                    form.appendChild(hiddenInputId);

                    const hiddenInputStatus = document.createElement('input');
                    hiddenInputStatus.type = 'hidden';
                    hiddenInputStatus.name = 'status';
                    hiddenInputStatus.value = this.value;
                    form.appendChild(hiddenInputStatus);

                    document.body.appendChild(form);
                    form.submit();
                });
            });
        });
    </script>
</head>
<body>
    <header>
        <h1>Suivi des Actions de Couture</h1>
    </header>

    <div class="nav-container">
        <nav>
            <div class="liens">
                <a href="formule.html">Infos Générales</a>
                <form action="todo.php" method="post" style="display:inline;">
                    <input type="hidden" name="id_info" value="<?= htmlspecialchars($id_info) ?>">
                    <a type="submit">To-Do List</a>
                </form>
            </div>
            <div class="search-container">
                <form id="search-form" action="cherche.php" method="POST">
                    <input type="text" id="search" name="search" placeholder="Rechercher une tâche" required>
                </form>
            </div>
            
            <button type="button" id="ajout_btn">Ajouter Tâche</button>
            <div id="logout">
                <a href="page.html">
                    <ion-icon name="log-out-outline"></ion-icon>
                </a>
            </div>
        </nav>
    </div>

    <main>
        <section id="taches-section">
        <form action="recup-tache.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_info" value="<?= htmlspecialchars($id_info) ?>">

            <div id="nouvelles-taches">
                <h2 style="text-decoration: underline;">Ajouter de nouvelles tâches:</h2>
            </div>

            <button type="submit" id="submit-btn" name="taches">Soumettre</button>
        </form>

        <div id="taches">
        <h2 style="text-decoration: underline;">Tâches existantes:</h2>

            <?php if (!empty($taches)): ?>
                <?php foreach ($taches as $tache): ?>
                    <div class="tache">
                        <div class="checkbox">
                            <input type="text" name="titre[]" placeholder="Nom de la Tâche" value="<?= htmlspecialchars($tache['nom']) ?>">

                            <form id="statusForm" action="modifier1-status.php" method="POST">
                                <input type="hidden" name="origin" value="todo.php">
                                <input type="radio" class="status-radio" data-id="<?= htmlspecialchars($tache['id']) ?>" name="status[<?= htmlspecialchars($tache['id']) ?>]" value="fait" <?= $tache['status'] == 'fait' ? 'checked' : '' ?>> Fait
                                <input type="radio" class="status-radio" data-id="<?= htmlspecialchars($tache['id']) ?>" name="status[<?= htmlspecialchars($tache['id']) ?>]" value="en_cours" <?= $tache['status'] == 'en_cours' ? 'checked' : '' ?>> En cours
                                <input type="radio" class="status-radio" data-id="<?= htmlspecialchars($tache['id']) ?>" name="status[<?= htmlspecialchars($tache['id']) ?>]" value="non_fait" <?= $tache['status'] == 'non_fait' ? 'checked' : '' ?>> Non fait
                            </form>

                            <form action="supp.php" method="post" style="display:inline;">
                                <input type="hidden" name="delete-id" value="<?= htmlspecialchars($tache['id']) ?>">
                                <div class="delete-div">
                                    <button type="submit" class="delete-id">
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
                <p>Aucune tâche trouvée pour cet ID d'information générale.</p>
            <?php endif; ?>
        </div>
       
        </section>
    </main>
    
    <script src="./assets/js/script.js"></script>
</body>
</html>