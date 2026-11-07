<?php
// includes/header.php
require_once __DIR__ . '/session.php';
$flash    = getFlash();
$pageTitle = $pageTitle ?? 'GymPro';
$user      = isLoggedIn() ? currentUser() : null;
$unread    = 0;
if ($user && $user['role'] === 'client') {
    require_once __DIR__ . '/../controllers/notifications.php';
    $unread = countUnread((int)$user['id']);
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?> — GymPro</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="/gym-system/public/css/style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="gp-nav navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand gp-logo" href="/gym-system/index.php">
      <i class="bi bi-lightning-charge-fill"></i> GYMP<span>RO</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#gpNav">
      <i class="bi bi-list text-white fs-4"></i>
    </button>
    <div class="collapse navbar-collapse" id="gpNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="/gym-system/index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="/gym-system/branches.php">Branches</a></li>
        <li class="nav-item"><a class="nav-link" href="/gym-system/plans.php">Plans</a></li>
      </ul>
      <ul class="navbar-nav ms-auto align-items-center gap-2">
        <?php if ($user): ?>
          <?php if ($user['role'] === 'client'): ?>
            <li class="nav-item position-relative">
              <a class="nav-link" href="/gym-system/dashboard_client.php">
                <i class="bi bi-bell-fill"></i>
                <?php if ($unread > 0): ?>
                  <span class="gp-badge"><?= $unread ?></span>
                <?php endif; ?>
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                <span class="gp-avatar"><?= strtoupper(substr($user['name'],0,1)) ?></span>
                <?= htmlspecialchars($user['name']) ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end gp-dropdown">
                <li><a class="dropdown-item" href="/gym-system/dashboard_client.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                <li><a class="dropdown-item" href="/gym-system/views/client/profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                <li><a class="dropdown-item" href="/gym-system/views/client/digital_id.php"><i class="bi bi-credit-card me-2"></i>Digital ID</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="/gym-system/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
              </ul>
            </li>
          <?php elseif ($user['role'] === 'admin'): ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                <span class="gp-avatar gp-avatar--admin"><?= strtoupper(substr($user['name'],0,1)) ?></span>
                <?= htmlspecialchars($user['name']) ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end gp-dropdown">
                <li><a class="dropdown-item" href="/gym-system/dashboard_admin.php"><i class="bi bi-speedometer2 me-2"></i>Admin Panel</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="/gym-system/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
              </ul>
            </li>
          <?php endif; ?>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/gym-system/login.php">Login</a></li>
          <li class="nav-item"><a class="btn gp-btn-primary btn-sm px-3" href="/gym-system/register.php">Join Now</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- FLASH MESSAGE -->
<?php if ($flash): ?>
<div class="container mt-3">
  <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show gp-alert" role="alert">
    <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>-fill me-2"></i>
    <?= htmlspecialchars($flash['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
</div>
<?php endif; ?>
