<?php
require_once(__DIR__ . '/../utils/bootstrap.php');
require_once(__DIR__ . '/../controllers/AuthControllers.php');


$authController = new AuthController($db);
$authController->signup();
?>