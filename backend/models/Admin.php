<?php
require_once(__DIR__ . '/User.php');

class Admin extends User
{

    public function __construct($db, $user_id)
    {

        parent::__construct($db);

        $this->loadUserData($user_id);
    }


    public function addCar(CarRepositoryInterface $carModel, $carData)
    {

        $carData['seller_id'] = $this->getID();


        try {
            return $carModel->create($carData);
        } catch (Exception $e) {
            throw new Exception("Error adding car: " . $e->getMessage());
        }
    }
}
?>