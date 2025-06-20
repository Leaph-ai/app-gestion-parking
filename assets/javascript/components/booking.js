// Components/booking.js - Gestion des composants UI pour les réservations

let calculatedPrice = 0;
let paypalInstance = null;

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
            showSuccessMessage(`${data.spots.length} place(s) disponible(s) trouvée(s)`);
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

/**
 * Met à jour l'affichage des places et statistiques
 */
function updateSpotsDisplay(spots, counts) {
    // Afficher les statistiques
    const statsSection = document.getElementById('spots-stats');
    if (statsSection) {
        statsSection.style.display = 'block';

        const normalCount = document.getElementById('normal-count');
        const handicappedCount = document.getElementById('handicapped-count');
        const adminCount = document.getElementById('admin-count');
        const totalCount = document.getElementById('total-count');

        if (normalCount) normalCount.textContent = counts.normal || 0;
        if (handicappedCount) handicappedCount.textContent = counts.handicapped || 0;
        if (adminCount) adminCount.textContent = counts.admin || 0;
        if (totalCount) totalCount.textContent = counts.total || 0;
    }

    // Remplir le select des places
    const spotSelect = document.getElementById('quick_spot_id');
    const spotSelection = document.getElementById('spot-selection');

    if (spotSelect) {
        spotSelect.innerHTML = '<option value="">Sélectionnez une place</option>';

        if (spots.length > 0) {
            spots.forEach(spot => {
                const option = document.createElement('option');
                option.value = spot.id;
                option.textContent = `Place #${spot.number} - ${spot.type_label}`;
                option.className = `spot-option-${spot.type_string}`;
                option.setAttribute('data-type', spot.type_string);
                spotSelect.appendChild(option);
            });

            if (spotSelection) {
                spotSelection.style.display = 'block';
            }

            // Afficher le bouton de calcul du prix
            const checkBtn = document.getElementById('check-availability-btn');
            if (checkBtn) {
                checkBtn.style.display = 'inline-block';
            }
        } else {
            if (spotSelection) {
                spotSelection.style.display = 'none';
            }
        }
    }
}

/**
 * Efface l'affichage des places
 */
function clearSpotsDisplay() {
    const statsSection = document.getElementById('spots-stats');
    const spotSelection = document.getElementById('spot-selection');
    const spotSelect = document.getElementById('quick_spot_id');
    const checkBtn = document.getElementById('check-availability-btn');

    if (statsSection) statsSection.style.display = 'none';
    if (spotSelection) spotSelection.style.display = 'none';
    if (checkBtn) checkBtn.style.display = 'none';

    if (spotSelect) {
        spotSelect.innerHTML = '<option value="">Sélectionnez une place</option>';
    }
}

/**
 * Affiche un message d'erreur
 */
