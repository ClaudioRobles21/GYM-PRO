// public/js/main.js

// ── Auto-dismiss flash alerts ──────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(a => setTimeout(() => {
    const bsAlert = bootstrap.Alert.getOrCreateInstance(a);
    bsAlert.close();
  }, 5000));

  // ── Animate stat numbers ─────────────────────────────────
  document.querySelectorAll('[data-count]').forEach(el => {
    const target = parseFloat(el.dataset.count);
    const isFloat = el.dataset.float === '1';
    const prefix  = el.dataset.prefix || '';
    const suffix  = el.dataset.suffix || '';
    let start = 0;
    const duration = 1200;
    const step = 16;
    const increment = target / (duration / step);
    const timer = setInterval(() => {
      start += increment;
      if (start >= target) { start = target; clearInterval(timer); }
      el.textContent = prefix + (isFloat ? start.toFixed(2) : Math.floor(start).toLocaleString()) + suffix;
    }, step);
  });

  // ── Generate barcode bars on digital ID ──────────────────
  const barcode = document.getElementById('gp-barcode');
  if (barcode) {
    barcode.innerHTML = '';
    const widths = [3,1,2,1,3,2,1,3,1,2,1,3,2,1,1,3,2,1,3,1,2,3,1,2,1,3,1,2,3,1,2,1,3,2,1];
    widths.forEach((w, i) => {
      const span = document.createElement('span');
      span.style.width = w + 'px';
      span.style.animationDelay = (i * 18) + 'ms';
      barcode.appendChild(span);
    });
  }

  // ── Confirm delete dialogs ───────────────────────────────
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
      if (!confirm(el.dataset.confirm)) e.preventDefault();
    });
  });

  // ── Plan selection highlight on register ─────────────────
  document.querySelectorAll('.gp-plan-select-card').forEach(card => {
    card.addEventListener('click', () => {
      document.querySelectorAll('.gp-plan-select-card').forEach(c => {
        c.classList.remove('selected');
        c.querySelector('input[type=radio]')?.removeAttribute('checked');
      });
      card.classList.add('selected');
      const radio = card.querySelector('input[type=radio]');
      if (radio) { radio.checked = true; }
    });
  });

  // ── Sidebar active link ───────────────────────────────────
  const path = window.location.pathname;
  document.querySelectorAll('.gp-sidebar-link').forEach(link => {
    if (link.getAttribute('href') && path.endsWith(link.getAttribute('href').split('/').pop())) {
      link.classList.add('active');
    }
  });

  // ── Table search filter ───────────────────────────────────
  const searchInput = document.getElementById('tableSearch');
  if (searchInput) {
    searchInput.addEventListener('input', () => {
      const q = searchInput.value.toLowerCase();
      document.querySelectorAll('[data-searchable] tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
      });
    });
  }
});

// ── Charts (Chart.js) ─────────────────────────────────────
function renderSalesChart(labels, values) {
  const ctx = document.getElementById('salesChart');
  if (!ctx) return;
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Revenue ($)',
        data: values,
        backgroundColor: 'rgba(245,197,24,0.7)',
        borderColor: '#f5c518',
        borderWidth: 1,
        borderRadius: 4,
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#1a1a1a',
          borderColor: '#2a2a2a',
          borderWidth: 1,
          titleColor: '#f5c518',
          bodyColor: '#e8e8e8',
          callbacks: { label: ctx => ' $' + parseFloat(ctx.raw).toFixed(2) }
        }
      },
      scales: {
        x: { ticks: { color: '#888' }, grid: { color: '#1f1f1f' } },
        y: { ticks: { color: '#888', callback: v => '$' + v }, grid: { color: '#1f1f1f' } }
      }
    }
  });
}

function renderPlanPieChart(labels, values) {
  const ctx = document.getElementById('planChart');
  if (!ctx) return;
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels,
      datasets: [{
        data: values,
        backgroundColor: ['#f5c518','#e0a800','#c49000','#a87800'],
        borderColor: '#1a1a1a',
        borderWidth: 3,
      }]
    },
    options: {
      responsive: true,
      cutout: '65%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: { color: '#888', padding: 16, font: { size: 12 } }
        },
        tooltip: {
          backgroundColor: '#1a1a1a',
          borderColor: '#2a2a2a',
          borderWidth: 1,
          titleColor: '#f5c518',
          bodyColor: '#e8e8e8',
        }
      }
    }
  });
}
