<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/controllers/auth.php';
require_once __DIR__ . '/controllers/plans.php';

if (isLoggedIn()) {
    header('Location: /gym-system/' . (isAdmin() ? 'dashboard_admin.php' : 'dashboard_client.php'));
    exit;
}

$plans      = getActivePlans();
$preselect  = (int)($_GET['plan'] ?? 0);
$errors     = [];
$formData   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = $_POST;
    $result   = registerUser($_POST);
    if ($result['success']) {
        flashMessage('success', $result['message'] . ' Please sign in.');
        header('Location: /gym-system/login.php');
        exit;
    }
    $errors[] = $result['message'];
}

$pageTitle = 'Join GymPro';
require_once __DIR__ . '/includes/header.php';
?>

<div class="gp-page-hero">
  <div class="container">
    <span class="gp-overline">Get Started</span>
    <h1>CREATE YOUR ACCOUNT</h1>
    <p style="color:var(--gp-muted);margin-top:.5rem">Join thousands of members and start your fitness journey today.</p>
  </div>
</div>

<div class="container py-5">
  <?php if ($errors): ?>
    <div class="alert alert-danger gp-alert mb-4" style="max-width:720px;margin:0 auto 1.5rem">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <?= htmlspecialchars(implode(' ', $errors)) ?>
    </div>
  <?php endif; ?>

  <form method="POST" novalidate>
    <div class="row g-4" style="max-width:900px;margin:0 auto">

      <!-- LEFT: personal info -->
      <div class="col-lg-6">
        <div class="gp-form-wrapper" style="max-width:100%">
          <div class="gp-form-title mb-1">Personal Info</div>
          <p style="color:var(--gp-muted);font-size:.83rem;margin-bottom:1.5rem">Your basic information to set up the account.</p>

          <div class="mb-3">
            <label class="form-label">Full Name *</label>
            <input type="text" name="name" class="form-control" placeholder="John Doe"
                   value="<?= htmlspecialchars($formData['name'] ?? '') ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email *</label>
            <input type="email" name="email" class="form-control" placeholder="you@example.com"
                   value="<?= htmlspecialchars($formData['email'] ?? '') ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password *</label>
            <input type="password" name="password" class="form-control"
                   placeholder="Min. 6 characters" required minlength="6">
          </div>
          <div class="row g-3">
            <div class="col-6">
              <label class="form-label">Date of Birth *</label>
              <input type="date" name="birth_date" class="form-control"
                     value="<?= htmlspecialchars($formData['birth_date'] ?? '') ?>" required>
            </div>
            <div class="col-6">
              <label class="form-label">Phone</label>
              <input type="tel" name="phone" class="form-control" placeholder="818 000 0000"
                     value="<?= htmlspecialchars($formData['phone'] ?? '') ?>">
            </div>
          </div>

          <div style="border-top:1px solid var(--gp-border);margin:1.5rem 0;padding-top:1.5rem">
            <div class="gp-form-title mb-1" style="font-size:1.4rem">Payment Card</div>
            <p style="color:var(--gp-muted);font-size:.83rem;margin-bottom:1rem">Optional – required to subscribe to a plan.</p>
            <div class="mb-3">
              <label class="form-label">Card Number</label>
              <input type="text" name="card_number" class="form-control" maxlength="16"
                     placeholder="16 digits" value="<?= htmlspecialchars($formData['card_number'] ?? '') ?>">
            </div>
            <div class="row g-3">
              <div class="col-6">
                <label class="form-label">CVV</label>
                <input type="text" name="cvv" class="form-control" maxlength="4"
                       placeholder="123" value="<?= htmlspecialchars($formData['cvv'] ?? '') ?>">
              </div>
              <div class="col-6">
                <label class="form-label">Expiry Date</label>
                <input type="date" name="expiry_date" class="form-control"
                       value="<?= htmlspecialchars($formData['expiry_date'] ?? '') ?>">
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- RIGHT: plan selection -->
      <div class="col-lg-6">
        <div style="color:var(--gp-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.1em;margin-bottom:1rem">
          Select a Plan (optional)
        </div>
        <div class="d-flex flex-column gap-3">
          <?php foreach ($plans as $i => $plan): ?>
          <label class="gp-plan-select-card <?= ($preselect === $plan['id'] || ($preselect === 0 && $i === 1)) ? 'selected' : '' ?>"
                 style="cursor:pointer">
            <input type="radio" name="plan_id" value="<?= $plan['id'] ?>"
                   <?= ($preselect === $plan['id'] || ($preselect === 0 && $i === 1)) ? 'checked' : '' ?>
                   style="display:none">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div style="font-family:'Bebas Neue',sans-serif;font-size:1.3rem;color:#fff"><?= htmlspecialchars($plan['name']) ?></div>
                <div style="color:var(--gp-muted);font-size:.8rem;margin-top:.2rem">
                  <?= implode(' · ', array_slice($plan['benefits_list'], 0, 2)) ?>
                </div>
              </div>
              <div style="text-align:right;flex-shrink:0;margin-left:1rem">
                <div style="font-family:'Bebas Neue',sans-serif;font-size:1.8rem;color:var(--gp-yellow)">
                  $<?= number_format($plan['price'], 0) ?>
                </div>
                <div style="color:var(--gp-muted);font-size:.75rem">/month</div>
              </div>
            </div>
          </label>
          <?php endforeach; ?>

          <label class="gp-plan-select-card" style="cursor:pointer">
            <input type="radio" name="plan_id" value="" style="display:none">
            <div style="font-family:'Bebas Neue',sans-serif;font-size:1.1rem;color:var(--gp-muted)">
              No plan for now — register only
            </div>
          </label>
        </div>

        <button type="submit" class="gp-btn-primary w-100 mt-4 py-2" style="font-size:1rem">
          <i class="bi bi-person-plus me-2"></i>Create Account
        </button>
        <p class="text-center mt-3" style="font-size:.83rem;color:var(--gp-muted)">
          Already a member?
          <a href="/gym-system/login.php" style="color:var(--gp-yellow);text-decoration:none;font-weight:600">Sign In</a>
        </p>
      </div>
    </div>
  </form>
</div>

<style>
.gp-plan-select-card {
  background: var(--gp-surface);
  border: 1.5px solid var(--gp-border);
  border-radius: 10px;
  padding: 1.1rem 1.4rem;
  transition: border-color .2s, background .2s;
  display: block;
}
.gp-plan-select-card:hover { border-color: #555; }
.gp-plan-select-card.selected {
  border-color: var(--gp-yellow);
  background: rgba(245,197,24,0.05);
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
