<?php
// controllers/reports.php
require_once __DIR__ . '/../config/database.php';

function getSalesByMonth(int $year): array {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT
            MONTH(payment_date) AS month,
            MONTHNAME(payment_date) AS month_name,
            SUM(amount) AS total,
            COUNT(*) AS count
        FROM payments
        WHERE YEAR(payment_date) = ?
        GROUP BY MONTH(payment_date)
        ORDER BY MONTH(payment_date)
    ");
    $stmt->execute([$year]);
    return $stmt->fetchAll();
}

function getUsersPerPlan(): array {
    $db = getDB();
    $stmt = $db->query("
        SELECT p.name AS plan_name, p.price,
               COUNT(s.id) AS total_subscribers
        FROM plans p
        LEFT JOIN subscriptions s ON s.plan_id = p.id AND s.status = 'active'
        GROUP BY p.id
        ORDER BY total_subscribers DESC
    ");
    return $stmt->fetchAll();
}

function getReportSummary(): array {
    $db = getDB();

    $totalUsers   = (int)$db->query("SELECT COUNT(*) FROM users WHERE role_id=2 AND is_active=1")->fetchColumn();
    $activeSubsQ  = $db->query("SELECT COUNT(*) FROM subscriptions WHERE status='active'");
    $activeSubs   = (int)$activeSubsQ->fetchColumn();
    $monthRevQ    = $db->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE MONTH(payment_date)=MONTH(CURDATE()) AND YEAR(payment_date)=YEAR(CURDATE())");
    $monthRev     = (float)$monthRevQ->fetchColumn();
    $totalRevQ    = $db->query("SELECT COALESCE(SUM(amount),0) FROM payments");
    $totalRev     = (float)$totalRevQ->fetchColumn();

    return [
        'total_clients'    => $totalUsers,
        'active_subs'      => $activeSubs,
        'month_revenue'    => $monthRev,
        'total_revenue'    => $totalRev,
    ];
}

function getAvailableYears(): array {
    $db = getDB();
    $stmt = $db->query("SELECT DISTINCT YEAR(payment_date) AS yr FROM payments ORDER BY yr DESC");
    $years = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (empty($years)) $years = [date('Y')];
    return $years;
}
