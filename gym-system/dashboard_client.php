<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/controllers/users.php';
require_once __DIR__ . '/controllers/notifications.php';

requireClient();

$userId   = (int)$_SESSION['user_id'];
$profile  = getUserProfile($userId);
$notifs   = getUserNotifications($userId);
$payments = getUserPayments($userId);
$unread   = countUnread($userId);

$pageTitle = 'My Dashboard';
require_once __DIR__ . '/includes/header.php';
?>

<div class="gp-dash-layout">
  <!-- Sidebar -->
  <aside class="gp-sidebar d-none d-md-block">
    <div class="px-4 py-2 mb-2">
      <div class="d-flex align-items-center gap-3">
        <div class="gp-avatar" style="width:42px;height:42px;font-size:1rem">
          <?= strtoupper(substr($profile['name'] ?? 'U', 0, 1)) ?>
        </div>
        <div>
          <div style="font-weight:600;font-size:.9rem;color:#fff"><?= htmlspecialchars($profile['name'] ?? '') ?></div>
          <div style="font-size:.72rem;color:var(--gp-muted)"><?= htmlspecialchars($profile['plan_name'] ?? 'No Plan') ?></div>
        </div>
      </div>
    </div>

    <div class="gp-sidebar-section">Main</div>
    <a class="gp-sidebar-link active" href="/gym-system/dashboard_client.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a class="gp-sidebar-link" href="/gym-system/views/client/profile.php"><i class="bi bi-person"></i> Profile</a>
    <a class="gp-sidebar-link" href="/gym-system/views/client/digital_id.php"><i class="bi bi-credit-card-2-front"></i> Digital ID</a>

    <div class="gp-sidebar-section">Membership</div>
    <a class="gp-sidebar-link" href="/gym-system/views/client/subscription.php"><i class="bi bi-box-seam"></i> My Plan</a>
    <a class="gp-sidebar-link" href="/gym-system/views/client/payments.php"><i class="bi bi-receipt"></i> Payment History</a>

    <div class="gp-sidebar-section">Comms</div>
    <a class="gp-sidebar-link position-relative" href="/gym-system/views/client/notifications.php">
      <i class="bi bi-bell"></i> Notifications
      <?php if ($unread > 0): ?>
        <span class="ms-auto gp-status gp-status--expired" style="font-size:.65rem;padding:.1rem .45rem"><?= $unread ?></span>
      <?php endif; ?>
    </a>

    <div class="gp-sidebar-section">Account</div>
    <a class="gp-sidebar-link" href="/gym-system/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </aside>

  <!-- Content -->
  <main class="gp-dash-content">
    <div class="gp-dash-header">
      <div class="gp-dash-title">WELCOME BACK, <span class="text-yellow"><?= strtoupper(explode(' ', $profile['name'] ?? 'MEMBER')[0]) ?></span></div>
      <div style="color:var(--gp-muted);font-size:.85rem"><?= date('l, F j, Y') ?></div>
    </div>

    <!-- Stats row -->
    <div class="row g-3 mb-4">
      <div class="col-6 col-lg-3">
        <div class="gp-stat-card">
          <div class="gp-stat-icon"><i class="bi bi-box-seam"></i></div>
          <div class="gp-stat-num"><?= htmlspecialchars($profile['plan_name'] ?? '—') ?></div>
          <div class="gp-stat-label">Current Plan</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="gp-stat-card">
          <div class="gp-stat-icon"><i class="bi bi-calendar-check"></i></div>
          <div class="gp-stat-num"><?= $profile['end_date'] ? date('M d', strtotime($profile['end_date'])) : '—' ?></div>
          <div class="gp-stat-label">Plan Expires</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="gp-stat-card">
          <div class="gp-stat-icon"><i class="bi bi-receipt"></i></div>
          <div class="gp-stat-num"><?= count($payments) ?></div>
          <div class="gp-stat-label">Total Payments</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="gp-stat-card">
          <div class="gp-stat-icon"><i class="bi bi-bell"></i></div>
          <div class="gp-stat-num"><?= $unread ?></div>
          <div class="gp-stat-label">Unread Alerts</div>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <!-- Subscription card -->
      <div class="col-lg-5">
        <div class="gp-card h-100">
          <div style="font-family:'Bebas Neue',sans-serif;font-size:1.3rem;color:#fff;margin-bottom:1.25rem">
            My Membership
          </div>
          <?php if ($profile['plan_name']): ?>
            <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem">
              <div class="gp-stat-icon" style="margin:0"><i class="bi bi-lightning-charge-fill"></i></div>
              <div>
                <div style="font-family:'Bebas Neue',sans-serif;font-size:1.6rem;color:#fff"><?= htmlspecialchars($profile['plan_name']) ?></div>
                <span class="gp-status gp-status--<?= $profile['sub_status'] === 'active' ? 'active' : 'expired' ?>">
                  <?= ucfirst($profile['sub_status']) ?>
                </span>
              </div>
            </div>
            <div style="font-size:.83rem;color:var(--gp-muted);display:flex;flex-direction:column;gap:.4rem;margin-bottom:1.25rem">
              <div><i class="bi bi-calendar me-2 text-yellow"></i>Start: <?= date('F j, Y', strtotime($profile['start_date'])) ?></div>
              <div><i class="bi bi-calendar-x me-2 text-yellow"></i>Expires: <?= date('F j, Y', strtotime($profile['end_date'])) ?></div>
              <div><i class="bi bi-credit-card me-2 text-yellow"></i>
                Card: ****<?= $profile['card_number'] ? substr($profile['card_number'], -4) : 'N/A' ?>
              </div>
            </div>
            <?php
            $days = max(0, (int)ceil((strtotime($profile['end_date']) - time()) / 86400));
            $pct  = min(100, max(0, (int)(($days / 30) * 100)));
            ?>
            <div style="font-size:.75rem;color:var(--gp-muted);margin-bottom:.4rem">
              <?= $days ?> day<?= $days !== 1 ? 's' : '' ?> remaining
            </div>
            <div style="background:var(--gp-border);border-radius:3px;height:6px;overflow:hidden">
              <div style="width:<?= $pct ?>%;background:var(--gp-yellow);height:100%;transition:width .8s"></div>
            </div>
            <a href="/gym-system/views/client/subscription.php" class="gp-btn-outline mt-3 d-inline-block" style="font-size:.75rem">
              Change Plan
            </a>
          <?php else: ?>
            <div style="color:var(--gp-muted);font-size:.88rem;margin-bottom:1rem">No active subscription.</div>
            <a href="/gym-system/plans.php" class="gp-btn-primary">Browse Plans</a>
          <?php endif; ?>
        </div>
      </div>

      <!-- Recent notifications -->
      <div class="col-lg-7">
        <div class="gp-card h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div style="font-family:'Bebas Neue',sans-serif;font-size:1.3rem;color:#fff">Notifications</div>
            <a href="/gym-system/views/client/notifications.php" style="color:var(--gp-yellow);font-size:.8rem;text-decoration:none">
              View All <i class="bi bi-arrow-right"></i>
            </a>
          </div>
          <?php if (empty($notifs)): ?>
            <div style="color:var(--gp-muted);font-size:.88rem">No notifications yet.</div>
          <?php else: ?>
            <?php foreach (array_slice($notifs, 0, 4) as $n): ?>
            <div class="gp-notif-item <?= !$n['is_read'] ? 'unread' : '' ?>">
              <div class="d-flex justify-content-between align-items-start">
                <div class="gp-notif-title"><?= htmlspecialchars($n['title']) ?></div>
                <?php if (!$n['is_read']): ?>
                  <span class="gp-status gp-status--active ms-2" style="font-size:.65rem;flex-shrink:0">New</span>
                <?php endif; ?>
              </div>
              <div class="gp-notif-msg"><?= htmlspecialchars(substr($n['message'], 0, 90)) ?>...</div>
              <div class="gp-notif-date"><?= date('M j, Y · g:i a', strtotime($n['created_at'])) ?></div>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Recent payments table -->
    <div class="mt-4">
      <div class="gp-table-wrap">
        <div class="gp-table-head">
          <div class="gp-table-title">Recent Payments</div>
          <a href="/gym-system/views/client/payments.php" style="color:var(--gp-yellow);font-size:.8rem;text-decoration:none">View All</a>
        </div>
        <div class="table-responsive">
          <table class="table mb-0">
            <thead><tr>
              <th>Date</th><th>Plan</th><th>Card</th><th class="text-end">Amount</th>
            </tr></thead>
            <tbody>
              <?php if (empty($payments)): ?>
                <tr><td colspan="4" class="text-center py-4" style="color:var(--gp-muted)">No payment history.</td></tr>
              <?php else: ?>
                <?php foreach (array_slice($payments, 0, 5) as $p): ?>
                <tr>
                  <td><?= date('M j, Y', strtotime($p['payment_date'])) ?></td>
                  <td><?= htmlspecialchars($p['plan_name']) ?></td>
                  <td style="color:var(--gp-muted)"><?= htmlspecialchars($p['card_last4']) ?></td>
                  <td class="text-end text-yellow fw-semibold">$<?= number_format($p['amount'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
