document.addEventListener('DOMContentLoaded', function () {
    const url = location.pathname.split('/');
    const page = url[url.length - 1];

    if (page === 'todo.php' || page === 'affiche-tache.php') {
        const ajout_btn = document.getElementById('ajout_btn');
        const taches = document.getElementById('nouvelles-taches');

        if (!taches) {
            console.error('Élément avec ID "nouvelles-taches" introuvable');
            return;
        }

        function Ajout() {
            const nouvelleTache = document.createElement('div');
            nouvelleTache.classList.add('tache');
            nouvelleTache.innerHTML = `
                <div class="checkbox">
                    <input type="text" name="titre[]" placeholder="Nom de la Tâche">
                    <input type="radio" name="status[]" value="fait"> Fait
                    <input type="radio" name="status[]" value="en_cours"> En cours
                    <input type="radio" name="status[]" value="non_fait"> Non fait
                    <div class="delete-div">
                        <button type="button" name="delete-btn" class="delete-btn">
                            <ion-icon name="trash-outline"></ion-icon>
                        </button>
                    </div>
                </div>
                <div class="inputs">
                     <textarea class="commentaire" name="commentaire[]" placeholder="Commentaire" rows="3" style="width: 100%;"></textarea>
                    <input type="file" class="video" accept="video/*" name="file[]">
                </div>
            `;
            taches.appendChild(nouvelleTache);
            remove();
        }

        if (ajout_btn) {
            ajout_btn.addEventListener('click', Ajout);
        } else {
            console.error('Élément avec ID "ajout_btn" introuvable');
        }

        function remove() {
            const delete_btns = document.querySelectorAll('.delete-btn');
            delete_btns.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    btn.closest('.tache').remove();
                });
            });
        }

        remove();
    }

    
if (page === 'filter.php') {
    const form_filtrer = document.getElementById('form_filtrer');
    const filtrer = document.getElementById('filtrer');

        filtrer.addEventListener('change', function () {
            form_filtrer.submit();
        });
    }


    function confirmerTransfert() {
        let confirmation = confirm("Êtes-vous sûr de vouloir transférer les données?");
        if (confirmation) {
            alert("Les données ont été transférées avec succès.");
            header("Location: affiche-tach.php"); // Redirection vers la page
            return true; // Autoriser la soumission du formulaire
        } else {
            return false; // Empêcher la soumission du formulaire
        }
    }

    function updatePlaceholder() {
        const select = document.getElementById('filtrer');
        const search = document.getElementById('search');
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
    
    const select = document.getElementById('filtrer');
    if (select) {
        select.addEventListener('change', updatePlaceholder);
    } else {
        console.error('Élément avec ID "filtrer" introuvable');
    }
    
    document.addEventListener("DOMContentLoaded", function() {
        const statusRadios = document.querySelectorAll(".status-radio");
    
        statusRadios.forEach(function(radio) {
            radio.addEventListener("change", function() {
              
    
                // Créer un formulaire temporaire pour soumettre les données
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'modifier-status.php';
                
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
    

    

    
});
