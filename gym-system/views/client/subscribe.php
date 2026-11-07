<?php
// views/client/subscribe.php  — handles POST from plans.php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../controllers/plans.php';

requireClient();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $planId = (int)($_POST['plan_id'] ?? 0);
    if ($planId > 0) {
        $result = subscribeToPlan((int)$_SESSION['user_id'], $planId);
        flashMessage($result['success'] ? 'success' : 'danger', $result['message']);
    } else {
        flashMessage('danger', 'Invalid plan selected.');
    }
}

header('Location: /gym-system/views/client/subscription.php');
exit;
