<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../controllers/reports.php';

requireAdmin();

$year    = (int)($_GET['year'] ?? date('Y'));
$years   = getAvailableYears();
$sales   = getSalesByMonth($year);
$perPlan = getUsersPerPlan();
$summary = getReportSummary();

$monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
$salesMap   = array_column($sales, 'total', 'month');
$salesVals  = array_map(fn($m) => (float)($salesMap[$m] ?? 0), range(1,12));
$salesCount = array_column($sales, 'count', 'month');
$countVals  = array_map(fn($m) => (int)($salesCount[$m] ?? 0), range(1,12));

$planLabels = array_column($perPlan, 'plan_name');
$planVals   = array_map('intval', array_column($perPlan, 'total_subscribers'));

$pageTitle = 'Reports';
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
    <a class="gp-sidebar-link" href="/gym-system/views/admin/notifications.php"><i class="bi bi-bell"></i> Notifications</a>
    <div class="gp-sidebar-section">Analytics</div>
    <a class="gp-sidebar-link active" href="/gym-system/views/admin/reports.php"><i class="bi bi-bar-chart-line"></i> Reports</a>
    <div class="gp-sidebar-section">Account</div>
    <a class="gp-sidebar-link" href="/gym-system/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </aside>

  <main class="gp-dash-content">
    <div class="gp-dash-header d-flex justify-content-between align-items-start flex-wrap gap-3">
      <div>
        <div class="gp-dash-title">SALES <span class="text-yellow">REPORTS</span></div>
        <div style="color:var(--gp-muted);font-size:.85rem">Financial overview and membership analytics</div>
      </div>
      <form method="GET" class="d-flex align-items-center gap-2">
        <label style="color:var(--gp-muted);font-size:.82rem">Year:</label>
        <select name="year" class="form-select form-select-sm" style="width:110px" onchange="this.form.submit()">
          <?php foreach ($years as $y): ?>
            <option value="<?= $y ?>" <?= $y==$year?'selected':'' ?>><?= $y ?></option>
          <?php endforeach; ?>
        </select>
      </form>
    </div>

    <!-- KPI summary -->
    <div class="row g-3 mb-5">
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
          <div class="gp-stat-label">Active Subscriptions</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="gp-stat-card">
          <div class="gp-stat-icon"><i class="bi bi-calendar-month"></i></div>
          <div class="gp-stat-num" data-count="<?= $summary['month_revenue'] ?>" data-float="1" data-prefix="$">
            $<?= number_format($summary['month_revenue'],2) ?>
          </div>
          <div class="gp-stat-label">This Month Revenue</div>
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

    <!-- Revenue bar chart -->
    <div class="row g-4 mb-4">
      <div class="col-lg-8">
        <div class="gp-chart-wrap">
          <div class="gp-chart-title">Monthly Revenue — <?= $year ?></div>
          <canvas id="salesChart" height="180"></canvas>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="gp-chart-wrap">
          <div class="gp-chart-title">Subscribers by Plan</div>
          <canvas id="planChart" height="220"></canvas>
        </div>
      </div>
    </div>

    <!-- Transactions count chart -->
    <div class="row g-4 mb-5">
      <div class="col-lg-8">
        <div class="gp-chart-wrap">
          <div class="gp-chart-title">Transactions per Month — <?= $year ?></div>
          <canvas id="countChart" height="160"></canvas>
        </div>
      </div>
      <div class="col-lg-4">
        <!-- Plan breakdown table -->
        <div class="gp-chart-wrap h-100">
          <div class="gp-chart-title mb-3">Plan Breakdown</div>
          <table class="table mb-0" style="font-size:.85rem">
            <thead><tr>
              <th style="padding:.5rem .75rem">Plan</th>
              <th style="padding:.5rem .75rem">Price</th>
              <th style="padding:.5rem .75rem;text-align:right">Subs</th>
            </tr></thead>
            <tbody>
              <?php foreach ($perPlan as $p): ?>
              <tr>
                <td style="padding:.5rem .75rem;font-weight:600;color:#fff"><?= htmlspecialchars($p['plan_name']) ?></td>
                <td style="padding:.5rem .75rem;color:var(--gp-yellow)">$<?= number_format($p['price'],0) ?></td>
                <td style="padding:.5rem .75rem;text-align:right">
                  <span class="gp-status gp-status--client"><?= $p['total_subscribers'] ?></span>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Monthly breakdown table -->
    <div class="gp-table-wrap">
      <div class="gp-table-head">
        <div class="gp-table-title">Monthly Breakdown — <?= $year ?></div>
      </div>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead><tr>
            <th>Month</th>
            <th class="text-end">Transactions</th>
            <th class="text-end">Revenue</th>
            <th style="width:220px">Revenue Bar</th>
          </tr></thead>
          <tbody>
            <?php
            $maxRev = max(array_merge([1], $salesVals));
            foreach ($monthNames as $i => $mName):
              $rev   = $salesVals[$i];
              $cnt   = $countVals[$i];
              $pct   = $maxRev > 0 ? ($rev / $maxRev * 100) : 0;
            ?>
            <tr>
              <td style="font-weight:600;color:#fff"><?= $mName ?> <?= $year ?></td>
              <td class="text-end" style="color:var(--gp-muted)"><?= $cnt ?></td>
              <td class="text-end fw-semibold <?= $rev > 0 ? 'text-yellow' : '' ?>">
                <?= $rev > 0 ? '$'.number_format($rev,2) : '<span style="color:#333">—</span>' ?>
              </td>
              <td>
                <div style="background:var(--gp-border);border-radius:3px;height:8px;overflow:hidden">
                  <div style="width:<?= round($pct,1) ?>%;background:var(--gp-yellow);height:100%;transition:width .6s;border-radius:3px"></div>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr style="border-top:2px solid var(--gp-border)">
              <td style="font-family:'Bebas Neue',sans-serif;font-size:1rem;color:#fff">TOTAL</td>
              <td class="text-end fw-bold text-yellow"><?= array_sum($countVals) ?></td>
              <td class="text-end fw-bold text-yellow">$<?= number_format(array_sum($salesVals),2) ?></td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  renderSalesChart(<?= json_encode($monthNames) ?>, <?= json_encode($salesVals) ?>);
  renderPlanPieChart(<?= json_encode($planLabels) ?>, <?= json_encode($planVals) ?>);

  // Transactions line chart
  const ctx = document.getElementById('countChart');
  if (ctx) {
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?= json_encode($monthNames) ?>,
        datasets: [{
          label: 'Transactions',
          data: <?= json_encode($countVals) ?>,
          borderColor: '#f5c518',
          backgroundColor: 'rgba(245,197,24,0.08)',
          borderWidth: 2,
          pointBackgroundColor: '#f5c518',
          pointRadius: 4,
          tension: 0.4,
          fill: true,
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#1a1a1a',
            borderColor: '#2a2a2a',
            borderWidth: 1,
            titleColor: '#f5c518',
            bodyColor: '#e8e8e8',
          }
        },
        scales: {
          x: { ticks: { color: '#888' }, grid: { color: '#1f1f1f' } },
          y: { ticks: { color: '#888', stepSize: 1 }, grid: { color: '#1f1f1f' } }
        }
      }
    });
  }
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
