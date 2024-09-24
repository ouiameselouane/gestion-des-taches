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
        /* Style pour le lien du fichier vidéo */
        .video-link {
            color: blue;
            text-decoration: underline;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <h1>Suivi des Actions de Couture</h1>
    </header>

    <div class="nav-container">
        <nav>
            <div class="search-container">
                <form id="search-form" action="cherche.php" method="POST">
                    <input type="text" id="search" name="search" placeholder="Rechercher une tâche" required>
                </form>
            </div>

            <div class="filter-box">
                <form action="filtrer.php" method="POST" id="form_filtrer">
                    <select name="filtrer" id="filtrer" required>
                        <option value="" selected>Filtrer par:</option>
                        <option value="agent">Par agent de méthode</option>
                        <option value="ligne">Par ligne de couture</option>
                        <option value="operatrice">Par opératrice de couture</option>
                    </select>
                </form>
            </div>

            <form action="transfert.php" method="post" id="transfert">
                <button type="submit" onclick="return confirmerTransfert();">Transférer Toutes les Données</button>
            </form>

            <div id="logout">
                <a href="page.html">
                    <ion-icon name="log-out-outline"></ion-icon>
                </a>
            </div>
        </nav>
    </div>

    <main>
        <section id="taches-section">
            <!-- Section pour les tâches existantes -->
            <div id="taches">
            <h2 style="text-decoration: underline;">Tâches existantes:</h2>
                <?php if (!empty($taches)): ?>
                    <?php foreach ($taches as $tache): ?>
                        <div class="tache">
                            <div class="checkbox">
                                <input type="text" name="titre[]" placeholder="Nom de la Tâche" value="<?= htmlspecialchars($tache['nom']) ?>" >
                                 <!-- Boutons radio pour le statut -->
                                    <form id="statusForm" action="modifier2-status.php" method="POST">
                                    <input type="hidden" name="origin" value="affiche-tache.php">
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
                    <p>Aucune tâche trouvée pour cet ID d'information générale.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
    
    <script>
        function confirmerTransfert() {
            let confirmation = confirm("Êtes-vous sûr de vouloir transférer les données?");
            if (confirmation) {
                alert("Les données ont été transférées avec succès.");
                return true; // Autoriser la soumission du formulaire
            } else {
                return false; // Empêcher la soumission du formulaire
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const select = document.getElementById('filtrer');
            const search = document.getElementById('search');

            // Fonction pour mettre à jour le placeholder en fonction de la sélection
            function updatePlaceholder() {
                let placeholderText = "Rechercher une tâche";

                switch (select.value) {
                    case 'agent':
                        placeholderText += " par agent de méthode";
                        break;
                    case 'ligne':
                        placeholderText += " par ligne de couture";
                        break;
                    case 'operatrice':
                        placeholderText += " par opératrice de couture";
                        break;
                    default:
                        placeholderText = "Rechercher une tâche";
                }

                if (search) {
                    search.placeholder = placeholderText;
                }
            }

            // Événement pour changer le placeholder lorsque l'utilisateur sélectionne un filtre
            if (select) {
                select.addEventListener('change', updatePlaceholder);
                updatePlaceholder(); // Initialisation du placeholder
            } else {
                console.error('Élément avec ID "filtrer" introuvable');
            }
// Écoute l'événement 'keypress' sur l'input de recherche
document.getElementById('search').addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
        event.preventDefault(); // Empêche le comportement par défaut du formulaire
        const filterValue = document.getElementById('filtrer').value;

        if (filterValue !== "") {
            // Ajoute la valeur de recherche au formulaire de filtrage
            document.getElementById('form_filtrer').appendChild(createHiddenInput('search', this.value));
            document.getElementById('form_filtrer').submit(); // Soumet le formulaire de filtrage
        } else {
            document.getElementById('search-form').submit(); // Soumet le formulaire de recherche simple
        }
    }
});

// Fonction pour créer un input caché et l'ajouter au formulaire
function createHiddenInput(name, value) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    return input;
}

        });



        document.addEventListener("DOMContentLoaded", function() {
    const statusRadios = document.querySelectorAll(".status-radio");

    statusRadios.forEach(function(radio) {
        radio.addEventListener("change", function() {
          

            // Créer un formulaire temporaire pour soumettre les données
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'modifier2-status.php';
            
            // Ajouter l'input caché pour l'ID de la tâche
            const hiddenInputId = document.createElement('input');
            hiddenInputId.type = 'hidden';
            hiddenInputId.name = 'id';
            hiddenInputId.value = this.getAttribute('data-id');
            form.appendChild(hiddenInputId);
            
            // Ajouter l'input caché pour le nouveau statut
            const hiddenInputStatus = document.createElement('input');
            hiddenInputStatus.type = 'hidden';
            hiddenInputStatus.name = 'status';
            hiddenInputStatus.value = this.value;
            form.appendChild(hiddenInputStatus);
            
            // Ajouter le formulaire au document et le soumettre
            document.body.appendChild(form);
            form.submit();
        });
    });
});

    </script>
</body>
</html>
