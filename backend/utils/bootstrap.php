<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error.log');

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

function jsonResponse($data, $statusCode = 200)
{
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once(__DIR__ . '/../config/Database.php');

require_once(__DIR__ . '/../models/CarRepositoryInterface.php');
require_once(__DIR__ . '/../models/Car.php');
require_once(__DIR__ . '/../models/User.php');
require_once(__DIR__ . '/../controllers/CarControllers.php');
require_once(__DIR__ . '/../models/User.php');
require_once(__DIR__ . '/../controllers/AuthControllers.php');

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    jsonResponse(['success' => false, 'error' => 'Could not connect to database.'], 500);
}

$carModel = new Car($db);
$carController = new CarController($carModel);
?>