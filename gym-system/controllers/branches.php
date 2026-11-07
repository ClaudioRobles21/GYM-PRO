<?php
// controllers/branches.php
require_once __DIR__ . '/../config/database.php';

function getAllBranches(): array {
    $db = getDB();
    $stmt = $db->query("
        SELECT b.*, u.name AS admin_name
        FROM branches b
        LEFT JOIN users u ON u.id = b.admin_id
        ORDER BY b.id ASC
    ");
    return $stmt->fetchAll();
}

function getActiveBranches(): array {
    $db = getDB();
    $stmt = $db->query("
        SELECT b.*, u.name AS admin_name
        FROM branches b
        LEFT JOIN users u ON u.id = b.admin_id
        WHERE b.is_active = 1
        ORDER BY b.id ASC
    ");
    return $stmt->fetchAll();
}

function getBranchById(int $id): ?array {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM branches WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function createBranch(array $data): array {
    $db = getDB();
    if (empty($data['name']) || empty($data['address'])) {
        return ['success' => false, 'message' => 'Name and address are required.'];
    }
    $stmt = $db->prepare("
        INSERT INTO branches (name, address, city, phone, schedule, admin_id)
        VALUES (?,?,?,?,?,?)
    ");
    $stmt->execute([
        trim($data['name']),
        trim($data['address']),
        trim($data['city'] ?? ''),
        $data['phone'] ?? null,
        $data['schedule'] ?? null,
        $data['admin_id'] ?? null,
    ]);
    return ['success' => true, 'message' => 'Branch created successfully.'];
}

function updateBranch(int $id, array $data): array {
    $db = getDB();
    $stmt = $db->prepare("
        UPDATE branches SET name=?, address=?, city=?, phone=?, schedule=?, is_active=?
        WHERE id=?
    ");
    $stmt->execute([
        trim($data['name']),
        trim($data['address']),
        trim($data['city'] ?? ''),
        $data['phone'] ?? null,
        $data['schedule'] ?? null,
        isset($data['is_active']) ? 1 : 0,
        $id
    ]);
    return ['success' => true, 'message' => 'Branch updated successfully.'];
}

function deleteBranch(int $id): array {
    $db = getDB();
    $db->prepare("UPDATE branches SET is_active = 0 WHERE id = ?")->execute([$id]);
    return ['success' => true, 'message' => 'Branch deactivated.'];
}
