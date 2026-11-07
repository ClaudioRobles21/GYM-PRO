<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../controllers/plans.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $r = createPlan($_POST);
    } elseif ($action === 'update') {
        $r = updatePlan((int)$_POST['id'], $_POST);
    } elseif ($action === 'delete') {
        $r = deletePlan((int)$_POST['id']);
    }
    flashMessage(($r['success']??false)?'success':'danger', $r['message']??'Error');
    header('Location: /gym-system/views/admin/plans.php');
    exit;
}

$plans    = getAllPlans();
$editPlan = null;
if (!empty($_GET['edit'])) $editPlan = getPlanById((int)$_GET['edit']);

$pageTitle = 'Manage Plans';
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="gp-dash-layout">
  <aside class="gp-sidebar d-none d-md-block">
    <div class="gp-sidebar-section">Overview</div>
    <a class="gp-sidebar-link" href="/gym-system/dashboard_admin.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <div class="gp-sidebar-section">Manage</div>
    <a class="gp-sidebar-link" href="/gym-system/views/admin/users.php"><i class="bi bi-people"></i> Users</a>
    <a class="gp-sidebar-link active" href="/gym-system/views/admin/plans.php"><i class="bi bi-box-seam"></i> Plans</a>
    <a class="gp-sidebar-link" href="/gym-system/views/admin/branches.php"><i class="bi bi-building"></i> Branches</a>
    <a class="gp-sidebar-link" href="/gym-system/views/admin/notifications.php"><i class="bi bi-bell"></i> Notifications</a>
    <div class="gp-sidebar-section">Analytics</div>
    <a class="gp-sidebar-link" href="/gym-system/views/admin/reports.php"><i class="bi bi-bar-chart-line"></i> Reports</a>
    <div class="gp-sidebar-section">Account</div>
    <a class="gp-sidebar-link" href="/gym-system/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </aside>
  <main class="gp-dash-content">
    <div class="gp-dash-header">
      <div class="gp-dash-title">MANAGE <span class="text-yellow">PLANS</span></div>
    </div>

    <!-- Create / Edit Form -->
    <div class="gp-card mb-5" style="max-width:560px">
      <div style="font-family:'Bebas Neue',sans-serif;font-size:1.4rem;color:#fff;margin-bottom:1rem">
        <?= $editPlan ? 'Edit Plan' : 'Create New Plan' ?>
      </div>
      <form method="POST">
        <input type="hidden" name="action" value="<?= $editPlan ? 'update' : 'create' ?>">
        <?php if ($editPlan): ?><input type="hidden" name="id" value="<?= $editPlan['id'] ?>"><?php endif; ?>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Plan Name *</label>
            <input type="text" name="name" class="form-control"
                   value="<?= htmlspecialchars($editPlan['name'] ?? '') ?>" required placeholder="e.g. Gold">
          </div>
          <div class="col-md-3">
            <label class="form-label">Price ($/mo) *</label>
            <input type="number" name="price" class="form-control" step="0.01" min="0"
                   value="<?= htmlspecialchars($editPlan['price'] ?? '') ?>" required placeholder="299.00">
          </div>
          <div class="col-md-3">
            <label class="form-label">Duration (months)</label>
            <input type="number" name="duration_months" class="form-control" min="1"
                   value="<?= htmlspecialchars($editPlan['duration_months'] ?? 1) ?>">
          </div>
          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="2"
                      placeholder="Short description of the plan"><?= htmlspecialchars($editPlan['description'] ?? '') ?></textarea>
          </div>
          <?php if ($editPlan): ?>
          <div class="col-md-4">
            <label class="form-label">Active</label>
            <select name="is_active" class="form-select">
              <option value="1" <?= $editPlan['is_active']?'selected':'' ?>>Active</option>
              <option value="0" <?= !$editPlan['is_active']?'selected':'' ?>>Inactive</option>
            </select>
          </div>
          <?php endif; ?>
        </div>
        <div class="d-flex gap-3 mt-3">
          <button type="submit" class="gp-btn-primary"><?= $editPlan ? 'Save Changes' : 'Create Plan' ?></button>
          <?php if ($editPlan): ?>
            <a href="/gym-system/views/admin/plans.php" class="gp-btn-outline">Cancel</a>
          <?php endif; ?>
        </div>
      </form>
    </div>

    <!-- Plans table -->
    <div class="gp-table-wrap">
      <div class="gp-table-head">
        <div class="gp-table-title">All Plans</div>
      </div>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead><tr>
            <th>ID</th><th>Name</th><th>Price</th><th>Duration</th><th>Description</th><th>Status</th><th>Actions</th>
          </tr></thead>
          <tbody>
            <?php foreach ($plans as $p): ?>
            <tr>
              <td style="color:var(--gp-muted)">#<?= $p['id'] ?></td>
              <td style="font-weight:600;color:#fff"><?= htmlspecialchars($p['name']) ?></td>
              <td class="text-yellow fw-semibold">$<?= number_format($p['price'],2) ?></td>
              <td style="color:var(--gp-muted)"><?= $p['duration_months'] ?> mo</td>
              <td style="color:var(--gp-muted);font-size:.82rem;max-width:200px">
                <?= htmlspecialchars(substr($p['description'],0,60)) ?>…
              </td>
              <td><span class="gp-status <?= $p['is_active']?'gp-status--active':'gp-status--expired' ?>">
                <?= $p['is_active']?'Active':'Inactive' ?>
              </span></td>
              <td>
                <div class="d-flex gap-2">
                  <a href="?edit=<?= $p['id'] ?>" class="gp-btn-outline" style="font-size:.7rem;padding:.25rem .6rem">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <form method="POST" class="d-inline">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <button type="submit" class="gp-btn-danger" style="font-size:.7rem;padding:.25rem .6rem"
                            data-confirm="Deactivate plan '<?= htmlspecialchars($p['name']) ?>'?">
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
