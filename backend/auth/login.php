<?php
require_once(__DIR__ . '/../utils/bootstrap.php');
require_once(__DIR__ . '/../controllers/AuthControllers.php');

if (!class_exists('AuthController')) {
    jsonResponse(['success' => false, 'error' => 'AuthController class not found'], 500);
}

$authController = new AuthController($db);
$authController->login();