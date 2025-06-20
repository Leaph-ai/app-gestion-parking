// Services/pricing.js - Services pour la gestion des tarifs

/**
 * Soumet le formulaire de création rapide
 */
async function submitQuickPricingForm(form) {
    const formData = new FormData(form);
    formData.append('action', 'create');

    const response = await fetch('index.php?component=pricing', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    return await response.json();
}

/**
 * Met à jour une règle via le formulaire rapide
 */
async function updateQuickPricingForm(form) {
    const formData = new FormData(form);
    formData.append('action', 'update');

    const response = await fetch('index.php?component=pricing', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    return await response.json();
}

/**
 * Calcule le prix pour une réservation
 */
function calculatePrice() {
    const spotType = document.getElementById('calc_spot_type').value;
    const startTime = document.getElementById('calc_start_time').value;
    const endTime = document.getElementById('calc_end_time').value;

    if (!spotType || !startTime || !endTime) {
        showErrorMessage('Veuillez remplir tous les champs');
        return;
    }

    if (new Date(endTime) <= new Date(startTime)) {
        showErrorMessage('La date de fin doit être après la date de début');
        return;
    }

    fetchPriceCalculation(spotType, startTime, endTime)
        .then(data => {
            if (data.success) {
                const duration = calculateDuration(startTime, endTime);
                displayPriceResult(data.price, duration);
            } else {
                showErrorMessage(data.message || 'Erreur lors du calcul');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showErrorMessage('Erreur lors du calcul');
        });
}

/**
 * Appel API pour le calcul de prix
 */
async function fetchPriceCalculation(spotType, startTime, endTime) {
    const response = await fetch(`index.php?component=pricing&action=calculate&spot_type=${spotType}&start_time=${encodeURIComponent(startTime)}&end_time=${encodeURIComponent(endTime)}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    return await response.json();
}

/**
 * Bascule le statut d'une règle de tarification
 */
function toggleRuleStatus(id, active) {
    updateRuleStatus(id, active)
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showErrorMessage(data.message || 'Erreur lors du changement de statut');
                location.reload();
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showErrorMessage('Erreur lors du changement de statut');
            location.reload();
        });
}

/**
 * Appel API pour mettre à jour le statut d'une règle
 */
async function updateRuleStatus(id, active) {
    const response = await fetch('index.php?component=pricing', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `action=toggle&id=${id}&active=${active}`
    });

    return await response.json();
}

/**
 * Supprime une règle de tarification
 */
function deleteRule(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette règle de tarification ?')) {
        deleteRuleById(id)
            .then(data => {
                if (data.success) {
                    showSuccessMessage(data.message);
                    location.reload();
                } else {
                    showErrorMessage(data.message || 'Erreur lors de la suppression');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showErrorMessage('Erreur lors de la suppression');
            });
    }
}

/**
 * Appel API pour supprimer une règle
 */
async function deleteRuleById(id) {
    const response = await fetch('index.php?component=pricing', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `action=delete&id=${id}`
    });

    return await response.json();
}

/**
 * Récupère toutes les règles de tarification
 */
async function getAllPricingRules(activeOnly = false) {
    const response = await fetch(`index.php?component=pricing&action=list&active_only=${activeOnly}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    return await response.json();
}

/**
 * Récupère une règle de tarification par ID
 */
async function getPricingRuleById(id) {
    const response = await fetch(`index.php?component=pricing&action=get&id=${id}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    return await response.json();
}

/**
 * Met à jour une règle de tarification
 */
async function updatePricingRule(id, data) {
    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('id', id);

    for (const [key, value] of Object.entries(data)) {
        if (Array.isArray(value)) {
            value.forEach(item => formData.append(`${key}[]`, item));
        } else {
            formData.append(key, value);
        }
    }

    const response = await fetch('index.php?component=pricing', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    return await response.json();
}

/**
 * Récupère les statistiques des règles de tarification
 */
async function getPricingStats() {
    const response = await fetch('index.php?component=pricing&action=stats', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    return await response.json();
}

/**
 * Valide une règle de tarification côté client
 */
function validatePricingRule(data) {
    const errors = [];

    if (!data.label || data.label.trim() === '') {
        errors.push('Le libellé est obligatoire');
    }

    if (!data.spot_type || ![1, 2, 3].includes(parseInt(data.spot_type))) {
        errors.push('Le type de place est obligatoire et doit être valide');
    }

    if (!data.start_hour) {
        errors.push('L\'heure de début est obligatoire');
    }

    if (!data.end_hour) {
        errors.push('L\'heure de fin est obligatoire');
    }

    if (data.start_hour && data.end_hour && data.start_hour >= data.end_hour) {
        errors.push('L\'heure de fin doit être après l\'heure de début');
    }

    if (!data.days || !Array.isArray(data.days) || data.days.length === 0) {
        errors.push('Au moins un jour doit être sélectionné');
    }

    if (!data.price_per_hour || parseFloat(data.price_per_hour) <= 0) {
        errors.push('Le prix par heure doit être supérieur à 0');
    }

    if (data.min_duration_minutes && parseInt(data.min_duration_minutes) < 0) {
        errors.push('La durée minimale ne peut pas être négative');
    }

    return errors;
}

/**
 * Formate les données du formulaire pour l'API
 */
function formatPricingFormData(formData) {
    const data = {};

    for (const [key, value] of formData.entries()) {
        if (key === 'days[]') {
            if (!data.days) data.days = [];
            data.days.push(value);
        } else {
            data[key] = value;
        }
    }

    return data;
}