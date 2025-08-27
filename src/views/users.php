<?php 
$usersHtml = '';
if (!empty($users)) {
    $usersHtml .= '<div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Verified</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>';
    foreach ($users as $user) {
        $usersHtml .= '
            <tr>
                <td>' . htmlspecialchars($user['id']) . '</td>
                <td>' . htmlspecialchars($user['name']) . '</td>
                <td>' . htmlspecialchars($user['email']) . '</td>
                <td>' . htmlspecialchars($user['role']) . '</td>
                <td>' . htmlspecialchars($user['verified'] ?? 'Not verified') . '</td>
                <td>' . htmlspecialchars($user['created_at']) . '</td>
            </tr>';
    }
    $usersHtml .= '
            </tbody>
        </table>
    </div>';
} else {
    $usersHtml = '<p>No users found in the system.</p>';
}

$this->view('layout', [
    'title' => $title,
    'content' => '
        <h1>Users</h1>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>User Management</h2>
            <button class="btn btn-primary">Add New User</button>
        </div>
        ' . $usersHtml . '
    '
]); 
?>