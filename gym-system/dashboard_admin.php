<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/controllers/reports.php';
require_once __DIR__ . '/controllers/users.php';

requireAdmin();

$summary   = getReportSummary();
$users     = getAllUsers();
$year      = (int)($_GET['year'] ?? date('Y'));
$sales     = getSalesByMonth($year);
$perPlan   = getUsersPerPlan();
$years     = getAvailableYears();

// Build chart data
$months      = range(1,12);
$monthNames  = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
$salesMap    = array_column($sales, 'total', 'month');
$salesVals   = array_map(fn($m) => (float)($salesMap[$m] ?? 0), $months);

$planLabels  = array_column($perPlan, 'plan_name');
$planVals    = array_column($perPlan, 'total_subscribers');

$pageTitle   = 'Admin Dashboard';
require_once __DIR__ . '/includes/header.php';
?>

<div class="gp-dash-layout">
  <!-- Sidebar -->
  <aside class="gp-sidebar d-none d-md-block">
    <div class="px-4 py-2 mb-2">
      <div class="d-flex align-items-center gap-3">
        <div class="gp-avatar gp-avatar--admin" style="width:42px;height:42px;font-size:1rem">A</div>
        <div>
          <div style="font-weight:600;font-size:.9rem;color:#fff"><?= htmlspecialchars($_SESSION['user_name']) ?></div>
          <div style="font-size:.72rem;color:var(--gp-muted)">Administrator</div>
        </div>
      </div>
    </div>

    <div class="gp-sidebar-section">Overview</div>
    <a class="gp-sidebar-link active" href="/gym-system/dashboard_admin.php"><i class="bi bi-speedometer2"></i> Dashboard</a>

    <div class="gp-sidebar-section">Manage</div>
    <a class="gp-sidebar-link" href="/gym-system/views/admin/users.php"><i class="bi bi-people"></i> Users</a>
    <a class="gp-sidebar-link" href="/gym-system/views/admin/plans.php"><i class="bi bi-box-seam"></i> Plans</a>
    <a class="gp-sidebar-link" href="/gym-system/views/admin/branches.php"><i class="bi bi-building"></i> Branches</a>
    <a class="gp-sidebar-link" href="/gym-system/views/admin/notifications.php"><i class="bi bi-bell"></i> Notifications</a>

    <div class="gp-sidebar-section">Analytics</div>
    <a class="gp-sidebar-link" href="/gym-system/views/admin/reports.php"><i class="bi bi-bar-chart-line"></i> Reports</a>

    <div class="gp-sidebar-section">Account</div>
    <a class="gp-sidebar-link" href="/gym-system/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </aside>

  <!-- Content -->
  <main class="gp-dash-content">
    <div class="gp-dash-header">
      <div class="gp-dash-title">ADMIN <span class="text-yellow">DASHBOARD</span></div>
      <div style="color:var(--gp-muted);font-size:.85rem"><?= date('l, F j, Y') ?></div>
    </div>

    <!-- KPI cards -->
    <div class="row g-3 mb-4">
      <div class="col-6 col-lg-3">
        <div class="gp-stat-card">
          <div class="gp-stat-icon"><i class="bi bi-people-fill"></i></div>
          <div class="gp-stat-num" data-count="<?= $summary['total_clients'] ?>"><?= $summary['total_clients'] ?></div>
          <div class="gp-stat-label">Active Clients</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="gp-stat-card">
          <div class="gp-stat-icon"><i class="bi bi-lightning-charge-fill"></i></div>
          <div class="gp-stat-num" data-count="<?= $summary['active_subs'] ?>"><?= $summary['active_subs'] ?></div>
          <div class="gp-stat-label">Active Subs</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="gp-stat-card">
          <div class="gp-stat-icon"><i class="bi bi-calendar-month"></i></div>
          <div class="gp-stat-num" data-count="<?= $summary['month_revenue'] ?>" data-float="1" data-prefix="$">
            $<?= number_format($summary['month_revenue'],2) ?>
          </div>
          <div class="gp-stat-label">This Month</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="gp-stat-card">
          <div class="gp-stat-icon"><i class="bi bi-cash-stack"></i></div>
          <div class="gp-stat-num" data-count="<?= $summary['total_revenue'] ?>" data-float="1" data-prefix="$">
            $<?= number_format($summary['total_revenue'],2) ?>
          </div>
          <div class="gp-stat-label">Total Revenue</div>
        </div>
      </div>
    </div>

    <!-- Charts row -->
    <div class="row g-4 mb-4">
      <div class="col-lg-7">
        <div class="gp-chart-wrap">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="gp-chart-title">Revenue by Month</div>
            <form method="GET" class="d-flex gap-2 align-items-center">
              <select name="year" class="form-select form-select-sm" style="width:100px" onchange="this.form.submit()">
                <?php foreach ($years as $y): ?>
                  <option value="<?= $y ?>" <?= $y==$year?'selected':'' ?>><?= $y ?></option>
                <?php endforeach; ?>
              </select>
            </form>
          </div>
          <canvas id="salesChart" height="200"></canvas>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="gp-chart-wrap">
          <div class="gp-chart-title mb-3">Subscribers by Plan</div>
          <canvas id="planChart" height="220"></canvas>
        </div>
      </div>
    </div>

    <!-- Recent users table -->
    <div class="gp-table-wrap">
      <div class="gp-table-head">
        <div class="gp-table-title">Recent Users</div>
        <a href="/gym-system/views/admin/users.php" class="gp-btn-primary" style="font-size:.75rem;padding:.35rem 1rem">
          Manage All
        </a>
      </div>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead><tr>
            <th>Name</th><th>Email</th><th>Role</th><th>Plan</th><th>Status</th><th>Joined</th>
          </tr></thead>
          <tbody>
            <?php foreach (array_slice($users, 0, 8) as $u): ?>
            <tr>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="gp-avatar <?= $u['role']==='admin'?'gp-avatar--admin':'' ?>" style="width:28px;height:28px;font-size:.75rem">
                    <?= strtoupper(substr($u['name'],0,1)) ?>
                  </div>
                  <?= htmlspecialchars($u['name']) ?>
                </div>
              </td>
              <td style="color:var(--gp-muted)"><?= htmlspecialchars($u['email']) ?></td>
              <td><span class="gp-status gp-status--<?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span></td>
              <td><?= $u['plan_name'] ? htmlspecialchars($u['plan_name']) : '<span style="color:#444">—</span>' ?></td>
              <td>
                <span class="gp-status <?= $u['is_active'] ? 'gp-status--active' : 'gp-status--expired' ?>">
                  <?= $u['is_active'] ? 'Active' : 'Inactive' ?>
                </span>
              </td>
              <td style="color:var(--gp-muted)"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  renderSalesChart(
    <?= json_encode($monthNames) ?>,
    <?= json_encode($salesVals) ?>
  );
  renderPlanPieChart(
    <?= json_encode($planLabels) ?>,
    <?= json_encode($planVals) ?>
  );
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
