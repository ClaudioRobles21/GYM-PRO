<?php
// controllers/plans.php
require_once __DIR__ . '/../config/database.php';

function getAllPlans(): array {
    $db = getDB();
    $stmt = $db->query("
        SELECT p.id, p.name, p.description, p.price, p.duration_months, p.is_active,
               GROUP_CONCAT(b.description ORDER BY b.id SEPARATOR '||') AS benefits
        FROM plans p
        LEFT JOIN plan_benefits pb ON pb.plan_id = p.id
        LEFT JOIN benefits b ON b.id = pb.benefit_id
        GROUP BY p.id
        ORDER BY p.price ASC
    ");
    $rows = $stmt->fetchAll();
    foreach ($rows as &$r) {
        $r['benefits_list'] = $r['benefits'] ? explode('||', $r['benefits']) : [];
    }
    return $rows;
}

function getPlanById(int $id): ?array {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM plans WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function createPlan(array $data): array {
    $db = getDB();
    if (empty($data['name']) || empty($data['price'])) {
        return ['success' => false, 'message' => 'Name and price are required.'];
    }
    $stmt = $db->prepare("INSERT INTO plans (name, description, price, duration_months) VALUES (?,?,?,?)");
    $stmt->execute([
        trim($data['name']),
        $data['description'] ?? '',
        (float)$data['price'],
        (int)($data['duration_months'] ?? 1)
    ]);
    return ['success' => true, 'message' => 'Plan created.', 'id' => $db->lastInsertId()];
}

function updatePlan(int $id, array $data): array {
    $db = getDB();
    $stmt = $db->prepare("
        UPDATE plans SET name=?, description=?, price=?, duration_months=?, is_active=?
        WHERE id=?
    ");
    $stmt->execute([
        trim($data['name']),
        $data['description'] ?? '',
        (float)$data['price'],
        (int)($data['duration_months'] ?? 1),
        isset($data['is_active']) ? 1 : 0,
        $id
    ]);
    return ['success' => true, 'message' => 'Plan updated.'];
}

function deletePlan(int $id): array {
    $db = getDB();
    $db->prepare("UPDATE plans SET is_active = 0 WHERE id = ?")->execute([$id]);
    return ['success' => true, 'message' => 'Plan deactivated.'];
}

function getActivePlans(): array {
    $db = getDB();
    $stmt = $db->query("
        SELECT p.id, p.name, p.description, p.price, p.duration_months,
               GROUP_CONCAT(b.description ORDER BY b.id SEPARATOR '||') AS benefits
        FROM plans p
        LEFT JOIN plan_benefits pb ON pb.plan_id = p.id
        LEFT JOIN benefits b ON b.id = pb.benefit_id
        WHERE p.is_active = 1
        GROUP BY p.id
        ORDER BY p.price ASC
    ");
    $rows = $stmt->fetchAll();
    foreach ($rows as &$r) {
        $r['benefits_list'] = $r['benefits'] ? explode('||', $r['benefits']) : [];
    }
    return $rows;
}

function subscribeToPlan(int $userId, int $planId): array {
    $db = getDB();
    // Cancel existing active subscriptions
    $db->prepare("UPDATE subscriptions SET status='cancelled' WHERE user_id=? AND status='active'")->execute([$userId]);

    $start = date('Y-m-d');
    $plan  = getPlanById($planId);
    if (!$plan) return ['success' => false, 'message' => 'Plan not found.'];

    $end = date('Y-m-d', strtotime("+{$plan['duration_months']} month", strtotime($start)));

    $stmt = $db->prepare("INSERT INTO subscriptions (user_id, plan_id, start_date, end_date, status) VALUES (?,?,?,?,'active')");
    $stmt->execute([$userId, $planId, $start, $end]);

    // Record payment
    $userStmt = $db->prepare("SELECT card_id FROM users WHERE id=?");
    $userStmt->execute([$userId]);
    $user = $userStmt->fetch();

    $pay = $db->prepare("INSERT INTO payments (user_id, plan_id, card_id, amount, payment_date) VALUES (?,?,?,?,?)");
    $pay->execute([$userId, $planId, $user['card_id'] ?? null, $plan['price'], $start]);

    return ['success' => true, 'message' => "Subscribed to {$plan['name']} plan!"];
}
