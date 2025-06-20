
import { deleteUser, deleteAccount } from "../services/user.js";

export const handleDeleteUser = () => {
    const deleteButtons = document.querySelectorAll('.btn-delete[data-user-id]');

    deleteButtons.forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();

            const userId = button.getAttribute('data-user-id');

            if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
                try {
                    const result = await deleteUser(userId);

                    if (result.success) {
                        location.reload();
                    } else if (result.error) {
                        alert(result.error);
                    } else {
                        alert('Erreur inconnue');
                    }
                } catch (error) {
                    alert('Une erreur est survenue: ' + error.message);
                }
            }
        });
    });
};

export const handleDeleteAccount = () => {
    const deleteAccountButton = document.getElementById('delete-account-btn');

    if (deleteAccountButton) {
        deleteAccountButton.addEventListener('click', async (e) => {
            e.preventDefault();

            if (confirm('⚠️ ATTENTION ⚠️\n\nÊtes-vous absolument sûr de vouloir supprimer votre compte ?\n\nCette action est IRRÉVERSIBLE et supprimera définitivement :\n- Votre profil utilisateur\n- Toutes vos données\n- Votre accès à la plateforme\n\nTapez "SUPPRIMER" pour confirmer :')) {
                const confirmation = prompt('Pour confirmer la suppression, tapez exactement : SUPPRIMER');

                if (confirmation === 'SUPPRIMER') {
                    if (confirm('Dernière confirmation : Voulez-vous vraiment supprimer votre compte définitivement ?')) {
                        try {
                            const response = await deleteAccount();
                            if (response.ok) {
                                window.location.href = 'index.php';
                            }
                        } catch (error) {
                            alert('Une erreur est survenue');
                        }
                    }
                } else if (confirmation !== null) {
                    alert('Suppression annulée : le texte de confirmation ne correspond pas.');
                }
            }
        });
    }
};