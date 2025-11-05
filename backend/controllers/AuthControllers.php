<?php
require_once(__DIR__ . '/../models/User.php');
require_once(__DIR__ . '/../utils/csrf.php');
require_once(__DIR__ . '/../utils/csrf.php');


class AuthController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // ===================== SIGNUP =====================
    public function signup()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data || !isset($data['email']) || !isset($data['password']) || !isset($data['full_name'])) {
            jsonResponse(['success' => false, 'error' => 'Invalid JSON data'], 400);
        }

        $email = trim($data['email']);
        $password = trim($data['password']);
        $full_name = trim($data['full_name']);
        $phone = trim($data['phone'] ?? null);
        $address = trim($data['address'] ?? null);
        $role_name = trim(strtolower($data['role'] ?? 'client'));

        // Set role_id
        $role_id = $role_name === 'admin' ? 1 : 2;

        if (empty($email) || empty($password) || empty($full_name)) {
            jsonResponse(['success' => false, 'error' => 'All fields are required.'], 400);
        }

        if (User::findByEmail($this->db, $email)) {
            jsonResponse(['success' => false, 'error' => 'An account with this email already exists.'], 409);
        }

        try {
            $newUserId = User::create($this->db, $email, $password, $full_name, $role_id, $phone, $address);

            jsonResponse([
                'success' => true,
                'message' => 'Account created successfully',
                'user_id' => $newUserId
            ], 201);
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ===================== LOGIN =====================
    public function login()
    {
        require_once(__DIR__ . '/../utils/csrf.php');

        // === Verificăm tokenul CSRF ===
        $headers = getallheaders();
        $token = $headers['X-CSRF-Token'] ?? $headers['x-csrf-token'] ?? null;

        if (!$token || !verifyCsrfToken($token)) {
            jsonResponse(['success' => false, 'error' => 'Invalid CSRF token'], 403);
        }

        // === Datele de login ===
        $data = json_decode(file_get_contents("php://input"), true);

        $email = trim($data['email'] ?? '');
        $password = trim($data['password'] ?? '');

        if (empty($email) || empty($password)) {
            jsonResponse(['success' => false, 'error' => 'Email and password are required.'], 400);
        }

        $user = User::findByEmail($this->db, $email);

        if (!$user || !password_verify($password, $user['password'])) {
            jsonResponse(['success' => false, 'error' => 'Incorrect email or password'], 401);
        }

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role_name'];
        $_SESSION['email'] = $user['email'];

        jsonResponse([
            'success' => true,
            'message' => 'Successful authentication',
            'user' => [
                'user_id' => $user['user_id'],
                'email' => $user['email'],
                'full_name' => $user['full_name'],
                'role_id' => $user['role_id']
            ]
        ]);
    }

    // ===================== LOGOUT =====================
    public function logout()
    {
        session_unset();
        session_destroy();
        jsonResponse(['success' => true, 'message' => 'Logout successful']);
    }
}
?>