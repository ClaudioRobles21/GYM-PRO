<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../controllers/plans.php';
require_once __DIR__ . '/../../controllers/users.php';

requireClient();
$userId = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $planId = (int)($_POST['plan_id'] ?? 0);
    if ($planId > 0) {
        $result = subscribeToPlan($userId, $planId);
        flashMessage($result['success'] ? 'success' : 'danger', $result['message']);
    }
    header('Location: /gym-system/views/client/subscription.php');
    exit;
}

$plans   = getActivePlans();
$profile = getUserProfile($userId);
$pageTitle = 'My Subscription';
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="gp-dash-layout">
  <aside class="gp-sidebar d-none d-md-block">
    <div class="gp-sidebar-section">Main</div>
    <a class="gp-sidebar-link" href="/gym-system/dashboard_client.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a class="gp-sidebar-link" href="/gym-system/views/client/profile.php"><i class="bi bi-person"></i> Profile</a>
    <a class="gp-sidebar-link" href="/gym-system/views/client/digital_id.php"><i class="bi bi-credit-card-2-front"></i> Digital ID</a>
    <div class="gp-sidebar-section">Membership</div>
    <a class="gp-sidebar-link active" href="/gym-system/views/client/subscription.php"><i class="bi bi-box-seam"></i> My Plan</a>
    <a class="gp-sidebar-link" href="/gym-system/views/client/payments.php"><i class="bi bi-receipt"></i> Payment History</a>
    <div class="gp-sidebar-section">Comms</div>
    <a class="gp-sidebar-link" href="/gym-system/views/client/notifications.php"><i class="bi bi-bell"></i> Notifications</a>
    <div class="gp-sidebar-section">Account</div>
    <a class="gp-sidebar-link" href="/gym-system/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </aside>
  <main class="gp-dash-content">
    <div class="gp-dash-header">
      <div class="gp-dash-title">MY <span class="text-yellow">SUBSCRIPTION</span></div>
    </div>

    <!-- Current plan -->
    <?php if ($profile['plan_name']): ?>
    <div class="gp-card mb-5" style="max-width:500px">
      <div style="font-family:'Bebas Neue',sans-serif;font-size:1.1rem;color:var(--gp-muted);margin-bottom:.5rem">Current Plan</div>
      <div style="font-family:'Bebas Neue',sans-serif;font-size:2rem;color:#fff"><?= htmlspecialchars($profile['plan_name']) ?></div>
      <div class="d-flex gap-4 mt-2" style="font-size:.83rem;color:var(--gp-muted)">
        <span><i class="bi bi-calendar me-1 text-yellow"></i><?= date('M j, Y', strtotime($profile['start_date'])) ?></span>
        <span><i class="bi bi-calendar-x me-1 text-yellow"></i><?= date('M j, Y', strtotime($profile['end_date'])) ?></span>
      </div>
      <span class="gp-status gp-status--<?= $profile['sub_status'] === 'active' ? 'active':'expired' ?> mt-2 d-inline-block">
        <?= ucfirst($profile['sub_status']) ?>
      </span>
    </div>
    <?php endif; ?>

    <div style="font-family:'Bebas Neue',sans-serif;font-size:1.5rem;color:#fff;margin-bottom:1.25rem">
      <?= $profile['plan_name'] ? 'CHANGE PLAN' : 'CHOOSE A PLAN' ?>
    </div>

    <div class="row g-4">
      <?php foreach ($plans as $i => $plan): ?>
      <div class="col-md-6 col-lg-4">
        <div class="gp-plan-card <?= $i===1?'featured':'' ?> <?= $profile['plan_id']==$plan['id']?'border-yellow':'' ?> h-100 d-flex flex-column">
          <?php if ($profile['plan_id'] == $plan['id']): ?>
            <div class="gp-plan-badge">Current</div>
          <?php elseif ($i===1): ?>
            <div class="gp-plan-badge">Popular</div>
          <?php endif; ?>
          <div style="color:var(--gp-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.1em"><?= htmlspecialchars($plan['name']) ?></div>
          <div class="gp-plan-price">$<?= number_format($plan['price'],0) ?></div>
          <div class="gp-plan-period">/ month</div>
          <ul class="gp-plan-benefits flex-grow-1 mt-3">
            <?php foreach ($plan['benefits_list'] as $b): ?>
              <li><i class="bi bi-check2"></i><?= htmlspecialchars($b) ?></li>
            <?php endforeach; ?>
          </ul>
          <div class="mt-auto pt-3">
            <?php if ($profile['plan_id'] == $plan['id']): ?>
              <button class="gp-btn-outline w-100" disabled style="opacity:.5;cursor:not-allowed">Active Plan</button>
            <?php else: ?>
              <form method="POST">
                <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">
                <button type="submit" class="gp-btn-primary w-100"
                        data-confirm="Switch to <?= htmlspecialchars($plan['name']) ?> plan for $<?= number_format($plan['price'],0) ?>/month?">
                  Select Plan
                </button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </main>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
