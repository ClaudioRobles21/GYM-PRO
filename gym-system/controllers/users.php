<?php
// controllers/users.php
require_once __DIR__ . '/../config/database.php';

function getAllUsers(): array {
    $db = getDB();
    $stmt = $db->query("
        SELECT u.id, u.name, u.email, u.phone, u.birth_date, u.is_active,
               r.name AS role, u.created_at,
               p.name AS plan_name, s.status AS sub_status
        FROM users u
        JOIN roles r ON u.role_id = r.id
        LEFT JOIN subscriptions s ON s.user_id = u.id AND s.status = 'active'
        LEFT JOIN plans p ON p.id = s.plan_id
        ORDER BY u.created_at DESC
    ");
    return $stmt->fetchAll();
}

function getUserById(int $id): ?array {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT u.*, r.name AS role
        FROM users u JOIN roles r ON u.role_id = r.id
        WHERE u.id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function updateUser(int $id, array $data): array {
    $db = getDB();
    $fields = [];
    $params = [];

    $allowed = ['name','email','phone','birth_date','is_active'];
    foreach ($allowed as $f) {
        if (isset($data[$f])) {
            $fields[] = "$f = ?";
            $params[] = $data[$f];
        }
    }
    if (!empty($data['password'])) {
        $fields[] = "password = ?";
        $params[] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
    }

    if (empty($fields)) return ['success' => false, 'message' => 'Nothing to update.'];

    $params[] = $id;
    $db->prepare("UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?")->execute($params);
    return ['success' => true, 'message' => 'User updated.'];
}

function deleteUser(int $id): array {
    $db = getDB();
    // Soft delete
    $stmt = $db->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
    $stmt->execute([$id]);
    return ['success' => true, 'message' => 'User deactivated.'];
}

function getUserProfile(int $userId): ?array {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT u.id, u.name, u.email, u.phone, u.birth_date, u.photo_url, u.created_at,
               s.id AS sub_id, s.start_date, s.end_date, s.status AS sub_status,
               p.id AS plan_id, p.name AS plan_name, p.price AS plan_price,
               c.card_number
        FROM users u
        LEFT JOIN subscriptions s ON s.user_id = u.id AND s.status = 'active'
        LEFT JOIN plans p ON p.id = s.plan_id
        LEFT JOIN cards c ON c.id = u.card_id
        WHERE u.id = ?
    ");
    $stmt->execute([$userId]);
    return $stmt->fetch() ?: null;
}

function getUserPayments(int $userId): array {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT py.id, py.amount, py.payment_date, p.name AS plan_name,
               CONCAT('****', RIGHT(c.card_number,4)) AS card_last4
        FROM payments py
        JOIN plans p ON p.id = py.plan_id
        LEFT JOIN cards c ON c.id = py.card_id
        WHERE py.user_id = ?
        ORDER BY py.payment_date DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function updateClientProfile(int $userId, array $data): array {
    $db = getDB();
    $fields = [];
    $params = [];

    foreach (['name','phone','birth_date'] as $f) {
        if (!empty($data[$f])) {
            $fields[] = "$f = ?";
            $params[] = $data[$f];
        }
    }
    if (!empty($data['password'])) {
        $fields[] = "password = ?";
        $params[] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
    }
    if (empty($fields)) return ['success' => false, 'message' => 'Nothing to update.'];

    $params[] = $userId;
    $db->prepare("UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?")->execute($params);

    // Update session name
    if (!empty($data['name'])) $_SESSION['user_name'] = $data['name'];
    return ['success' => true, 'message' => 'Profile updated successfully.'];
}
