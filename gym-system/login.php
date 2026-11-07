<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/controllers/auth.php';

if (isLoggedIn()) {
    header('Location: /gym-system/' . (isAdmin() ? 'dashboard_admin.php' : 'dashboard_client.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $result   = loginUser($email, $password);
    if ($result['success']) {
        header('Location: /gym-system/' . ($result['role'] === 'admin' ? 'dashboard_admin.php' : 'dashboard_client.php'));
        exit;
    }
    $error = $result['message'];
}

$pageTitle = 'Login';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container d-flex align-items-center justify-content-center" style="min-height:calc(100vh - 140px);padding:3rem 1rem">
  <div class="w-100" style="max-width:460px">

    <div class="text-center mb-4">
      <div class="gp-logo fs-2 mb-2"><i class="bi bi-lightning-charge-fill"></i> GYMP<span>RO</span></div>
      <p style="color:var(--gp-muted);font-size:.9rem">Sign in to your account</p>
    </div>

    <div class="gp-form-wrapper">
      <?php if ($error): ?>
        <div class="alert alert-danger gp-alert mb-3">
          <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" novalidate>
        <div class="mb-3">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-control"
                 placeholder="you@example.com"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
        </div>
        <div class="mb-4">
          <label class="form-label">Password</label>
          <div class="position-relative">
            <input type="password" name="password" id="pwField" class="form-control"
                   placeholder="••••••••" required>
            <button type="button" onclick="togglePw()" tabindex="-1"
                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--gp-muted);cursor:pointer">
              <i class="bi bi-eye" id="pwEye"></i>
            </button>
          </div>
        </div>
        <button type="submit" class="gp-btn-primary w-100 py-2" style="font-size:.95rem">
          Sign In <i class="bi bi-arrow-right ms-2"></i>
        </button>
      </form>

      <p class="text-center mt-4 mb-0" style="font-size:.85rem;color:var(--gp-muted)">
        Don't have an account?
        <a href="/gym-system/register.php" style="color:var(--gp-yellow);text-decoration:none;font-weight:600">Join GymPro</a>
      </p>
    </div>

    
    <!-- Demo credentials 
    <div class="mt-4 p-3 rounded" style="background:var(--gp-surface);border:1px solid var(--gp-border);font-size:.8rem">
      <div style="color:var(--gp-muted);text-transform:uppercase;letter-spacing:.08em;font-size:.7rem;margin-bottom:.6rem">
        <i class="bi bi-info-circle me-1 text-yellow"></i> Demo Credentials
      </div>
      <div class="d-flex gap-3 flex-wrap">
        <div>
          <span style="color:#555">Admin:</span>
          <code style="color:var(--gp-yellow)">admin@gym.com</code> /
          <code style="color:var(--gp-yellow)">password</code>
        </div>
        <div>
          <span style="color:#555">Client:</span>
          <code style="color:var(--gp-yellow)">john@email.com</code> /
          <code style="color:var(--gp-yellow)">password</code>
        </div>
      </div>
    </div> -->
  </div>
</div>

<script>
function togglePw() {
  const f = document.getElementById('pwField');
  const e = document.getElementById('pwEye');
  if (f.type === 'password') { f.type = 'text';     e.className = 'bi bi-eye-slash'; }
  else                        { f.type = 'password'; e.className = 'bi bi-eye'; }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