function showErrorMessage(message) {
    // Créer ou mettre à jour un élément d'alerte
    let alertElement = document.getElementById('booking-alert');

    if (!alertElement) {
        alertElement = document.createElement('div');
        alertElement.id = 'booking-alert';
        alertElement.className = 'alert alert-danger';
        alertElement.style.cssText = `
            margin: 10px 0;
            padding: 12px 16px;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            color: #721c24;
            background-color: #f8d7da;
        `;

        // Insérer l'alerte en haut du formulaire
        const container = document.querySelector('.quick-form-body') || document.querySelector('.booking-container') || document.body;
        container.insertBefore(alertElement, container.firstChild);
    }

    alertElement.className = 'alert alert-danger';
    alertElement.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${message}`;
    alertElement.style.display = 'block';

    // Auto-masquer après 5 secondes
    setTimeout(() => {
        if (alertElement) {
            alertElement.style.display = 'none';
        }
    }, 5000);
}

/**
 * Affiche un message de succès
 */
function showSuccessMessage(message) {
    let alertElement = document.getElementById('booking-alert');

    if (!alertElement) {
        alertElement = document.createElement('div');
        alertElement.id = 'booking-alert';
        alertElement.style.cssText = `
            margin: 10px 0;
            padding: 12px 16px;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            color: #155724;
            background-color: #d4edda;
        `;

        const container = document.querySelector('.quick-form-body') || document.querySelector('.booking-container') || document.body;
        container.insertBefore(alertElement, container.firstChild);
    }

    alertElement.className = 'alert alert-success';
    alertElement.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
    alertElement.style.display = 'block';

    setTimeout(() => {
        if (alertElement) {
            alertElement.style.display = 'none';
        }
    }, 3000);
}

/**
 * Gère l'affichage/masquage du formulaire rapide
 */
function toggleQuickForm() {
    const container = document.getElementById('quick-form-container');
    const btn = document.getElementById('toggle-form-btn');

    if (container.style.display === 'none' || container.style.display === '') {
        container.style.display = 'block';
        btn.innerHTML = '<i class="fas fa-times"></i> Fermer';
        btn.classList.remove('btn-success');
        btn.classList.add('btn-secondary');

        // Réinitialiser le formulaire
        resetQuickForm();
    } else {
        container.style.display = 'none';
        btn.innerHTML = '<i class="fas fa-plus"></i> Réservation rapide';
        btn.classList.remove('btn-secondary');
        btn.classList.add('btn-success');
    }
}

/**
 * Réinitialise le formulaire rapide
 */
function resetQuickForm() {
    const form = document.getElementById('quick-booking-form');
    if (form) {
        form.reset();

        const bookingInfo = document.getElementById('booking-info');
        const paymentSection = document.getElementById('payment-section');

        if (bookingInfo) bookingInfo.style.display = 'none';
        if (paymentSection) paymentSection.style.display = 'none';

        calculatedPrice = 0;

        // Masquer les sections
        clearSpotsDisplay();

        // Détruire l'instance PayPal existante
        if (paypalInstance) {
            const paypalContainer = document.getElementById('paypal-button-container');
            if (paypalContainer) {
                paypalContainer.innerHTML = '';
            }
            paypalInstance = null;
        }
    }
}

/**
 * Met à jour automatiquement l'heure de fin
 */
function updateBookingEndTime() {
    const startTimeInput = document.getElementById('quick_start_time');
    const endTimeInput = document.getElementById('quick_end_time');

    if (startTimeInput && endTimeInput && startTimeInput.value) {
        const startDate = new Date(startTimeInput.value);
        startDate.setHours(startDate.getHours() + 1);
        endTimeInput.value = startDate.toISOString().slice(0, 16);
    }
}

/**
 * Vérifie la disponibilité et calcule le prix
 */
async function checkAvailabilityAndPrice() {
    const spotId = document.getElementById('quick_spot_id')?.value;
    const startTime = document.getElementById('quick_start_time')?.value;
    const endTime = document.getElementById('quick_end_time')?.value;
    const bookingInfo = document.getElementById('booking-info');
    const availabilityStatus = document.getElementById('availability-status');
    const priceInfo = document.getElementById('price-info');
    const paymentSection = document.getElementById('payment-section');

    if (!spotId || !startTime || !endTime) {
        showErrorMessage('Veuillez remplir tous les champs');
        return;
    }

    // Validation côté client
    const validationErrors = validateBookingData({
        spot_id: spotId,
        start_time: startTime,
        end_time: endTime
    });

    if (validationErrors.length > 0) {
        showErrorMessage('Erreurs:\n' + validationErrors.join('\n'));
        return;
    }

    try {
        // Vérifier la disponibilité
        const availabilityResult = await checkAvailability(spotId, startTime, endTime);

        if (bookingInfo) bookingInfo.style.display = 'block';

        if (!availabilityResult.available) {
            if (availabilityStatus) {
                availabilityStatus.innerHTML = '<span style="color: #dc3545;"><i class="fas fa-times-circle"></i> Place non disponible pour ces créneaux</span>';
            }
            if (priceInfo) priceInfo.style.display = 'none';
            if (paymentSection) paymentSection.style.display = 'none';
            return;
        }

        if (availabilityStatus) {
            availabilityStatus.innerHTML = '<span style="color: #28a745;"><i class="fas fa-check-circle"></i> Place disponible</span>';
        }

        // Calculer le prix
        const priceResult = await calculateBookingPrice(spotId, startTime, endTime);

        if (priceResult.success) {
            calculatedPrice = parseFloat(priceResult.price);
            const calculatedPriceElement = document.getElementById('calculated-price');
            if (calculatedPriceElement) {
                calculatedPriceElement.textContent = calculatedPrice.toFixed(2);
            }
            if (priceInfo) priceInfo.style.display = 'block';
            if (paymentSection) paymentSection.style.display = 'block';

            // Initialiser PayPal
            initializePayPal();
        } else {
            showErrorMessage('Erreur lors du calcul du prix');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showErrorMessage('Erreur lors de la vérification');
    }
}

/**
 * Initialise PayPal
 */
function initializePayPal() {
    const paypalContainer = document.getElementById('paypal-button-container');
    if (!paypalContainer) return;

    if (paypalInstance) {
        paypalContainer.innerHTML = '';
    }

    if (typeof paypal !== 'undefined') {
        paypalInstance = paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: calculatedPrice.toFixed(2)
                        },
                        description: 'Réservation de place de parking'
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(async function(details) {
                    await handlePaymentSuccess(details);
                });
            },
            onError: function(err) {
                showErrorMessage('Erreur lors du paiement');
            }
        }).render('#paypal-button-container');
    }
}

/**
 * Gère le succès du paiement
 */
async function handlePaymentSuccess(paymentDetails) {
    console.log('Début handlePaymentSuccess', paymentDetails); // Pour debug

    try {
        const spotId = document.getElementById('quick_spot_id')?.value;
        const startTime = document.getElementById('quick_start_time')?.value;
        const endTime = document.getElementById('quick_end_time')?.value;

        if (!spotId || !startTime || !endTime) {
            showErrorMessage('Erreur: données manquantes');
            return;
        }

        // Afficher un message de traitement
        showInfoMessage('Création de votre réservation en cours...');

        const result = await createBooking(spotId, startTime, endTime, paymentDetails.id);

        if (result.success) {
            // Masquer le message de traitement
            hideAlert();

            // Afficher le message de succès
            showSuccessMessage(`Paiement réussi ! Votre réservation #${result.booking_id || ''} a été créée.`);

            // Attendre 3 secondes avant de recharger pour que l'utilisateur puisse voir le message
            setTimeout(() => {
                location.reload();
            }, 3000);
        } else {
            // Masquer le message de traitement
            hideAlert();

            showErrorMessage('Paiement réussi mais erreur lors de la création de la réservation. Contactez le support.');
        }
    } catch (error) {
        console.error('Erreur:', error);
        hideAlert(); // Masquer le message de traitement
        showErrorMessage('Erreur lors de la création de la réservation');
    }
}

