<?php
session_start();
$admin_password = 'admin123'; // Change this password as needed
$login_error = '';

if (isset($_POST['logout'])) {
    unset($_SESSION['admin_logged_in']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['password'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $login_error = 'Incorrect password.';
    }
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true): ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Contact Submissions</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>body { background: #f8fafc; } .login-box { max-width: 400px; margin: 100px auto; padding: 32px; background: #fff; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.07); }</style>
</head>
<body>
<div class="login-box">
    <h2 class="mb-4 text-primary">Admin Login</h2>
    <?php if ($login_error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($login_error) ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required autofocus>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
</div>
</body>
</html>
<?php exit; endif; ?>
<?php
// Admin view for contact form submissions
$dataFile = 'contact_submissions.json';
$submissions = [];
if (file_exists($dataFile)) {
    $json = file_get_contents($dataFile);
    $submissions = json_decode($json, true) ?: [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Form Submissions - Admin View</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f8fafc; font-family: 'Segoe UI', Arial, sans-serif; }
        .container { max-width: 1100px; margin: 40px auto; }
        h1 { margin-bottom: 32px; }
        table { background: #fff; }
        th, td { vertical-align: middle !important; }
        .table-responsive { box-shadow: 0 4px 16px rgba(0,0,0,0.07); border-radius: 12px; overflow: hidden; }
        .logout-btn { position: absolute; top: 24px; right: 32px; }
    </style>
</head>
<body>
<div class="container position-relative">
    <form method="post" class="logout-btn">
        <button type="submit" name="logout" class="btn btn-outline-danger btn-sm">Logout</button>
    </form>
    <h1 class="text-primary">Contact Form Submissions</h1>
    <?php if (empty($submissions)): ?>
        <div class="alert alert-info">No submissions found.</div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-primary">
                <tr>
                    <th>#</th>
                    <th>Date/Time</th>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach (array_reverse($submissions) as $i => $row): ?>
                <tr>
                    <td><?= count($submissions) - $i ?></td>
                    <td><?= htmlspecialchars($row['timestamp'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['mobile'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['subject'] ?? '') ?></td>
                    <td><?= nl2br(htmlspecialchars($row['message'] ?? '')) ?></td>
                    <td><?= htmlspecialchars($row['ip_address'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
</body>
</html> 