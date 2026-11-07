<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../controllers/notifications.php';

requireClient();
$userId = (int)$_SESSION['user_id'];

// Mark as read if ID given
if (!empty($_GET['read'])) {
    markNotificationRead($userId, (int)$_GET['read']);
    header('Location: /gym-system/views/client/notifications.php');
    exit;
}

$notifs    = getUserNotifications($userId);
$pageTitle = 'Notifications';
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="gp-dash-layout">
  <aside class="gp-sidebar d-none d-md-block">
    <div class="gp-sidebar-section">Main</div>
    <a class="gp-sidebar-link" href="/gym-system/dashboard_client.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a class="gp-sidebar-link" href="/gym-system/views/client/profile.php"><i class="bi bi-person"></i> Profile</a>
    <a class="gp-sidebar-link" href="/gym-system/views/client/digital_id.php"><i class="bi bi-credit-card-2-front"></i> Digital ID</a>
    <div class="gp-sidebar-section">Membership</div>
    <a class="gp-sidebar-link" href="/gym-system/views/client/subscription.php"><i class="bi bi-box-seam"></i> My Plan</a>
    <a class="gp-sidebar-link" href="/gym-system/views/client/payments.php"><i class="bi bi-receipt"></i> Payment History</a>
    <div class="gp-sidebar-section">Comms</div>
    <a class="gp-sidebar-link active" href="/gym-system/views/client/notifications.php"><i class="bi bi-bell"></i> Notifications</a>
    <div class="gp-sidebar-section">Account</div>
    <a class="gp-sidebar-link" href="/gym-system/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </aside>
  <main class="gp-dash-content">
    <div class="gp-dash-header d-flex justify-content-between align-items-start">
      <div>
        <div class="gp-dash-title">NOTIFICATIONS</div>
        <div style="color:var(--gp-muted);font-size:.85rem"><?= count($notifs) ?> total messages</div>
      </div>
    </div>

    <?php if (empty($notifs)): ?>
      <div class="text-center py-5" style="color:var(--gp-muted)">
        <i class="bi bi-bell-slash fs-1 d-block mb-3"></i>
        No notifications yet.
      </div>
    <?php else: ?>
      <div style="max-width:680px">
        <?php foreach ($notifs as $n): ?>
        <a href="/gym-system/views/client/notifications.php?read=<?= $n['id'] ?>"
           style="text-decoration:none" class="d-block">
          <div class="gp-notif-item <?= !$n['is_read'] ? 'unread' : '' ?>">
            <div class="d-flex justify-content-between align-items-start gap-2">
              <div class="gp-notif-title"><?= htmlspecialchars($n['title']) ?></div>
              <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <?php if (!$n['is_read']): ?>
                  <span class="gp-status gp-status--active" style="font-size:.65rem">New</span>
                <?php endif; ?>
              </div>
            </div>
            <div class="gp-notif-msg"><?= htmlspecialchars($n['message']) ?></div>
            <div class="gp-notif-date">
              <i class="bi bi-clock me-1"></i><?= date('M j, Y · g:i a', strtotime($n['created_at'])) ?>
            </div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
