<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/controllers/plans.php';

$plans     = getActivePlans();
$pageTitle = 'Plans';
require_once __DIR__ . '/includes/header.php';
?>

<div class="gp-page-hero">
  <div class="container">
    <span class="gp-overline">Membership</span>
    <h1>CHOOSE YOUR <span class="text-yellow">PLAN</span></h1>
    <p style="color:var(--gp-muted);margin-top:.5rem">Flexible options for every goal and budget. No contracts, cancel anytime.</p>
  </div>
</div>

<div class="gp-section">
  <div class="container">
    <div class="row g-4 justify-content-center">
      <?php foreach ($plans as $i => $plan): ?>
      <div class="col-md-6 col-lg-4">
        <div class="gp-plan-card <?= $i === 1 ? 'featured' : '' ?> h-100 d-flex flex-column">
          <?php if ($i === 1): ?><div class="gp-plan-badge">Most Popular</div><?php endif; ?>
          <div style="color:var(--gp-muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.12em;margin-bottom:.5rem">
            <?= htmlspecialchars($plan['name']) ?>
          </div>
          <div class="gp-plan-price">$<?= number_format($plan['price'], 0) ?></div>
          <div class="gp-plan-period">per month · <?= $plan['duration_months'] ?> month<?= $plan['duration_months'] > 1 ? 's' : '' ?></div>
          <p style="color:var(--gp-muted);font-size:.85rem;margin:1.25rem 0"><?= htmlspecialchars($plan['description']) ?></p>
          <ul class="gp-plan-benefits flex-grow-1">
            <?php foreach ($plan['benefits_list'] as $b): ?>
              <li><i class="bi bi-check2-circle"></i><?= htmlspecialchars($b) ?></li>
            <?php endforeach; ?>
          </ul>
          <div class="mt-auto pt-3">
            <?php if (isClient()): ?>
              <form action="/gym-system/views/client/subscribe.php" method="POST">
                <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">
                <button type="submit" class="gp-btn-primary w-100 text-center d-block">
                  Subscribe Now
                </button>
              </form>
            <?php else: ?>
              <a href="/gym-system/register.php?plan=<?= $plan['id'] ?>" class="gp-btn-primary w-100 text-center d-block">
                Get Started
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Comparison table -->
    <div class="mt-5">
      <h3 style="font-family:'Bebas Neue',sans-serif;font-size:2rem;color:#fff;text-align:center;margin-bottom:2rem">
        FULL COMPARISON
      </h3>
      <?php
      $allBenefits = [];
      foreach ($plans as $p) {
          foreach ($p['benefits_list'] as $b) {
              if (!in_array($b, $allBenefits)) $allBenefits[] = $b;
          }
      }
      ?>
      <div class="gp-table-wrap">
        <div class="table-responsive">
          <table class="table text-center mb-0">
            <thead>
              <tr>
                <th class="text-start">Feature</th>
                <?php foreach ($plans as $p): ?>
                  <th><?= htmlspecialchars($p['name']) ?></th>
                <?php endforeach; ?>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="text-start fw-semibold" style="color:var(--gp-muted);font-size:.8rem;text-transform:uppercase">Monthly Price</td>
                <?php foreach ($plans as $p): ?>
                  <td style="color:var(--gp-yellow);font-family:'Bebas Neue',sans-serif;font-size:1.2rem">$<?= number_format($p['price'],0) ?></td>
                <?php endforeach; ?>
              </tr>
              <?php foreach ($allBenefits as $benefit): ?>
              <tr>
                <td class="text-start" style="font-size:.87rem"><?= htmlspecialchars($benefit) ?></td>
                <?php foreach ($plans as $p): ?>
                  <td>
                    <?php if (in_array($benefit, $p['benefits_list'])): ?>
                      <i class="bi bi-check-circle-fill text-yellow"></i>
                    <?php else: ?>
                      <i class="bi bi-x-circle" style="color:#333"></i>
                    <?php endif; ?>
                  </td>
                <?php endforeach; ?>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