/**
 * Affiche un message d'information
 */
function showInfoMessage(message) {
    console.log('Affichage message info:', message);
    displayMessage(message, 'info');
}

/**
 * Affiche un message de succès
 */
function showSuccessMessage(message) {
    console.log('Affichage message succès:', message);
    displayMessage(message, 'success');
}

/**
 * Affiche un message d'erreur
 */
function showErrorMessage(message) {
    console.log('Affichage message erreur:', message);
    displayMessage(message, 'error');
}

/**
 * Fonction générique pour afficher les messages
 */
function displayMessage(message, type) {
    // Supprimer l'ancien message s'il existe
    hideAlert();

    // Créer le nouvel élément
    const alertElement = document.createElement('div');
    alertElement.id = 'booking-alert';

    // Styles de base
    alertElement.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        min-width: 350px;
        max-width: 500px;
        padding: 16px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.4s ease;
        border-left: 4px solid;
    `;

    // Couleurs selon le type
    let backgroundColor, textColor, borderColor, icon;

    switch (type) {
        case 'success':
            backgroundColor = '#d4edda';
            textColor = '#155724';
            borderColor = '#28a745';
            icon = 'fas fa-check-circle';
            break;
        case 'error':
            backgroundColor = '#f8d7da';
            textColor = '#721c24';
            borderColor = '#dc3545';
            icon = 'fas fa-exclamation-triangle';
            break;
        case 'info':
            backgroundColor = '#d1ecf1';
            textColor = '#0c5460';
            borderColor = '#17a2b8';
            icon = 'fas fa-info-circle';
            break;
        default:
            backgroundColor = '#e2e3e5';
            textColor = '#383d41';
            borderColor = '#6c757d';
            icon = 'fas fa-bell';
    }

    alertElement.style.backgroundColor = backgroundColor;
    alertElement.style.color = textColor;
    alertElement.style.borderLeftColor = borderColor;

    // Contenu du message
    alertElement.innerHTML = `
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center;">
                <i class="${icon}" style="margin-right: 12px; font-size: 16px;"></i>
                <span>${message}</span>
            </div>
            <button onclick="hideAlert()" style="
                background: none; 
                border: none; 
                color: ${textColor}; 
                font-size: 20px; 
                cursor: pointer; 
                padding: 0; 
                margin-left: 15px;
                opacity: 0.7;
                line-height: 1;
            ">×</button>
        </div>
    `;

    // Ajouter au DOM
    document.body.appendChild(alertElement);

    // Animation d'entrée
    requestAnimationFrame(() => {
        alertElement.style.opacity = '1';
        alertElement.style.transform = 'translateX(0)';
    });

    // Auto-masquage
    const autoHideDelay = type === 'error' ? 8000 : (type === 'success' ? 4000 : 5000);
    setTimeout(() => {
        if (document.getElementById('booking-alert') === alertElement) {
            hideAlert();
        }
    }, autoHideDelay);
}

/**
 * Masque l'alerte avec animation
 */
function hideAlert() {
    const alertElement = document.getElementById('booking-alert');
    if (alertElement) {
        alertElement.style.opacity = '0';
        alertElement.style.transform = 'translateX(100%)';

        setTimeout(() => {
            if (alertElement.parentNode) {
                alertElement.parentNode.removeChild(alertElement);
            }
        }, 400);
    }
}

/**
 * Alternative sans rechargement automatique pour tester
 */
async function handlePaymentSuccessNoReload(paymentDetails) {
    try {
        const spotId = document.getElementById('quick_spot_id')?.value;
        const startTime = document.getElementById('quick_start_time')?.value;
        const endTime = document.getElementById('quick_end_time')?.value;

        if (!spotId || !startTime || !endTime) {
            showErrorMessage('Erreur: données manquantes');
            return;
        }

        showInfoMessage('Création de votre réservation en cours...');

        const result = await createBooking(spotId, startTime, endTime, paymentDetails.id);

        setTimeout(() => {
            hideAlert();

            if (result.success) {
                showSuccessMessage(`Paiement réussi ! Votre réservation #${result.booking_id || ''} a été créée. La page va se recharger dans quelques secondes.`);

                // Proposer le rechargement après 5 secondes
                setTimeout(() => {
                    if (confirm('Souhaitez-vous recharger la page pour voir votre nouvelle réservation ?')) {
                        location.reload();
                    }
                }, 5000);
            } else {
                showErrorMessage('Paiement réussi mais erreur lors de la création de la réservation. Contactez le support.');
            }
        }, 1000);

    } catch (error) {
        console.error('Erreur:', error);
        setTimeout(() => {
            hideAlert();
            showErrorMessage('Erreur lors de la création de la réservation');
        }, 1000);
    }
}

