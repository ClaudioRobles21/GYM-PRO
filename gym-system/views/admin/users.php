<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../controllers/users.php';

requireAdmin();

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $r = deleteUser((int)$_POST['id']);
        flashMessage($r['success']?'success':'danger', $r['message']);
    } elseif ($action === 'update') {
        $r = updateUser((int)$_POST['id'], $_POST);
        flashMessage($r['success']?'success':'danger', $r['message']);
    }
    header('Location: /gym-system/views/admin/users.php');
    exit;
}

$users     = getAllUsers();
$editUser  = null;
if (!empty($_GET['edit'])) {
    $editUser = getUserById((int)$_GET['edit']);
}
$pageTitle = 'Manage Users';
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="gp-dash-layout">
  <aside class="gp-sidebar d-none d-md-block">
    <div class="gp-sidebar-section">Overview</div>
    <a class="gp-sidebar-link" href="/gym-system/dashboard_admin.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <div class="gp-sidebar-section">Manage</div>
    <a class="gp-sidebar-link active" href="/gym-system/views/admin/users.php"><i class="bi bi-people"></i> Users</a>
    <a class="gp-sidebar-link" href="/gym-system/views/admin/plans.php"><i class="bi bi-box-seam"></i> Plans</a>
    <a class="gp-sidebar-link" href="/gym-system/views/admin/branches.php"><i class="bi bi-building"></i> Branches</a>
    <a class="gp-sidebar-link" href="/gym-system/views/admin/notifications.php"><i class="bi bi-bell"></i> Notifications</a>
    <div class="gp-sidebar-section">Analytics</div>
    <a class="gp-sidebar-link" href="/gym-system/views/admin/reports.php"><i class="bi bi-bar-chart-line"></i> Reports</a>
    <div class="gp-sidebar-section">Account</div>
    <a class="gp-sidebar-link" href="/gym-system/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </aside>
  <main class="gp-dash-content">
    <div class="gp-dash-header d-flex justify-content-between align-items-start">
      <div>
        <div class="gp-dash-title">MANAGE <span class="text-yellow">USERS</span></div>
        <div style="color:var(--gp-muted);font-size:.85rem"><?= count($users) ?> total users</div>
      </div>
    </div>

    <!-- Edit Modal inline -->
    <?php if ($editUser): ?>
    <div class="gp-card mb-4" style="max-width:560px">
      <div style="font-family:'Bebas Neue',sans-serif;font-size:1.4rem;color:#fff;margin-bottom:1rem">
        Edit User: <?= htmlspecialchars($editUser['name']) ?>
      </div>
      <form method="POST">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?= $editUser['id'] ?>">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($editUser['name']) ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($editUser['email']) ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($editUser['phone'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">New Password <span style="color:#555">(optional)</span></label>
            <input type="password" name="password" class="form-control" placeholder="Leave blank to keep">
          </div>
          <div class="col-md-6">
            <label class="form-label">Active</label>
            <select name="is_active" class="form-select">
              <option value="1" <?= $editUser['is_active']?'selected':'' ?>>Active</option>
              <option value="0" <?= !$editUser['is_active']?'selected':'' ?>>Inactive</option>
            </select>
          </div>
        </div>
        <div class="d-flex gap-3 mt-3">
          <button type="submit" class="gp-btn-primary">Save Changes</button>
          <a href="/gym-system/views/admin/users.php" class="gp-btn-outline">Cancel</a>
        </div>
      </form>
    </div>
    <?php endif; ?>

    <div class="gp-table-wrap">
      <div class="gp-table-head">
        <div class="gp-table-title">All Users</div>
        <input type="text" id="tableSearch" class="form-control" placeholder="Search…"
               style="width:220px;font-size:.83rem;padding:.4rem .75rem">
      </div>
      <div class="table-responsive">
        <table class="table mb-0" data-searchable>
          <thead><tr>
            <th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Plan</th><th>Status</th><th>Joined</th><th>Actions</th>
          </tr></thead>
          <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
              <td style="color:var(--gp-muted)">#<?= $u['id'] ?></td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="gp-avatar <?= $u['role']==='admin'?'gp-avatar--admin':'' ?>" style="width:26px;height:26px;font-size:.7rem">
                    <?= strtoupper(substr($u['name'],0,1)) ?>
                  </div>
                  <?= htmlspecialchars($u['name']) ?>
                </div>
              </td>
              <td style="color:var(--gp-muted);font-size:.83rem"><?= htmlspecialchars($u['email']) ?></td>
              <td><span class="gp-status gp-status--<?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span></td>
              <td><?= $u['plan_name'] ? htmlspecialchars($u['plan_name']) : '<span style="color:#444">—</span>' ?></td>
              <td><span class="gp-status <?= $u['is_active']?'gp-status--active':'gp-status--expired' ?>">
                <?= $u['is_active'] ? 'Active' : 'Inactive' ?>
              </span></td>
              <td style="color:var(--gp-muted);font-size:.82rem"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
              <td>
                <div class="d-flex gap-2">
                  <a href="?edit=<?= $u['id'] ?>" class="gp-btn-outline" style="font-size:.7rem;padding:.25rem .6rem">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <?php if ($u['id'] != $_SESSION['user_id']): ?>
                  <form method="POST" class="d-inline">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                    <button type="submit" class="gp-btn-danger" style="font-size:.7rem;padding:.25rem .6rem"
                            data-confirm="Deactivate user <?= htmlspecialchars($u['name']) ?>?">
                      <i class="bi bi-person-x"></i>
                    </button>
                  </form>
                  <?php endif; ?>
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
