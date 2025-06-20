// Services/booking.js - Services pour la gestion des réservations

/**
 * Vérifie la disponibilité d'une place
 */
async function checkAvailability(spotId, startTime, endTime) {
    const response = await fetch(
        `index.php?component=booking&action=check_availability&spot_id=${spotId}&start_time=${encodeURIComponent(startTime)}&end_time=${encodeURIComponent(endTime)}`,
        { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
    );
    return await response.json();
}

/**
 * Calcule le prix d'une réservation
 */
async function calculateBookingPrice(spotId, startTime, endTime) {
    const response = await fetch(
        `index.php?component=booking&action=calculate&spot_id=${spotId}&start_time=${encodeURIComponent(startTime)}&end_time=${encodeURIComponent(endTime)}`,
        { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
    );
    return await response.json();
}

/**
 * Crée une nouvelle réservation
 */
async function createBooking(spotId, startTime, endTime, paymentId) {
    const formData = new FormData();
    formData.append('action', 'create');
    formData.append('spot_id', spotId);
    formData.append('start_time', startTime);
    formData.append('end_time', endTime);
    formData.append('payment_id', paymentId);

    const response = await fetch('index.php?component=booking', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });

    return await response.json();
}

/**
 * Annule une réservation
 */
async function cancelBookingById(bookingId) {
    const formData = new FormData();
    formData.append('action', 'cancel');
    formData.append('id', bookingId);

    const response = await fetch('index.php?component=booking', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });

    return await response.json();
}

/**
 * Récupère une réservation par ID
 */
async function getBookingById(bookingId) {
    const response = await fetch(
        `index.php?component=booking&action=get&id=${bookingId}`,
        { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
    );
    return await response.json();
}

/**
 * Récupère toutes les réservations
 */
async function getAllBookings(page = 1, sortBy = 'id', sortOrder = 'desc') {
    const response = await fetch(
        `index.php?component=booking&action=list&page=${page}&sort=${sortBy}&order=${sortOrder}`,
        { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
    );
    return await response.json();
}

/**
 * Récupère les statistiques des réservations
 */
async function getBookingStats() {
    const response = await fetch(
        'index.php?component=booking&action=stats',
        { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
    );
    return await response.json();
}

/**
 * Récupère les places disponibles
 */
async function getAvailableSpots() {
    const response = await fetch(
        'index.php?component=booking&action=available_spots',
        { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
    );
    return await response.json();
}

/**
 * Récupère les places disponibles pour une période donnée
 */
async function getAvailableSpotsForPeriod(startTime, endTime) {
    const response = await fetch(
        `index.php?component=booking&action=get_available_spots_for_period&start_time=${encodeURIComponent(startTime)}&end_time=${encodeURIComponent(endTime)}`,
        { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
    );
    return await response.json();
}

/**
 * Valide les données de réservation côté client
 */
function validateBookingData(data) {
    const errors = [];

    if (!data.spot_id || parseInt(data.spot_id) <= 0) {
        errors.push('Veuillez sélectionner une place de parking');
    }

    if (!data.start_time) {
        errors.push('L\'heure de début est obligatoire');
    }

    if (!data.end_time) {
        errors.push('L\'heure de fin est obligatoire');
    }

    if (data.start_time && data.end_time) {
        const startDate = new Date(data.start_time);
        const endDate = new Date(data.end_time);
        const now = new Date();

        if (startDate <= now) {
            errors.push('L\'heure de début doit être dans le futur');
        }

        if (endDate <= startDate) {
            errors.push('L\'heure de fin doit être après l\'heure de début');
        }

        // Vérifier la durée minimale (15 minutes)
        const diffMinutes = (endDate - startDate) / (1000 * 60);
        if (diffMinutes < 15) {
            errors.push('La durée minimale de réservation est de 15 minutes');
        }

        if (diffMinutes > 43200) {
            errors.push('La durée maximale de réservation est de 30 jours');
        }
    }

    return errors;
}

/**
 * Formate la durée d'une réservation
 */
function formatBookingDuration(startTime, endTime) {
    const start = new Date(startTime);
    const end = new Date(endTime);
    const diffTime = Math.abs(end - start);
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
    const diffHours = Math.floor((diffTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const diffMinutes = Math.floor((diffTime % (1000 * 60 * 60)) / (1000 * 60));

    let durationText = '';
    if (diffDays > 0) durationText += `${diffDays} jour${diffDays > 1 ? 's' : ''} `;
    if (diffHours > 0) durationText += `${diffHours} heure${diffHours > 1 ? 's' : ''} `;
    if (diffMinutes > 0) durationText += `${diffMinutes} minute${diffMinutes > 1 ? 's' : ''}`;

    return durationText || '0 minute';
}
/**
 * Recherche les places disponibles pour une période donnée
 */
async function searchAvailableSpots() {
    const startTime = document.getElementById('quick_start_time')?.value;
    const endTime = document.getElementById('quick_end_time')?.value;

    // Validation des champs
    if (!startTime || !endTime) {
        showErrorMessage('Veuillez sélectionner une date de début et de fin');
        return;
    }

    const startDate = new Date(startTime);
    const endDate = new Date(endTime);
    const now = new Date();

    if (startDate >= endDate) {
        showErrorMessage('La date de fin doit être postérieure à la date de début');
        return;
    }

    if (startDate < now) {
        showErrorMessage('La date de début ne peut pas être dans le passé');
        return;
    }

    // Vérifier la durée maximale (30 jours)
    const diffTime = endDate - startDate;
    const diffDays = diffTime / (1000 * 60 * 60 * 24);

    if (diffDays > 30) {
        showErrorMessage('La durée maximale de réservation est de 30 jours');
        return;
    }

    // Vérifier qu'on ne réserve pas trop loin dans le futur (6 mois)
    const maxFutureDate = new Date();
    maxFutureDate.setMonth(maxFutureDate.getMonth() + 6);

    if (startDate > maxFutureDate) {
        showErrorMessage('Impossible de réserver plus de 6 mois à l\'avance');
        return;
    }

    // Afficher le loader
    const btn = document.getElementById('search-spots-btn');
    if (!btn) {
        console.error('Bouton search-spots-btn non trouvé');
        return;
    }

    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Recherche...';
    btn.disabled = true;

    try {
        // Faire l'appel API
        const data = await getAvailableSpotsForPeriod(startTime, endTime);

        if (data.success) {
            updateSpotsDisplay(data.spots, data.counts);
            const duration = calculateDetailedDuration(startTime, endTime);
            showSuccessMessage(`${data.spots.length} place(s) disponible(s) trouvée(s) pour ${duration.formatted}`);
        } else {
            showErrorMessage(data.message || 'Erreur lors de la recherche');
            clearSpotsDisplay();
        }
    } catch (error) {
        console.error('Erreur lors de la recherche:', error);
        showErrorMessage('Erreur lors de la recherche des places disponibles');
        clearSpotsDisplay();
    } finally {
        // Restaurer le bouton
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}