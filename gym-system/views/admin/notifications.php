<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../controllers/notifications.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $r = createNotification($_POST, (int)$_SESSION['user_id']);
    } elseif ($action === 'update') {
        $r = updateNotification((int)$_POST['id'], $_POST);
    } elseif ($action === 'delete') {
        $r = deleteNotification((int)$_POST['id']);
    }
    flashMessage(($r['success']??false)?'success':'danger', $r['message']??'Error');
    header('Location: /gym-system/views/admin/notifications.php');
    exit;
}

$notifications = getAllNotifications();
$editNotif     = null;
if (!empty($_GET['edit'])) $editNotif = getNotificationById((int)$_GET['edit']);

$pageTitle = 'Manage Notifications';
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="gp-dash-layout">
  <aside class="gp-sidebar d-none d-md-block">
    <div class="gp-sidebar-section">Overview</div>
    <a class="gp-sidebar-link" href="/gym-system/dashboard_admin.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <div class="gp-sidebar-section">Manage</div>
    <a class="gp-sidebar-link" href="/gym-system/views/admin/users.php"><i class="bi bi-people"></i> Users</a>
    <a class="gp-sidebar-link" href="/gym-system/views/admin/plans.php"><i class="bi bi-box-seam"></i> Plans</a>
    <a class="gp-sidebar-link" href="/gym-system/views/admin/branches.php"><i class="bi bi-building"></i> Branches</a>
    <a class="gp-sidebar-link active" href="/gym-system/views/admin/notifications.php"><i class="bi bi-bell"></i> Notifications</a>
    <div class="gp-sidebar-section">Analytics</div>
    <a class="gp-sidebar-link" href="/gym-system/views/admin/reports.php"><i class="bi bi-bar-chart-line"></i> Reports</a>
    <div class="gp-sidebar-section">Account</div>
    <a class="gp-sidebar-link" href="/gym-system/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </aside>
  <main class="gp-dash-content">
    <div class="gp-dash-header">
      <div class="gp-dash-title">MANAGE <span class="text-yellow">NOTIFICATIONS</span></div>
      <div style="color:var(--gp-muted);font-size:.85rem">Notifications are sent to all active clients automatically.</div>
    </div>

    <!-- Form -->
    <div class="gp-card mb-5" style="max-width:600px">
      <div style="font-family:'Bebas Neue',sans-serif;font-size:1.4rem;color:#fff;margin-bottom:1rem">
        <?= $editNotif ? 'Edit Notification' : 'Send New Notification' ?>
      </div>
      <form method="POST">
        <input type="hidden" name="action" value="<?= $editNotif ? 'update' : 'create' ?>">
        <?php if ($editNotif): ?><input type="hidden" name="id" value="<?= $editNotif['id'] ?>"><?php endif; ?>
        <div class="mb-3">
          <label class="form-label">Title *</label>
          <input type="text" name="title" class="form-control"
                 value="<?= htmlspecialchars($editNotif['title'] ?? '') ?>" required
                 placeholder="e.g. Holiday Hours">
        </div>
        <div class="mb-3">
          <label class="form-label">Message *</label>
          <textarea name="message" class="form-control" rows="4" required
                    placeholder="Write the full notification message…"><?= htmlspecialchars($editNotif['message'] ?? '') ?></textarea>
        </div>
        <div class="d-flex gap-3">
          <button type="submit" class="gp-btn-primary">
            <i class="bi bi-send me-2"></i><?= $editNotif ? 'Update' : 'Send to All Clients' ?>
          </button>
          <?php if ($editNotif): ?>
            <a href="/gym-system/views/admin/notifications.php" class="gp-btn-outline">Cancel</a>
          <?php endif; ?>
        </div>
      </form>
    </div>

    <!-- Table -->
    <div class="gp-table-wrap">
      <div class="gp-table-head"><div class="gp-table-title">All Notifications</div></div>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead><tr>
            <th>ID</th><th>Title</th><th>Message</th><th>Sent To</th><th>Created</th><th>Actions</th>
          </tr></thead>
          <tbody>
            <?php foreach ($notifications as $n): ?>
            <tr>
              <td style="color:var(--gp-muted)">#<?= $n['id'] ?></td>
              <td style="font-weight:600;color:#fff"><?= htmlspecialchars($n['title']) ?></td>
              <td style="color:var(--gp-muted);font-size:.82rem;max-width:240px">
                <?= htmlspecialchars(substr($n['message'],0,70)) ?>…
              </td>
              <td>
                <span class="gp-status gp-status--client"><?= $n['sent_count'] ?> users</span>
              </td>
              <td style="color:var(--gp-muted);font-size:.82rem">
                <?= date('M j, Y', strtotime($n['created_at'])) ?>
              </td>
              <td>
                <div class="d-flex gap-2">
                  <a href="?edit=<?= $n['id'] ?>" class="gp-btn-outline" style="font-size:.7rem;padding:.25rem .6rem">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <form method="POST" class="d-inline">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $n['id'] ?>">
                    <button type="submit" class="gp-btn-danger" style="font-size:.7rem;padding:.25rem .6rem"
                            data-confirm="Delete this notification permanently?">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
