<?php
// controllers/notifications.php
require_once __DIR__ . '/../config/database.php';

function getAllNotifications(): array {
    $db = getDB();
    $stmt = $db->query("
        SELECT n.*, u.name AS created_by_name,
               COUNT(ns.user_id) AS sent_count
        FROM notifications n
        LEFT JOIN users u ON u.id = n.created_by
        LEFT JOIN notifications_sent ns ON ns.notification_id = n.id
        GROUP BY n.id
        ORDER BY n.created_at DESC
    ");
    return $stmt->fetchAll();
}

function getNotificationById(int $id): ?array {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM notifications WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function createNotification(array $data, int $createdBy): array {
    $db = getDB();
    if (empty($data['title']) || empty($data['message'])) {
        return ['success' => false, 'message' => 'Title and message are required.'];
    }
    $stmt = $db->prepare("INSERT INTO notifications (title, message, created_by) VALUES (?,?,?)");
    $stmt->execute([trim($data['title']), trim($data['message']), $createdBy]);
    $notifId = (int)$db->lastInsertId();

    // Send to all active clients
    $users = $db->query("SELECT id FROM users WHERE role_id = 2 AND is_active = 1")->fetchAll();
    $ns = $db->prepare("INSERT IGNORE INTO notifications_sent (user_id, notification_id) VALUES (?,?)");
    foreach ($users as $u) {
        $ns->execute([$u['id'], $notifId]);
    }

    return ['success' => true, 'message' => 'Notification sent to all clients.'];
}

function updateNotification(int $id, array $data): array {
    $db = getDB();
    $stmt = $db->prepare("UPDATE notifications SET title=?, message=? WHERE id=?");
    $stmt->execute([trim($data['title']), trim($data['message']), $id]);
    return ['success' => true, 'message' => 'Notification updated.'];
}

function deleteNotification(int $id): array {
    $db = getDB();
    $db->prepare("DELETE FROM notifications_sent WHERE notification_id = ?")->execute([$id]);
    $db->prepare("DELETE FROM notifications WHERE id = ?")->execute([$id]);
    return ['success' => true, 'message' => 'Notification deleted.'];
}

function getUserNotifications(int $userId): array {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT n.id, n.title, n.message, n.created_at, ns.is_read
        FROM notifications n
        JOIN notifications_sent ns ON ns.notification_id = n.id
        WHERE ns.user_id = ?
        ORDER BY n.created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function markNotificationRead(int $userId, int $notifId): void {
    $db = getDB();
    $db->prepare("UPDATE notifications_sent SET is_read=1 WHERE user_id=? AND notification_id=?")
       ->execute([$userId, $notifId]);
}

function countUnread(int $userId): int {
    $db = getDB();
    $stmt = $db->prepare("SELECT COUNT(*) FROM notifications_sent WHERE user_id=? AND is_read=0");
    $stmt->execute([$userId]);
    return (int)$stmt->fetchColumn();
}
