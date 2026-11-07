<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/controllers/plans.php';
require_once __DIR__ . '/controllers/branches.php';

if (isLoggedIn()) {
    header('Location: /gym-system/' . (isAdmin() ? 'dashboard_admin.php' : 'dashboard_client.php'));
    exit;
}

$plans    = getActivePlans();
$branches = getActiveBranches();
$pageTitle = 'Home';
require_once __DIR__ . '/includes/header.php';
?>

<!-- HERO -->
<section class="gp-hero">
  <div class="gp-hero-accent">LIFT</div>
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-7">
        <div class="gp-hero-tag"><i class="bi bi-lightning-charge-fill me-1"></i> #1 Gym in Monterrey</div>
        <h1 class="gp-hero-title">PUSH YOUR<br><span style="color:var(--gp-yellow)">LIMITS</span><br>EVERY DAY</h1>
        <p class="gp-hero-sub">State-of-the-art equipment, expert trainers, and a community that pushes you forward. Join GymPro today.</p>
        <div class="d-flex gap-3 flex-wrap">
          <a href="/gym-system/register.php" class="gp-btn-primary">Start Today <i class="bi bi-arrow-right ms-1"></i></a>
          <a href="/gym-system/plans.php" class="gp-btn-outline">View Plans</a>
        </div>
        <div class="gp-hero-stats">
          <div>
            <div class="gp-hero-stat-num" data-count="2400">0</div>
            <div class="gp-hero-stat-label">Active Members</div>
          </div>
          <div>
            <div class="gp-hero-stat-num" data-count="<?= count($branches) ?>">0</div>
            <div class="gp-hero-stat-label">Branches</div>
          </div>
          <div>
            <div class="gp-hero-stat-num" data-count="15">0</div>
            <div class="gp-hero-stat-label">Years Open</div>
          </div>
          <div>
            <div class="gp-hero-stat-num" data-count="48">0</div>
            <div class="gp-hero-stat-label">Weekly Classes</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- WHY US -->
<section class="gp-section gp-section-dark">
  <div class="container">
    <div class="text-center mb-5">
      <span class="gp-overline">Why GymPro</span>
      <h2 class="gp-section-title">BUILT FOR <span class="text-yellow">ATHLETES</span></h2>
    </div>
    <div class="row g-4">
      <?php
      $features = [
        ['bi-lightning-charge-fill','High-Intensity Training','HIIT, CrossFit, and strength programs designed by certified coaches to maximize your results.'],
        ['bi-people-fill','Expert Trainers','Our team of certified professionals creates personalized plans to match your goals and fitness level.'],
        ['bi-clock-fill','Open 24/7','Select branches offer round-the-clock access so you can train on your schedule, not ours.'],
        ['bi-shield-check-fill','Digital Access','Your digital membership ID lets you walk into any branch with ease — no card, no hassle.'],
      ];
      foreach ($features as [$icon,$title,$desc]):
      ?>
      <div class="col-md-6 col-lg-3">
        <div class="gp-card h-100">
          <div class="gp-stat-icon mb-3"><i class="bi <?= $icon ?>"></i></div>
          <h5 style="font-family:'Bebas Neue',sans-serif;font-size:1.3rem;color:#fff;margin-bottom:.5rem"><?= $title ?></h5>
          <p style="color:var(--gp-muted);font-size:.85rem;margin:0"><?= $desc ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- PLANS PREVIEW -->
<section class="gp-section">
  <div class="container">
    <div class="text-center mb-5">
      <span class="gp-overline">Membership</span>
      <h2 class="gp-section-title">CHOOSE YOUR <span class="text-yellow">PLAN</span></h2>
      <p style="color:var(--gp-muted);max-width:500px;margin:0.75rem auto 0">All plans include locker access, free parking, and our mobile app.</p>
    </div>
    <div class="row g-4 justify-content-center">
      <?php foreach ($plans as $i => $plan): ?>
      <div class="col-md-6 col-lg-4">
        <div class="gp-plan-card <?= $i === 1 ? 'featured' : '' ?>">
          <?php if ($i === 1): ?><div class="gp-plan-badge">Most Popular</div><?php endif; ?>
          <div style="color:var(--gp-muted);font-size:.8rem;text-transform:uppercase;letter-spacing:.1em;margin-bottom:.75rem">
            <?= htmlspecialchars($plan['name']) ?>
          </div>
          <div class="gp-plan-price">$<?= number_format($plan['price'],0) ?></div>
          <div class="gp-plan-period">/ month</div>
          <p style="color:var(--gp-muted);font-size:.83rem;margin:1rem 0"><?= htmlspecialchars($plan['description']) ?></p>
          <ul class="gp-plan-benefits">
            <?php foreach ($plan['benefits_list'] as $b): ?>
              <li><i class="bi bi-check2"></i><?= htmlspecialchars($b) ?></li>
            <?php endforeach; ?>
          </ul>
          <a href="/gym-system/register.php?plan=<?= $plan['id'] ?>" class="gp-btn-primary w-100 text-center d-block">Get Started</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-4">
      <a href="/gym-system/plans.php" class="gp-btn-outline">Compare All Plans</a>
    </div>
  </div>
</section>

<!-- BRANCHES PREVIEW -->
<section class="gp-section gp-section-dark">
  <div class="container">
    <div class="text-center mb-5">
      <span class="gp-overline">Locations</span>
      <h2 class="gp-section-title">OUR <span class="text-yellow">BRANCHES</span></h2>
    </div>
    <div class="row g-4">
      <?php foreach ($branches as $branch): ?>
      <div class="col-md-6 col-lg-6">
        <div class="gp-branch-card">
          <div class="gp-branch-img"><i class="bi bi-building"></i></div>
          <div class="gp-branch-body">
            <div class="gp-branch-name"><?= htmlspecialchars($branch['name']) ?></div>
            <div class="gp-branch-detail"><i class="bi bi-geo-alt"></i><?= htmlspecialchars($branch['address']) ?>, <?= htmlspecialchars($branch['city']) ?></div>
            <?php if ($branch['phone']): ?>
            <div class="gp-branch-detail"><i class="bi bi-telephone"></i><?= htmlspecialchars($branch['phone']) ?></div>
            <?php endif; ?>
            <?php if ($branch['schedule']): ?>
            <div class="gp-branch-detail"><i class="bi bi-clock"></i><?= htmlspecialchars($branch['schedule']) ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-4">
      <a href="/gym-system/branches.php" class="gp-btn-outline">All Branches</a>
    </div>
  </div>
</section>

<!-- CTA STRIP -->
<section style="background:var(--gp-yellow);padding:4rem 0">
  <div class="container text-center">
    <h2 style="font-family:'Bebas Neue',sans-serif;font-size:3rem;color:var(--gp-black);margin-bottom:.5rem">READY TO START?</h2>
    <p style="color:rgba(0,0,0,.6);margin-bottom:1.75rem">First month 20% off for new members. No contract. Cancel anytime.</p>
    <a href="/gym-system/register.php" class="gp-btn-primary" style="background:var(--gp-black);color:#fff !important;font-size:.95rem;padding:.8rem 2.5rem">
      Create Free Account <i class="bi bi-arrow-right ms-2"></i>
    </a>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
