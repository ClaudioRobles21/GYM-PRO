<?php // includes/footer.php ?>
<footer class="gp-footer mt-auto">
  <div class="container">
    <div class="row py-5 gy-4">
      <div class="col-lg-4">
        <div class="gp-logo mb-3 fs-3">
          <i class="bi bi-lightning-charge-fill"></i> GYMP<span>RO</span>
        </div>
        <p class="text-muted small">Your elite fitness destination. Train harder, live better.</p>
        <div class="d-flex gap-3 mt-3">
          <a href="#" class="gp-social"><i class="bi bi-instagram"></i></a>
          <a href="#" class="gp-social"><i class="bi bi-facebook"></i></a>
          <a href="#" class="gp-social"><i class="bi bi-tiktok"></i></a>
          <a href="#" class="gp-social"><i class="bi bi-twitter-x"></i></a>
        </div>
      </div>
      <div class="col-lg-2 col-6">
        <h6 class="gp-footer-heading">Navigate</h6>
        <ul class="list-unstyled">
          <li><a href="/gym-system/index.php" class="gp-footer-link">Home</a></li>
          <li><a href="/gym-system/plans.php" class="gp-footer-link">Plans</a></li>
          <li><a href="/gym-system/branches.php" class="gp-footer-link">Branches</a></li>
        </ul>
      </div>
      <div class="col-lg-2 col-6">
        <h6 class="gp-footer-heading">Account</h6>
        <ul class="list-unstyled">
          <li><a href="/gym-system/login.php" class="gp-footer-link">Login</a></li>
          <li><a href="/gym-system/register.php" class="gp-footer-link">Register</a></li>
        </ul>
      </div>
      <div class="col-lg-4">
        <h6 class="gp-footer-heading">Contact</h6>
        <p class="text-muted small mb-1"><i class="bi bi-geo-alt me-2 text-warning"></i>Av. Constitución 1500, Monterrey</p>
        <p class="text-muted small mb-1"><i class="bi bi-telephone me-2 text-warning"></i>818 300 1000</p>
        <p class="text-muted small"><i class="bi bi-envelope me-2 text-warning"></i>info@gympro.com</p>
      </div>
    </div>
    <hr class="border-secondary">
    <p class="text-center text-muted small py-3 mb-0">
      &copy; <?= date('Y') ?> GymPro. All rights reserved.
    </p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/gym-system/public/js/main.js"></script>
</body>
</html>
