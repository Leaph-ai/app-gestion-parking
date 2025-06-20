
<?php
// Variables pour le tri
$currentSort = $_GET['sort'] ?? 'number';
$currentOrder = $_GET['order'] ?? 'asc';

// Fonction helper pour générer les URLs de tri
function getSortUrl($column, $currentSort, $currentOrder, $page) {
    $newOrder = ($currentSort === $column && $currentOrder === 'asc') ? 'desc' : 'asc';
    return "index.php?component=parking_spots&sort={$column}&order={$newOrder}&page={$page}";
}

// Fonction helper pour obtenir l'icône de tri
function getSortIcon($column, $currentSort, $currentOrder) {
    if ($currentSort !== $column) {
        return '<i class="fas fa-sort sort-icon"></i>';
    }
    return $currentOrder === 'asc'
        ? '<i class="fas fa-sort-up sort-icon active"></i>'
        : '<i class="fas fa-sort-down sort-icon active"></i>';
}

// Types de places
$spotTypes = getParkingSpotTypes();
?>

<div class="users-container">
    <div class="users-header">
        <h1>
            <i class="fas fa-parking"></i>
            Gestion des places de parking
            <?php if ($totalSpots > 0): ?>
                <span class="count-badge">(<?= $totalSpots ?>)</span>
            <?php endif; ?>
        </h1>
        <a href="index.php?component=parking_spot&action=create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Créer une place
        </a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="users-table-container">
        <table class="users-table">
            <thead>
            <tr>
                <th>
                    <a href="<?= getSortUrl('id', $currentSort, $currentOrder, $page) ?>" class="sort-header">
                        ID <?= getSortIcon('id', $currentSort, $currentOrder) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= getSortUrl('number', $currentSort, $currentOrder, $page) ?>" class="sort-header">
                        Numéro <?= getSortIcon('number', $currentSort, $currentOrder) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= getSortUrl('type', $currentSort, $currentOrder, $page) ?>" class="sort-header">
                        Type <?= getSortIcon('type', $currentSort, $currentOrder) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= getSortUrl('is_occupied', $currentSort, $currentOrder, $page) ?>" class="sort-header">
                        Statut <?= getSortIcon('is_occupied', $currentSort, $currentOrder) ?>
                    </a>
                </th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($parkingSpots) > 0): ?>
                <?php foreach ($parkingSpots as $spot): ?>
                    <tr>
                        <td><?= $spot['id'] ?></td>
                        <td><strong>Place <?= $spot['number'] ?></strong></td>
                        <td>
                            <span class="type-badge type-<?= $spot['type'] ?>">
                                <?= $spotTypes[$spot['type']] ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge <?= $spot['is_occupied'] == 1 ? 'status-occupied' : 'status-free' ?>">
                                <?= $spot['is_occupied'] == 1 ? 'Occupée' : 'Libre' ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="index.php?component=parking_spot&action=edit&id=<?= $spot['id'] ?>"
                               class="btn btn-edit"
                               title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-delete"
                                    data-spot-id="<?= $spot['id'] ?>"
                                    title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="no-data">
                        <i class="fas fa-info-circle"></i>
                        Aucune place de parking trouvée
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="index.php?component=parking_spots&page=<?= $page - 1 ?>&sort=<?= $currentSort ?>&order=<?= $currentOrder ?>" class="pagination-btn">
                    <i class="fas fa-chevron-left"></i> Précédent
                </a>
            <?php endif; ?>

            <div class="pagination-numbers">
                <?php
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);

                if ($start > 1) {
                    echo '<a href="index.php?component=parking_spots&page=1&sort=' . $currentSort . '&order=' . $currentOrder . '" class="pagination-number">1</a>';
                    if ($start > 2) echo '<span class="pagination-dots">...</span>';
                }

                for ($i = $start; $i <= $end; $i++):
                    ?>
                    <?php if ($i == $page): ?>
                    <span class="pagination-number active"><?= $i ?></span>
                <?php else: ?>
                    <a href="index.php?component=parking_spots&page=<?= $i ?>&sort=<?= $currentSort ?>&order=<?= $currentOrder ?>" class="pagination-number"><?= $i ?></a>
                <?php endif; ?>
                <?php endfor; ?>

                <?php if ($end < $totalPages): ?>
                    <?php if ($end < $totalPages - 1) echo '<span class="pagination-dots">...</span>'; ?>
                    <a href="index.php?component=parking_spots&page=<?= $totalPages ?>&sort=<?= $currentSort ?>&order=<?= $currentOrder ?>" class="pagination-number"><?= $totalPages ?></a>
                <?php endif; ?>
            </div>

            <?php if ($page < $totalPages): ?>
                <a href="index.php?component=parking_spots&page=<?= $page + 1 ?>&sort=<?= $currentSort ?>&order=<?= $currentOrder ?>" class="pagination-btn">
                    Suivant <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script type="module">
    import { handleDeleteParkingSpot } from "./assets/javascript/components/parking_spot.js";
    handleDeleteParkingSpot();
</script>