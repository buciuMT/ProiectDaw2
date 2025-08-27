<?php 
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Admin Dashboard</h1>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">Users</div>
            <div class="card-body">
                <h5 class="card-title"><?php echo count($users ?? []); ?></h5>
                <p class="card-text">Total registered users</p>
                <a href="/admin/users" class="btn btn-light btn-sm">Manage Users</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">Books</div>
            <div class="card-body">
                <h5 class="card-title"><?php echo count($books ?? []); ?></h5>
                <p class="card-text">Total books in library</p>
                <a href="/books" class="btn btn-light btn-sm">Manage Books</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info mb-3">
            <div class="card-header">Reservations</div>
            <div class="card-body">
                <h5 class="card-title"><?php echo count($reservations ?? []); ?></h5>
                <p class="card-text">Total reservations</p>
                <a href="/admin/reservations" class="btn btn-light btn-sm">View Reservations</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">
                <h5>Recent Users</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($users)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $recentUsers = array_slice($users, 0, 5);
                                foreach ($recentUsers as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'librarian' ? 'warning' : 'secondary'); ?>">
                                                <?php echo htmlspecialchars($user['role']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>No users found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">
                <h5>Database Migrations</h5>
            </div>
            <div class="card-body">
                <p>Manage your database schema changes.</p>
                <a href="/admin/migrations" class="btn btn-primary">Manage Migrations</a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>