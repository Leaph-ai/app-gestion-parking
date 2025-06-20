<?php
$currentSort = $_GET['sort'] ?? 'id';
$currentOrder = $_GET['order'] ?? 'asc';

function getSortUrl($column, $currentSort, $currentOrder, $page) {
    $newOrder = ($currentSort === $column && $currentOrder === 'asc') ? 'desc' : 'asc';
    return "index.php?component=users&sort={$column}&order={$newOrder}&page={$page}";
}

function getSortIcon($column, $currentSort, $currentOrder) {
    if ($currentSort !== $column) {
        return '<i class="fas fa-sort sort-icon"></i>';
    }
    return $currentOrder === 'asc'
        ? '<i class="fas fa-sort-up sort-icon active"></i>'
        : '<i class="fas fa-sort-down sort-icon active"></i>';
}
?>

<div class="users-container">
    <div class="users-header">
        <h1>
            <i class="fas fa-users"></i>
            Gestion des utilisateurs
            <?php if ($totalUsers > 0): ?>
                <span class="count-badge">(<?= $totalUsers ?>)</span>
            <?php endif; ?>
        </h1>

        <a href="index.php?component=user&action=create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Créer un utilisateur
        </a>
    </div>

    <?php if (isset($errors) && !empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
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
                    <a href="<?= getSortUrl('first_name', $currentSort, $currentOrder, $page) ?>" class="sort-header">
                        Prénom <?= getSortIcon('first_name', $currentSort, $currentOrder) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= getSortUrl('last_name', $currentSort, $currentOrder, $page) ?>" class="sort-header">
                        Nom <?= getSortIcon('last_name', $currentSort, $currentOrder) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= getSortUrl('email', $currentSort, $currentOrder, $page) ?>" class="sort-header">
                        Email <?= getSortIcon('email', $currentSort, $currentOrder) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= getSortUrl('phone_number', $currentSort, $currentOrder, $page) ?>" class="sort-header">
                        Téléphone <?= getSortIcon('phone_number', $currentSort, $currentOrder) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= getSortUrl('role', $currentSort, $currentOrder, $page) ?>" class="sort-header">
                        Rôle <?= getSortIcon('role', $currentSort, $currentOrder) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= getSortUrl('active', $currentSort, $currentOrder, $page) ?>" class="sort-header">
                        Statut <?= getSortIcon('active', $currentSort, $currentOrder) ?>
                    </a>
                </th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($users) > 0): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['first_name']) ?></td>
                        <td><?= htmlspecialchars($user['last_name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['phone_number'] ?: '-') ?></td>
                        <td>
                                <span class="role-badge <?= $user['role'] == 2 ? 'role-admin' : 'role-user' ?>">
                                    <?= $user['role'] == 2 ? 'Admin' : 'Utilisateur' ?>
                                </span>
                        </td>
                        <td>
                                <span class="status-badge <?= $user['active'] == 1 ? 'status-active' : 'status-inactive' ?>">
                                    <?= $user['active'] == 1 ? 'Actif' : 'Inactif' ?>
                                </span>
                        </td>
                        <td class="actions">
                            <a href="index.php?component=user&action=edit&id=<?= $user['id'] ?>"
                               class="btn btn-edit" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-delete"
                                    data-user-id="<?= $user['id'] ?>"
                                    title="Supprimer">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="no-data">Aucun utilisateur trouvé</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="index.php?component=users&page=<?= $page - 1 ?>&sort=<?= $currentSort ?>&order=<?= $currentOrder ?>" class="pagination-btn">
                    <i class="fas fa-chevron-left"></i> Précédent
                </a>
            <?php endif; ?>

            <div class="pagination-numbers">
                <?php
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);

                if ($start > 1) {
                    echo '<a href="index.php?component=users&page=1&sort=' . $currentSort . '&order=' . $currentOrder . '" class="pagination-number">1</a>';
                    if ($start > 2) echo '<span class="pagination-dots">...</span>';
                }

                for ($i = $start; $i <= $end; $i++):
                    ?>
                    <?php if ($i == $page): ?>
                    <span class="pagination-number active"><?= $i ?></span>
                <?php else: ?>
                    <a href="index.php?component=users&page=<?= $i ?>&sort=<?= $currentSort ?>&order=<?= $currentOrder ?>" class="pagination-number"><?= $i ?></a>
                <?php endif; ?>
                <?php endfor; ?>

                <?php if ($end < $totalPages): ?>
                    <?php if ($end < $totalPages - 1) echo '<span class="pagination-dots">...</span>'; ?>
                    <a href="index.php?component=users&page=<?= $totalPages ?>&sort=<?= $currentSort ?>&order=<?= $currentOrder ?>" class="pagination-number"><?= $totalPages ?></a>
                <?php endif; ?>
            </div>

            <?php if ($page < $totalPages): ?>
                <a href="index.php?component=users&page=<?= $page + 1 ?>&sort=<?= $currentSort ?>&order=<?= $currentOrder ?>" class="pagination-btn">
                    Suivant <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script type="module">
    import { handleDeleteUser } from "./assets/javascript/components/user.js";
    handleDeleteUser();
</script>