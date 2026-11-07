<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../controllers/users.php';

requireClient();
$userId   = (int)$_SESSION['user_id'];
$payments = getUserPayments($userId);
$pageTitle = 'Payment History';
require_once __DIR__ . '/../../includes/header.php';

$total = array_sum(array_column($payments, 'amount'));
?>
<div class="gp-dash-layout">
  <aside class="gp-sidebar d-none d-md-block">
    <div class="gp-sidebar-section">Main</div>
    <a class="gp-sidebar-link" href="/gym-system/dashboard_client.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a class="gp-sidebar-link" href="/gym-system/views/client/profile.php"><i class="bi bi-person"></i> Profile</a>
    <a class="gp-sidebar-link" href="/gym-system/views/client/digital_id.php"><i class="bi bi-credit-card-2-front"></i> Digital ID</a>
    <div class="gp-sidebar-section">Membership</div>
    <a class="gp-sidebar-link" href="/gym-system/views/client/subscription.php"><i class="bi bi-box-seam"></i> My Plan</a>
    <a class="gp-sidebar-link active" href="/gym-system/views/client/payments.php"><i class="bi bi-receipt"></i> Payment History</a>
    <div class="gp-sidebar-section">Comms</div>
    <a class="gp-sidebar-link" href="/gym-system/views/client/notifications.php"><i class="bi bi-bell"></i> Notifications</a>
    <div class="gp-sidebar-section">Account</div>
    <a class="gp-sidebar-link" href="/gym-system/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </aside>
  <main class="gp-dash-content">
    <div class="gp-dash-header">
      <div class="gp-dash-title">PAYMENT <span class="text-yellow">HISTORY</span></div>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-sm-4">
        <div class="gp-stat-card">
          <div class="gp-stat-icon"><i class="bi bi-receipt"></i></div>
          <div class="gp-stat-num"><?= count($payments) ?></div>
          <div class="gp-stat-label">Total Payments</div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="gp-stat-card">
          <div class="gp-stat-icon"><i class="bi bi-cash-stack"></i></div>
          <div class="gp-stat-num" data-count="<?= $total ?>" data-float="1" data-prefix="$">$0.00</div>
          <div class="gp-stat-label">Total Spent</div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="gp-stat-card">
          <div class="gp-stat-icon"><i class="bi bi-calendar-month"></i></div>
          <div class="gp-stat-num">
            $<?= count($payments) > 0 ? number_format($total / count($payments), 2) : '0.00' ?>
          </div>
          <div class="gp-stat-label">Avg per Payment</div>
        </div>
      </div>
    </div>

    <div class="gp-table-wrap">
      <div class="gp-table-head">
        <div class="gp-table-title">All Payments</div>
        <input type="text" id="tableSearch" class="form-control" placeholder="Search…"
               style="width:220px;font-size:.83rem;padding:.4rem .75rem">
      </div>
      <div class="table-responsive">
        <table class="table mb-0" data-searchable>
          <thead><tr>
            <th>#</th><th>Date</th><th>Plan</th><th>Card</th><th class="text-end">Amount</th>
          </tr></thead>
          <tbody>
            <?php if (empty($payments)): ?>
              <tr><td colspan="5" class="text-center py-5" style="color:var(--gp-muted)">
                <i class="bi bi-receipt fs-2 d-block mb-2"></i>No payments yet.
              </td></tr>
            <?php else: ?>
              <?php foreach ($payments as $i => $p): ?>
              <tr>
                <td style="color:var(--gp-muted)">#<?= $p['id'] ?></td>
                <td><?= date('M j, Y', strtotime($p['payment_date'])) ?></td>
                <td><span class="gp-status gp-status--client"><?= htmlspecialchars($p['plan_name']) ?></span></td>
                <td style="color:var(--gp-muted)"><?= htmlspecialchars($p['card_last4']) ?></td>
                <td class="text-end fw-semibold text-yellow">$<?= number_format($p['amount'],2) ?></td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
