<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../controllers/branches.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $data   = array_merge($_POST, ['admin_id' => $_SESSION['user_id']]);
    if ($action === 'create') {
        $r = createBranch($data);
    } elseif ($action === 'update') {
        $r = updateBranch((int)$_POST['id'], $_POST);
    } elseif ($action === 'delete') {
        $r = deleteBranch((int)$_POST['id']);
    }
    flashMessage(($r['success']??false)?'success':'danger', $r['message']??'Error');
    header('Location: /gym-system/views/admin/branches.php');
    exit;
}

$branches   = getAllBranches();
$editBranch = null;
if (!empty($_GET['edit'])) $editBranch = getBranchById((int)$_GET['edit']);

$pageTitle = 'Manage Branches';
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="gp-dash-layout">
  <aside class="gp-sidebar d-none d-md-block">
    <div class="gp-sidebar-section">Overview</div>
    <a class="gp-sidebar-link" href="/gym-system/dashboard_admin.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <div class="gp-sidebar-section">Manage</div>
    <a class="gp-sidebar-link" href="/gym-system/views/admin/users.php"><i class="bi bi-people"></i> Users</a>
    <a class="gp-sidebar-link" href="/gym-system/views/admin/plans.php"><i class="bi bi-box-seam"></i> Plans</a>
    <a class="gp-sidebar-link active" href="/gym-system/views/admin/branches.php"><i class="bi bi-building"></i> Branches</a>
    <a class="gp-sidebar-link" href="/gym-system/views/admin/notifications.php"><i class="bi bi-bell"></i> Notifications</a>
    <div class="gp-sidebar-section">Analytics</div>
    <a class="gp-sidebar-link" href="/gym-system/views/admin/reports.php"><i class="bi bi-bar-chart-line"></i> Reports</a>
    <div class="gp-sidebar-section">Account</div>
    <a class="gp-sidebar-link" href="/gym-system/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </aside>
  <main class="gp-dash-content">
    <div class="gp-dash-header">
      <div class="gp-dash-title">MANAGE <span class="text-yellow">BRANCHES</span></div>
    </div>

    <!-- Form -->
    <div class="gp-card mb-5" style="max-width:680px">
      <div style="font-family:'Bebas Neue',sans-serif;font-size:1.4rem;color:#fff;margin-bottom:1rem">
        <?= $editBranch ? 'Edit Branch' : 'Add New Branch' ?>
      </div>
      <form method="POST">
        <input type="hidden" name="action" value="<?= $editBranch ? 'update' : 'create' ?>">
        <?php if ($editBranch): ?><input type="hidden" name="id" value="<?= $editBranch['id'] ?>"><?php endif; ?>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Branch Name *</label>
            <input type="text" name="name" class="form-control"
                   value="<?= htmlspecialchars($editBranch['name'] ?? '') ?>" required placeholder="GymPro Downtown">
          </div>
          <div class="col-md-6">
            <label class="form-label">City *</label>
            <input type="text" name="city" class="form-control"
                   value="<?= htmlspecialchars($editBranch['city'] ?? '') ?>" required placeholder="Monterrey">
          </div>
          <div class="col-12">
            <label class="form-label">Address *</label>
            <input type="text" name="address" class="form-control"
                   value="<?= htmlspecialchars($editBranch['address'] ?? '') ?>" required placeholder="Av. Constitución 1500">
          </div>
          <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="tel" name="phone" class="form-control"
                   value="<?= htmlspecialchars($editBranch['phone'] ?? '') ?>" placeholder="818 000 0000">
          </div>
          <div class="col-md-6">
            <?php if ($editBranch): ?>
            <label class="form-label">Active</label>
            <select name="is_active" class="form-select">
              <option value="1" <?= $editBranch['is_active']?'selected':'' ?>>Active</option>
              <option value="0" <?= !$editBranch['is_active']?'selected':'' ?>>Inactive</option>
            </select>
            <?php endif; ?>
          </div>
          <div class="col-12">
            <label class="form-label">Schedule</label>
            <input type="text" name="schedule" class="form-control"
                   value="<?= htmlspecialchars($editBranch['schedule'] ?? '') ?>"
                   placeholder="Mon–Fri 5am–11pm, Sat–Sun 7am–9pm">
          </div>
        </div>
        <div class="d-flex gap-3 mt-3">
          <button type="submit" class="gp-btn-primary"><?= $editBranch ? 'Save Changes' : 'Add Branch' ?></button>
          <?php if ($editBranch): ?>
            <a href="/gym-system/views/admin/branches.php" class="gp-btn-outline">Cancel</a>
          <?php endif; ?>
        </div>
      </form>
    </div>

    <!-- Table -->
    <div class="gp-table-wrap">
      <div class="gp-table-head"><div class="gp-table-title">All Branches</div></div>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead><tr>
            <th>ID</th><th>Name</th><th>City</th><th>Address</th><th>Phone</th><th>Schedule</th><th>Status</th><th>Actions</th>
          </tr></thead>
          <tbody>
            <?php foreach ($branches as $b): ?>
            <tr>
              <td style="color:var(--gp-muted)">#<?= $b['id'] ?></td>
              <td style="font-weight:600;color:#fff"><?= htmlspecialchars($b['name']) ?></td>
              <td style="color:var(--gp-muted)"><?= htmlspecialchars($b['city']) ?></td>
              <td style="color:var(--gp-muted);font-size:.82rem"><?= htmlspecialchars(substr($b['address'],0,40)) ?></td>
              <td style="color:var(--gp-muted);font-size:.82rem"><?= htmlspecialchars($b['phone'] ?? '—') ?></td>
              <td style="color:var(--gp-muted);font-size:.78rem;max-width:160px"><?= htmlspecialchars(substr($b['schedule']??'—',0,50)) ?></td>
              <td><span class="gp-status <?= $b['is_active']?'gp-status--active':'gp-status--expired' ?>">
                <?= $b['is_active']?'Active':'Inactive' ?>
              </span></td>
              <td>
                <div class="d-flex gap-2">
                  <a href="?edit=<?= $b['id'] ?>" class="gp-btn-outline" style="font-size:.7rem;padding:.25rem .6rem">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <form method="POST" class="d-inline">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $b['id'] ?>">
                    <button type="submit" class="gp-btn-danger" style="font-size:.7rem;padding:.25rem .6rem"
                            data-confirm="Deactivate branch '<?= htmlspecialchars($b['name']) ?>'?">
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
