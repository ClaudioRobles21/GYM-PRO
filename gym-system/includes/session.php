<?php
// includes/session.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isClient(): bool {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'client';
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /gym-system/login.php');
        exit;
    }
}

function requireAdmin(): void {
    requireLogin();
    if (!isAdmin()) {
        header('Location: /gym-system/dashboard_client.php');
        exit;
    }
}

function requireClient(): void {
    requireLogin();
    if (!isClient()) {
        header('Location: /gym-system/dashboard_admin.php');
        exit;
    }
}

function flashMessage(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function currentUser(): array {
    return [
        'id'    => $_SESSION['user_id'] ?? null,
        'name'  => $_SESSION['user_name'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'role'  => $_SESSION['role'] ?? '',
    ];
}
