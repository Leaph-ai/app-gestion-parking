// Components/pricing.js - Gestion des composants UI pour les tarifs

/**
 * Gère l'affichage/masquage du formulaire rapide
 */
function toggleQuickForm() {
    const container = document.getElementById('quick-form-container');
    const btn = document.getElementById('toggle-form-btn');

    if (container.style.display === 'none') {
        container.style.display = 'block';
        btn.innerHTML = '<i class="fas fa-times"></i> Fermer';
        btn.classList.remove('btn-success');
        btn.classList.add('btn-secondary');

        resetQuickForm();
    } else {
        container.style.display = 'none';
        btn.innerHTML = '<i class="fas fa-plus"></i> Création rapide';
        btn.classList.remove('btn-secondary');
        btn.classList.add('btn-success');
    }
}

/**
 * Réinitialise le formulaire rapide
 */
function resetQuickForm() {
    const form = document.getElementById('quick-pricing-form');
    const container = document.getElementById('quick-form-container');

    form.reset();

    const idField = form.querySelector('input[name="id"]');
    if (idField) {
        idField.remove();
    }

    const header = container.querySelector('.quick-form-header h3');
    if (header) {
        header.innerHTML = '<i class="fas fa-plus"></i> Création rapide de règle';
    }

    const submitBtn = container.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Créer';
    }

    form.dataset.mode = 'create';
}