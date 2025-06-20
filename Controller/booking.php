<?php
/**
 * @var PDO $pdo
 */

require "Model/booking.php";
require "Model/users.php";

if (!canAccessComponent('booking')) {
    denyAccess();
    exit();
}


if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');

    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    $currentUserId = getCurrentUserId($pdo);

    switch ($action) {
        case 'get':
            if (isset($_GET['id'])) {
                $bookingId = (int)$_GET['id'];
                $booking = getBookingById($pdo, $bookingId);

                if ($booking) {
                    if (!isAdmin() && $booking['user_id'] != $currentUserId) {
                        echo json_encode(['success' => false, 'message' => 'Accès refusé']);
                        exit();
                    }

                    echo json_encode(['success' => true, 'booking' => $booking]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Réservation non trouvée']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'ID manquant']);
            }
            break;

        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $errors = validateBookingData($_POST);

                if (empty($errors)) {
                    $spotId = (int)$_POST['spot_id'];
                    $startTime = $_POST['start_time'];
                    $endTime = $_POST['end_time'];
                    $paymentId = $_POST['payment_id'] ?? null;

                    if (!isSpotAvailable($pdo, $spotId, $startTime, $endTime)) {
                        echo json_encode(['success' => false, 'message' => 'Cette place n\'est pas disponible pour ces créneaux']);
                        exit();
                    }

                    $query = "SELECT type FROM parking_spots WHERE id = :id";
                    $res = $pdo->prepare($query);
                    $res->bindParam(':id', $spotId, PDO::PARAM_INT);
                    $res->execute();
                    $spot = $res->fetch(PDO::FETCH_ASSOC);

                    if (!$spot) {
                        echo json_encode(['success' => false, 'message' => 'Place de parking introuvable']);
                        exit();
                    }

                    $totalPrice = calculateBookingPrice($pdo, $spot['type'], $startTime, $endTime);

                    $bookingId = createBooking($pdo, $currentUserId, $spotId, $startTime, $endTime, $totalPrice);

                    if ($bookingId) {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Réservation créée avec succès',
                            'booking_id' => $bookingId,
                            'total_price' => $totalPrice
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Erreur lors de la création']);
                    }
                } else {
                    echo json_encode(['success' => false, 'errors' => $errors]);
                }
            }
            break;

        case 'cancel':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $bookingId = (int)$_POST['id'];
                $booking = getBookingById($pdo, $bookingId);

                if (!$booking) {
                    echo json_encode(['success' => false, 'message' => 'Réservation non trouvée']);
                    exit();
                }

                if (!isAdmin() && $booking['user_id'] != $currentUserId) {
                    echo json_encode(['success' => false, 'message' => 'Accès refusé']);
                    exit();
                }

                $success = cancelBooking($pdo, $bookingId);

                if ($success) {
                    echo json_encode(['success' => true, 'message' => 'Réservation annulée avec succès']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'annulation']);
                }
            }
            break;

        case 'calculate':
            if (isset($_GET['spot_id']) && isset($_GET['start_time']) && isset($_GET['end_time'])) {
                $spotId = (int)$_GET['spot_id'];

                $query = "SELECT type FROM parking_spots WHERE id = :id";
                $res = $pdo->prepare($query);
                $res->bindParam(':id', $spotId, PDO::PARAM_INT);
                $res->execute();
                $spot = $res->fetch(PDO::FETCH_ASSOC);

                if ($spot) {
                    $price = calculateBookingPrice($pdo, $spot['type'], $_GET['start_time'], $_GET['end_time']);
                    echo json_encode(['success' => true, 'price' => $price]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Place non trouvée']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
            }
            break;

        case 'check_availability':
            if (isset($_GET['spot_id']) && isset($_GET['start_time']) && isset($_GET['end_time'])) {
                $available = isSpotAvailable($pdo, (int)$_GET['spot_id'], $_GET['start_time'], $_GET['end_time']);
                echo json_encode(['success' => true, 'available' => $available]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
            }
            break;

        case 'get_available_spots_for_period':
            if (isset($_GET['start_time']) && isset($_GET['end_time'])) {
                $startTime = $_GET['start_time'];
                $endTime = $_GET['end_time'];
                
                $spots = getAvailableSpotsForPeriod($pdo, $startTime, $endTime, isAdmin());
                $counts = getSpotCountsByType($pdo, $startTime, $endTime, isAdmin());
                
                echo json_encode([
                    'success' => true, 
                    'spots' => $spots,
                    'counts' => $counts
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
    }
    exit();
}

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$sortBy = $_GET['sort'] ?? 'id';
$sortOrder = $_GET['order'] ?? 'desc';
$currentUserId = getCurrentUserId($pdo);

$userId = isAdmin() ? null : $currentUserId;

$bookings = getBookings($pdo, $page, $sortBy, $sortOrder, $userId);
$totalBookings = getTotalBookings($pdo, $userId);
$totalPages = ceil($totalBookings / 10);
$stats = getBookingStats($pdo);

$availableSpots = getAvailableSpotsForPeriod($pdo, null, null, isAdmin());
$spotCounts = getSpotCountsByType($pdo, null, null, isAdmin());

require "View/booking.php";