/**
 * Annule une réservation
 */
async function cancelBooking(bookingId) {
    if (!confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')) {
        return;
    }

    try {
        const result = await cancelBookingById(bookingId);

        if (result.success) {
            showSuccessMessage(result.message);
            location.reload();
        } else {
            showErrorMessage(result.message || 'Erreur lors de l\'annulation');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showErrorMessage('Erreur lors de l\'annulation');
    }
}

/**
 * Met à jour l'état d'un bouton de soumission
 */
function updateSubmitButton(button, loading = false, originalText = null) {
    if (loading) {
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';
    } else {
        button.disabled = false;
        button.innerHTML = originalText || '<i class="fas fa-save"></i> Enregistrer';
    }
}

/**
 * Initialise les événements de la page booking
 */
function initializeBookingEvents() {
    // Mise à jour automatique de l'heure de fin
    const startTimeInput = document.getElementById('quick_start_time');
    if (startTimeInput) {
        startTimeInput.addEventListener('change', updateBookingEndTime);
    }

    // Bouton de recherche des places disponibles
    const searchBtn = document.getElementById('search-spots-btn');
    if (searchBtn) {
        searchBtn.addEventListener('click', searchAvailableSpots);
    }

    // Bouton de vérification/calcul prix
    const checkBtn = document.getElementById('check-availability-btn');
    if (checkBtn) {
        checkBtn.addEventListener('click', checkAvailabilityAndPrice);
    }

    // Changement de place sélectionnée
    const spotSelect = document.getElementById('quick_spot_id');
    if (spotSelect) {
        spotSelect.addEventListener('change', function() {
            // Masquer les infos de réservation quand on change de place
            const bookingInfo = document.getElementById('booking-info');
            const paymentSection = document.getElementById('payment-section');

            if (bookingInfo) bookingInfo.style.display = 'none';
            if (paymentSection) paymentSection.style.display = 'none';
        });
    }
}

// Initialiser les événements quand le DOM est chargé
document.addEventListener('DOMContentLoaded', initializeBookingEvents);
/**
 * Initialise PayPal avec gestion des messages améliorée
 */
function initializePayPalButton(price) {
    const container = document.getElementById('paypal-button-container');
    if (!container) {
        showErrorMessage('Erreur: conteneur de paiement non trouvé');
        return;
    }

    container.innerHTML = '';

    if (typeof paypal === 'undefined') {
        showErrorMessage('Erreur: système de paiement non disponible');
        return;
    }

    paypal.Buttons({
        createOrder: function(data, actions) {
            showInfoMessage('Initialisation du paiement...');

            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: price.toFixed(2),
                        currency_code: 'EUR'
                    },
                    description: 'Réservation de place de parking'
                }]
            });
        },

        onApprove: function(data, actions) {
            showInfoMessage('Paiement en cours de validation...');

            return actions.order.capture().then(function(details) {
                // Attendre un peu pour que le message soit visible
                setTimeout(() => {
                    handlePaymentSuccess({
                        id: details.id,
                        status: details.status,
                        payer: details.payer
                    });
                }, 500);
            }).catch(function(error) {
                console.error('Erreur capture:', error);
                hideAlert();
                showErrorMessage('Erreur lors de la finalisation du paiement');
            });
        },

        onError: function(err) {
            console.error('Erreur PayPal:', err);
            hideAlert();
            showErrorMessage('Erreur lors du paiement. Veuillez réessayer.');
        },

        onCancel: function(data) {
            console.log('Paiement annulé');
            hideAlert();
            showInfoMessage('Paiement annulé par l\'utilisateur');
        }

    }).render('#paypal-button-container').then(function() {
        console.log('PayPal initialisé');
    }).catch(function(error) {
        console.error('Erreur initialisation PayPal:', error);
        showErrorMessage('Impossible de charger le système de paiement');
    });
}