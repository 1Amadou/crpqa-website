document.addEventListener('DOMContentLoaded', function () {
    // S'assurer que nous sommes sur la bonne page (celle avec la table des inscriptions)
    const bulkActionsForm = document.getElementById('bulkActionsForm');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const registrationCheckboxes = document.querySelectorAll('.registration-checkbox');

    if (bulkActionsForm && selectAllCheckbox && registrationCheckboxes.length > 0) {
        // Gérer la case "Tout sélectionner"
        selectAllCheckbox.addEventListener('change', function () {
            registrationCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });

        // Si une case individuelle est décochée, décocher "Tout sélectionner"
        registrationCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                if (!this.checked) {
                    selectAllCheckbox.checked = false;
                } else {
                    // Vérifier si toutes les autres sont cochées pour cocher "Tout sélectionner"
                    let allChecked = true;
                    registrationCheckboxes.forEach(cb => {
                        if (!cb.checked) {
                            allChecked = false;
                        }
                    });
                    selectAllCheckbox.checked = allChecked;
                }
            });
        });

        // Gérer la soumission du formulaire des actions groupées
        bulkActionsForm.addEventListener('submit', function (event) {
            const bulkActionSelect = document.getElementById('bulk_action');
            const selectedAction = bulkActionSelect ? bulkActionSelect.value : '';
            const selectedRegistrations = Array.from(registrationCheckboxes)
                                              .filter(checkbox => checkbox.checked)
                                              .map(checkbox => checkbox.value);

            if (selectedAction === '') {
                event.preventDefault(); // Empêcher la soumission du formulaire
                alert('Veuillez sélectionner une action groupée à appliquer.');
                return;
            }

            if (selectedRegistrations.length === 0) {
                event.preventDefault(); // Empêcher la soumission du formulaire
                alert('Veuillez sélectionner au moins une inscription pour appliquer une action groupée.');
                return;
            }

            // Confirmation pour les actions destructives
            if (selectedAction === 'delete') {
                if (!confirm(`Êtes-vous sûr de vouloir supprimer les ${selectedRegistrations.length} inscription(s) sélectionnée(s) ? Cette action est irréversible.`)) {
                    event.preventDefault(); // Empêcher la soumission si l'utilisateur annule
                }
            } else if (selectedAction === 'approve' || selectedAction === 'reject') {
                 if (!confirm(`Êtes-vous sûr de vouloir ${selectedAction === 'approve' ? 'approuver' : 'rejeter'} les ${selectedRegistrations.length} inscription(s) sélectionnée(s) ?`)) {
                    event.preventDefault();
                }
            }
            // Pour les autres actions, on peut soumettre directement ou ajouter d'autres confirmations.
            // Le formulaire sera soumis à route('admin.event-registrations.bulk-actions')
            // avec les champs 'bulk_action' et 'selected_registrations[]' (grâce aux names des inputs)
            // et le champ caché 'event_id_for_redirect'.
        });
    }

    // Optionnel : Logique pour les petits formulaires de mise à jour de statut individuelle (si on veut de l'AJAX plus tard)
    // Pour l'instant, ils soumettent un formulaire HTML classique, ce qui est bien.
    // document.querySelectorAll('.update-status-form').forEach(form => {
    //     form.addEventListener('submit', function(event) {
    //         // Potentielle logique AJAX ici pour une meilleure UX sans rechargement de page
    //     });
    // });
});