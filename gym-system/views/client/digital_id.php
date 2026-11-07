<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../controllers/users.php';

requireClient();
$userId  = (int)$_SESSION['user_id'];
$profile = getUserProfile($userId);
$pageTitle = 'Digital ID';
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="gp-dash-layout">
  <aside class="gp-sidebar d-none d-md-block">
    <div class="gp-sidebar-section">Main</div>
    <a class="gp-sidebar-link" href="/gym-system/dashboard_client.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a class="gp-sidebar-link" href="/gym-system/views/client/profile.php"><i class="bi bi-person"></i> Profile</a>
    <a class="gp-sidebar-link active" href="/gym-system/views/client/digital_id.php"><i class="bi bi-credit-card-2-front"></i> Digital ID</a>
    <div class="gp-sidebar-section">Membership</div>
    <a class="gp-sidebar-link" href="/gym-system/views/client/subscription.php"><i class="bi bi-box-seam"></i> My Plan</a>
    <a class="gp-sidebar-link" href="/gym-system/views/client/payments.php"><i class="bi bi-receipt"></i> Payment History</a>
    <div class="gp-sidebar-section">Comms</div>
    <a class="gp-sidebar-link" href="/gym-system/views/client/notifications.php"><i class="bi bi-bell"></i> Notifications</a>
    <div class="gp-sidebar-section">Account</div>
    <a class="gp-sidebar-link" href="/gym-system/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </aside>
  <main class="gp-dash-content">
    <div class="gp-dash-header">
      <div class="gp-dash-title">DIGITAL <span class="text-yellow">ID</span></div>
      <div style="color:var(--gp-muted);font-size:.85rem">Show this at any GymPro branch to access facilities.</div>
    </div>

    <div class="d-flex flex-column align-items-center">
      <!-- ID CARD -->
      <div class="gp-id-card" id="idCard">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="gp-id-logo"><i class="bi bi-lightning-charge-fill me-1"></i>GYMP<span style="color:#fff">RO</span></div>
          <?php if ($profile['sub_status'] === 'active'): ?>
            <span class="gp-status gp-status--active" style="font-size:.7rem">Active</span>
          <?php else: ?>
            <span class="gp-status gp-status--expired" style="font-size:.7rem">Inactive</span>
          <?php endif; ?>
        </div>

        <div class="d-flex align-items-center gap-3 mb-3">
          <div class="gp-id-avatar"><?= strtoupper(substr($profile['name'],0,1)) ?></div>
          <div>
            <div class="gp-id-name"><?= htmlspecialchars(strtoupper($profile['name'])) ?></div>
            <div class="gp-id-plan"><?= htmlspecialchars($profile['plan_name'] ?? 'No Plan') ?></div>
            <div class="gp-id-detail mt-1">ID #<?= str_pad($profile['id'], 6, '0', STR_PAD_LEFT) ?></div>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem 1.5rem;margin-bottom:1rem">
          <div>
            <div style="font-size:.65rem;color:#555;text-transform:uppercase;letter-spacing:.08em">Member Since</div>
            <div style="font-size:.82rem;color:var(--gp-text)"><?= date('M Y', strtotime($profile['created_at'])) ?></div>
          </div>
          <div>
            <div style="font-size:.65rem;color:#555;text-transform:uppercase;letter-spacing:.08em">Valid Until</div>
            <div style="font-size:.82rem;color:var(--gp-text)">
              <?= $profile['end_date'] ? date('M d, Y', strtotime($profile['end_date'])) : 'N/A' ?>
            </div>
          </div>
        </div>

        <div class="gp-id-barcode" id="gp-barcode"></div>
        <div style="font-size:.65rem;color:#444;text-align:center;margin-top:.4rem">
          <?= str_repeat(rand(0,1) ? '|' : ' ', 3) ?> 
          <?= str_pad($profile['id'], 6, '0', STR_PAD_LEFT) ?>-<?= strtoupper(substr(md5($profile['email']),0,8)) ?>
        </div>
      </div>

      <?php if ($profile['sub_status'] !== 'active'): ?>
      <div class="mt-4 p-3 rounded d-flex gap-3 align-items-center"
           style="background:rgba(229,62,62,.1);border:1px solid #e53e3e;max-width:420px">
        <i class="bi bi-exclamation-triangle-fill" style="color:#e53e3e;font-size:1.2rem;flex-shrink:0"></i>
        <div>
          <div style="font-weight:600;color:#f87171;font-size:.9rem">Subscription Required</div>
          <div style="font-size:.82rem;color:var(--gp-muted)">Your ID is inactive. Subscribe to a plan to gain access.</div>
        </div>
      </div>
      <a href="/gym-system/plans.php" class="gp-btn-primary mt-3">View Plans</a>
      <?php endif; ?>

      <div class="mt-4 d-flex gap-3">
        <button onclick="window.print()" class="gp-btn-outline">
          <i class="bi bi-printer me-2"></i>Print ID
        </button>
      </div>
    </div>
  </main>
</div>
<style>
@media print {
  .gp-nav, .gp-sidebar, .gp-footer, .gp-dash-header, button, a:not(#idCard a) { display:none!important; }
  .gp-dash-layout { display:block; }
  .gp-dash-content { padding:0; }
  body { background:#fff; }
  .gp-id-card { border:2px solid #000; color:#000; background:#fff!important; box-shadow:none; }
}
</style>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
