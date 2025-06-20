
<?php
/**
 * @var PDO $pdo
 */

require "Model/pricing.php";

if (!canAccessComponent('pricing')) {
    denyAccess();
    exit();
}

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');

    $action = $_POST['action'] ?? $_GET['action'] ?? '';

    if (!isAdmin() && in_array($action, ['create', 'update', 'delete', 'toggle'])) {
        echo json_encode(['success' => false, 'message' => 'Accès refusé']);
        exit();
    }

    switch ($action) {
        case 'get':
            if (isset($_GET['id'])) {
                $ruleId = (int)$_GET['id'];
                $rule = getPricingRuleById($pdo, $ruleId);

                if ($rule) {
                    $rule['days'] = explode(',', $rule['days']);
                    echo json_encode(['success' => true, 'rule' => $rule]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Règle non trouvée']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'ID manquant']);
            }
            break;

        case 'create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $errors = validatePricingRuleData($_POST);

                if (empty($errors)) {
                    $success = createPricingRule(
                        $pdo,
                        cleanString($_POST['label']),
                        (int)$_POST['spot_type'],
                        $_POST['start_hour'],
                        $_POST['end_hour'],
                        $_POST['days'] ?? [],
                        (float)$_POST['price_per_hour'],
                        (int)($_POST['min_duration_minutes'] ?? 0),
                        isset($_POST['active'])
                    );

                    if ($success) {
                        echo json_encode(['success' => true, 'message' => 'Règle de tarification créée avec succès']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Erreur lors de la création']);
                    }
                } else {
                    echo json_encode(['success' => false, 'errors' => $errors]);
                }
            }
            break;

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $ruleId = (int)$_POST['id'];
                $errors = validatePricingRuleData($_POST);

                if (empty($errors)) {
                    $success = updatePricingRule(
                        $pdo,
                        $ruleId,
                        cleanString($_POST['label']),
                        (int)$_POST['spot_type'],
                        $_POST['start_hour'],
                        $_POST['end_hour'],
                        $_POST['days'] ?? [],
                        (float)$_POST['price_per_hour'],
                        (int)($_POST['min_duration_minutes'] ?? 0),
                        isset($_POST['active'])
                    );

                    if ($success) {
                        echo json_encode(['success' => true, 'message' => 'Règle de tarification mise à jour avec succès']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
                    }
                } else {
                    echo json_encode(['success' => false, 'errors' => $errors]);
                }
            }
            break;

        case 'delete':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $ruleId = (int)$_POST['id'];

                $success = deletePricingRule($pdo, $ruleId);

                if ($success) {
                    echo json_encode(['success' => true, 'message' => 'Règle de tarification supprimée']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
                }
            }
            break;

        case 'toggle':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $ruleId = (int)$_POST['id'];
                $active = isset($_POST['active']) && $_POST['active'] === 'true';

                $success = togglePricingRuleStatus($pdo, $ruleId, $active);

                if ($success) {
                    $status = $active ? 'activée' : 'désactivée';
                    echo json_encode(['success' => true, 'message' => "Règle $status avec succès"]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erreur lors du changement de statut']);
                }
            }
            break;

        case 'calculate':
            if (isset($_GET['spot_type']) && isset($_GET['start_time']) && isset($_GET['end_time'])) {
                $price = calculatePriceForReservation(
                    $pdo,
                    (int)$_GET['spot_type'],
                    $_GET['start_time'],
                    $_GET['end_time']
                );
                echo json_encode(['success' => true, 'price' => $price]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
    }
    exit();
}

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'create':
        if (!isAdmin()) {
            denyAccess("Seuls les administrateurs peuvent créer des règles de tarification.");
            exit();
        }

        $spotTypes = getSpotTypesForPricing();
        $daysOfWeek = getDaysOfWeek();
        require "View/pricing_form.php";
        break;

    case 'edit':
        if (!isAdmin()) {
            denyAccess("Seuls les administrateurs peuvent modifier des règles de tarification.");
            exit();
        }

        if (isset($_GET['id'])) {
            $ruleId = (int)$_GET['id'];
            $rule = getPricingRuleById($pdo, $ruleId);

            if ($rule) {
                $spotTypes = getSpotTypesForPricing();
                $daysOfWeek = getDaysOfWeek();
                require "View/pricing_form.php";
            } else {
                header("Location: index.php?component=pricing");
                exit();
            }
        } else {
            header("Location: index.php?component=pricing");
            exit();
        }
        break;

    default:
        $showAllRules = isAdmin();
        $rules = getAllPricingRules($pdo, !$showAllRules); // Si pas admin, seulement les actives
        $stats = getPricingStats($pdo);
        $spotTypes = getSpotTypesForPricing();
        $daysOfWeek = getDaysOfWeek();

        require "View/pricing.php";
        break;
}