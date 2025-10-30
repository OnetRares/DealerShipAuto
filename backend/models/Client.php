<?php
require_once(__DIR__ . '/User.php');

class Client extends User
{
    public function __construct($db, $user_id)
    {
        parent::__construct($db);
        $this->loadUserData($user_id);
    }


    public function saveFavoriteCar($car_id)
    {
        $sql = "INSERT INTO public.favorites (user_id, car_id) VALUES (:user_id, :car_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['user_id' => $this->getID(), 'car_id' => $car_id]);

        return true;
    }
}
?>