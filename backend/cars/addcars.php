<?php
require_once(__DIR__ . '/../utils/bootstrap.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Unallowed method.'], 405);
}

$carController->addCar();
?>