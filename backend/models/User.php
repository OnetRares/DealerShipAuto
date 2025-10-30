<?php
class User
{
    protected $conn;
    protected $userData;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // ===================== Load user data by ID =====================
    protected function loadUserData($user_id)
    {
        $sql = "SELECT u.*, r.role_name 
                FROM public.users u 
                JOIN public.roles r ON u.role_id = r.role_id
                WHERE u.user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['user_id' => $user_id]);
        $this->userData = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ===================== Getters =====================
    public function getID()
    {
        return $this->userData['user_id'] ?? null;
    }
    public function getEmail()
    {
        return $this->userData['email'] ?? null;
    }
    public function getRole()
    {
        return $this->userData['role_name'] ?? null;
    }
    public function getFullName()
    {
        return $this->userData['full_name'] ?? null;
    }

    // ===================== Find user by email =====================
    public static function findByEmail($db, $email)
    {
        $sql = "SELECT u.*, r.role_name
                FROM public.users u
                JOIN public.roles r ON u.role_id = r.role_id
                WHERE u.email = :email";
        $stmt = $db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ===================== Create new user =====================
    public static function create($db, $email, $password, $full_name, $role_id = 2, $phone = null, $address = null)
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO public.users (email, password, full_name, phone, address, role_id)
                VALUES (:email, :password, :full_name, :phone, :address, :role_id)
                RETURNING user_id";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            'email' => $email,
            'password' => $hashedPassword,
            'full_name' => $full_name,
            'phone' => $phone,
            'address' => $address,
            'role_id' => $role_id
        ]);

        return $stmt->fetchColumn();
    }
}
?>