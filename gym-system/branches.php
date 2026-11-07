<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/controllers/branches.php';

$branches  = getActiveBranches();
$pageTitle = 'Branches';
require_once __DIR__ . '/includes/header.php';
?>

<div class="gp-page-hero">
  <div class="container">
    <span class="gp-overline">Locations</span>
    <h1>OUR <span class="text-yellow">BRANCHES</span></h1>
    <p style="color:var(--gp-muted);margin-top:.5rem">Find a GymPro location near you and train anytime.</p>
  </div>
</div>

<div class="gp-section">
  <div class="container">
    <?php if (empty($branches)): ?>
      <div class="text-center py-5" style="color:var(--gp-muted)">
        <i class="bi bi-geo-alt fs-1 d-block mb-3"></i>
        No branches available yet.
      </div>
    <?php else: ?>
      <div class="row g-4">
        <?php foreach ($branches as $b): ?>
        <div class="col-md-6 col-lg-4">
          <div class="gp-branch-card h-100">
            <div class="gp-branch-img" style="font-size:4rem;color:rgba(245,197,24,.15)">
              <i class="bi bi-building"></i>
            </div>
            <div class="gp-branch-body">
              <div class="gp-branch-name"><?= htmlspecialchars($b['name']) ?></div>
              <div class="mt-3 d-flex flex-column gap-1">
                <div class="gp-branch-detail">
                  <i class="bi bi-geo-alt-fill"></i>
                  <span><?= htmlspecialchars($b['address']) ?>, <?= htmlspecialchars($b['city']) ?></span>
                </div>
                <?php if ($b['phone']): ?>
                <div class="gp-branch-detail">
                  <i class="bi bi-telephone-fill"></i>
                  <span><?= htmlspecialchars($b['phone']) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($b['schedule']): ?>
                <div class="gp-branch-detail">
                  <i class="bi bi-clock-fill"></i>
                  <span><?= htmlspecialchars($b['schedule']) ?></span>
                </div>
                <?php endif; ?>
              </div>
              <div class="mt-3 pt-3" style="border-top:1px solid var(--gp-border)">
                <a href="https://maps.google.com/?q=<?= urlencode($b['address'].','.$b['city']) ?>"
                   target="_blank" class="gp-btn-outline" style="font-size:.75rem;padding:.4rem 1rem">
                  <i class="bi bi-map me-1"></i> Get Directions
                </a>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- CTA -->
<div class="gp-section gp-section-dark">
  <div class="container text-center">
    <h2 style="font-family:'Bebas Neue',sans-serif;font-size:2.5rem;color:#fff">
      READY TO TRAIN AT ANY BRANCH?
    </h2>
    <p style="color:var(--gp-muted);margin:.5rem 0 1.5rem">Your GymPro membership gives you access to all locations.</p>
    <a href="/gym-system/register.php" class="gp-btn-primary">Join Now</a>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
