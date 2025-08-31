<?php 
ob_start();
?>

<style>
    .overdue-row {
        background-color: #f8d7da !important;
        border-left: 4px solid #dc3545;
    }
    
    .overdue-row td {
        color: #721c24;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Employee Dashboard</h1>
    <a href="/" class="btn btn-secondary">Back to Home</a>
</div>

<!-- Search Section -->
<div class="card mb-4">
    <div class="card-header">
        <h5>Search Reservations</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="/employee/dashboard">
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="user_search" class="form-label">Search by User</label>
                        <select class="form-select" id="user_search" name="user_id">
                            <option value="">Select a user</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>" <?php echo (isset($_GET['user_id']) && $_GET['user_id'] == $user['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($user['name'] . ' (' . $user['email'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="book_search" class="form-label">Search by Book</label>
                        <select class="form-select" id="book_search" name="book_id">
                            <option value="">Select a book</option>
                            <?php foreach ($books as $book): ?>
                                <option value="<?php echo $book['id']; ?>" <?php echo (isset($_GET['book_id']) && $_GET['book_id'] == $book['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($book['title'] . ' by ' . $book['author']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="/employee/dashboard" class="btn btn-secondary">Clear</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Overdue Reservations -->
<?php if (!empty($overdueReservations)): ?>
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">
            <h5>Overdue Reservations</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Book</th>
                            <th>Reserved Date</th>
                            <th>Due Date</th>
                            <th>Days Overdue</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($overdueReservations as $reservation): ?>
                            <tr class="overdue-row">
                                <td><?php echo htmlspecialchars($reservation['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['book_title']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($reservation['reserved_at'])); ?></td>
                                <td><?php echo date('M j, Y', strtotime($reservation['due_date'])); ?></td>
                                <td>
                                    <?php 
                                    $dueDate = new DateTime($reservation['due_date']);
                                    $today = new DateTime();
                                    $interval = $today->diff($dueDate);
                                    echo $interval->days;
                                    ?>
                                </td>
                                <td>
                                    <form method="POST" action="/employee/manage-reservation" class="d-inline">
                                        <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                        <button type="submit" name="action" value="mark_returned" class="btn btn-sm btn-success">Mark Returned</button>
                                    </form>
                                    <form method="POST" action="/employee/manage-reservation" class="d-inline">
                                        <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                        <button type="submit" name="action" value="report_missing" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure this book is missing? This will decrease the total number of copies.')">Report Missing</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Active Reservations -->
<div class="card">
    <div class="card-header">
        <h5>Active Reservations</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($activeReservations)): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Book</th>
                            <th>Reserved Date</th>
                            <th>Due Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activeReservations as $reservation): ?>
                            <tr <?php echo (!empty($reservation['due_date']) && strtotime($reservation['due_date']) < time()) ? 'class="overdue-row"' : ''; ?>>
                                <td><?php echo htmlspecialchars($reservation['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['book_title']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($reservation['reserved_at'])); ?></td>
                                <td><?php echo !empty($reservation['due_date']) ? date('M j, Y', strtotime($reservation['due_date'])) : 'Not set'; ?></td>
                                <td>
                                    <form method="POST" action="/employee/manage-reservation" class="d-inline">
                                        <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                        <button type="submit" name="action" value="approve" class="btn btn-sm btn-primary" <?php echo !empty($reservation['due_date']) ? 'disabled' : ''; ?>>Approve</button>
                                    </form>
                                    <form method="POST" action="/employee/manage-reservation" class="d-inline">
                                        <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                        <button type="submit" name="action" value="cancel" class="btn btn-sm btn-warning" onclick="return confirm('Are you sure you want to cancel this reservation?')">Cancel</button>
                                    </form>
                                    <form method="POST" action="/employee/manage-reservation" class="d-inline">
                                        <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                        <button type="submit" name="action" value="mark_returned" class="btn btn-sm btn-success">Mark Returned</button>
                                    </form>
                                    <form method="POST" action="/employee/manage-reservation" class="d-inline">
                                        <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                        <button type="submit" name="action" value="report_missing" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure this book is missing? This will decrease the total number of copies.')">Report Missing</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No active reservations found.</p>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>