import { deleteParkingSpot } from "../services/parking_spot.js";

export const handleDeleteParkingSpot = () => {
    const deleteButtons = document.querySelectorAll('.btn-delete[data-spot-id]');

    deleteButtons.forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();

            const spotId = button.getAttribute('data-spot-id');

            if (confirm('Êtes-vous sûr de vouloir supprimer cette place de parking ?')) {
                try {
                    const result = await deleteParkingSpot(spotId);

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