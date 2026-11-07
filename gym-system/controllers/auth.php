<?php
// controllers/auth.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';

function loginUser(string $email, string $password): array {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT u.id, u.name, u.email, u.password, r.name AS role
        FROM users u
        JOIN roles r ON u.role_id = r.id
        WHERE u.email = ? AND u.is_active = 1
        LIMIT 1
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Invalid email or password.'];
    }

    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_name']  = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['role']       = $user['role'];

    return ['success' => true, 'role' => $user['role']];
}

function registerUser(array $data): array {
    $db = getDB();

    // Validate required fields
    $required = ['name','email','password','birth_date'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return ['success' => false, 'message' => "Field '$field' is required."];
        }
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Invalid email address.'];
    }
    if (strlen($data['password']) < 6) {
        return ['success' => false, 'message' => 'Password must be at least 6 characters.'];
    }

    // Check duplicate email
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Email already registered.'];
    }

    $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);

    // Optionally handle card
    $cardId = null;
    if (!empty($data['card_number']) && !empty($data['cvv']) && !empty($data['expiry_date'])) {
        $cs = $db->prepare("INSERT INTO cards (card_number, cvv, expiry_date) VALUES (?, ?, ?)");
        $cs->execute([$data['card_number'], $data['cvv'], $data['expiry_date']]);
        $cardId = $db->lastInsertId();
    }

    $stmt = $db->prepare("
        INSERT INTO users (name, email, password, birth_date, phone, role_id, card_id)
        VALUES (?, ?, ?, ?, ?, 2, ?)
    ");
    $stmt->execute([
        trim($data['name']),
        strtolower(trim($data['email'])),
        $hash,
        $data['birth_date'],
        $data['phone'] ?? null,
        $cardId
    ]);

    $userId = (int)$db->lastInsertId();

    // Auto-subscribe if plan selected
    if (!empty($data['plan_id'])) {
        $planId = (int)$data['plan_id'];
        $start  = date('Y-m-d');
        $end    = date('Y-m-d', strtotime('+1 month'));

        $ps = $db->prepare("INSERT INTO subscriptions (user_id, plan_id, start_date, end_date) VALUES (?, ?, ?, ?)");
        $ps->execute([$userId, $planId, $start, $end]);

        // Get plan price
        $pp = $db->prepare("SELECT price FROM plans WHERE id = ?");
        $pp->execute([$planId]);
        $plan = $pp->fetch();

        if ($plan && $cardId) {
            $pay = $db->prepare("INSERT INTO payments (user_id, plan_id, card_id, amount, payment_date) VALUES (?,?,?,?,?)");
            $pay->execute([$userId, $planId, $cardId, $plan['price'], $start]);
        }
    }

    // Send welcome notification
    $notif = $db->prepare("SELECT id FROM notifications ORDER BY id ASC LIMIT 1");
    $notif->execute();
    $n = $notif->fetch();
    if ($n) {
        $ns = $db->prepare("INSERT IGNORE INTO notifications_sent (user_id, notification_id) VALUES (?,?)");
        $ns->execute([$userId, $n['id']]);
    }

    return ['success' => true, 'message' => 'Account created successfully!'];
}

function logoutUser(): void {
    session_destroy();
    header('Location: /gym-system/login.php');
    exit;
}
