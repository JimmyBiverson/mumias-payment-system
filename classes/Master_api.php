<?php
require_once __DIR__ . '/Master_fixed.php';

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();

// CSRF token generation
if ($action === 'get_csrf_token') {
    $suffix = isset($_GET['s']) ? preg_replace('/[^a-z_]/', '', $_GET['s']) : 'default';
    echo json_encode(['token' => hash('sha256', session_id() . '::' . $suffix)]);
    exit;
}

switch ($action) {
    case 'save_company':
        echo $Master->save_company();
    break;
    case 'delete_company':
        echo $Master->delete_company();
    break;
    case 'save_fee':
        echo $Master->save_fee();
    break;
    case 'delete_fee':
        echo $Master->delete_fee();
    break;
    case 'get_fee':
        echo $Master->get_fee();
    break;
    case 'toggle_fee':
        echo $Master->toggle_fee();
    break;
    case 'save_gateway':
        echo $Master->save_gateway();
    break;
    case 'delete_gateway':
        echo $Master->delete_gateway();
    break;
    case 'save_transaction':
        echo $Master->save_transaction();
    break;
    case 'delete_transaction':
        echo $Master->delete_transaction();
    break;
    case 'get_unread_count':
        echo $Master->get_unread_transactions_count();
    break;
    case 'mark_read':
        echo $Master->mark_transaction_read();
    break;
    default:
    break;
}
