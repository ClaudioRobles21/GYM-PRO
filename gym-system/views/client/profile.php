<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../controllers/users.php';

requireClient();
$userId  = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = updateClientProfile($userId, $_POST);
    flashMessage($result['success'] ? 'success' : 'danger', $result['message']);
    header('Location: /gym-system/views/client/profile.php');
    exit;
}

$profile   = getUserProfile($userId);
$pageTitle = 'My Profile';
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="gp-dash-layout">
  <aside class="gp-sidebar d-none d-md-block">
    <div class="gp-sidebar-section">Main</div>
    <a class="gp-sidebar-link" href="/gym-system/dashboard_client.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a class="gp-sidebar-link active" href="/gym-system/views/client/profile.php"><i class="bi bi-person"></i> Profile</a>
    <a class="gp-sidebar-link" href="/gym-system/views/client/digital_id.php"><i class="bi bi-credit-card-2-front"></i> Digital ID</a>
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
      <div class="gp-dash-title">MY <span class="text-yellow">PROFILE</span></div>
    </div>
    <div style="max-width:560px">
      <div class="gp-form-wrapper" style="max-width:100%">
        <div class="d-flex align-items-center gap-3 mb-4">
          <div class="gp-avatar" style="width:60px;height:60px;font-size:1.6rem">
            <?= strtoupper(substr($profile['name'],0,1)) ?>
          </div>
          <div>
            <div style="font-family:'Bebas Neue',sans-serif;font-size:1.5rem;color:#fff"><?= htmlspecialchars($profile['name']) ?></div>
            <div style="font-size:.8rem;color:var(--gp-muted)"><?= htmlspecialchars($profile['email']) ?></div>
          </div>
        </div>
        <form method="POST">
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($profile['name']) ?>" required>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-6">
              <label class="form-label">Phone</label>
              <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>">
            </div>
            <div class="col-6">
              <label class="form-label">Date of Birth</label>
              <input type="date" name="birth_date" class="form-control" value="<?= htmlspecialchars($profile['birth_date'] ?? '') ?>">
            </div>
          </div>
          <div class="mb-4">
            <label class="form-label">New Password <span style="color:#555">(leave blank to keep current)</span></label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" minlength="6">
          </div>
          <div class="d-flex gap-3">
            <button type="submit" class="gp-btn-primary">Save Changes</button>
            <a href="/gym-system/dashboard_client.php" class="gp-btn-outline">Cancel</a>
          </div>
        </form>
      </div>

      <!-- Member since info -->
      <div class="mt-3 p-3 rounded" style="background:var(--gp-surface);border:1px solid var(--gp-border);font-size:.82rem">
        <div style="color:var(--gp-muted)">
          <i class="bi bi-calendar2 me-2 text-yellow"></i>
          Member since: <strong style="color:#fff"><?= date('F Y', strtotime($profile['created_at'])) ?></strong>
        </div>
      </div>
    </div>
  </main>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
