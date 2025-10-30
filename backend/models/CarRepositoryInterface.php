<?php


interface CarRepositoryInterface
{
    /**
     *Find a single car by ID
     * @param int $car_id
     * @return array|false
     */
    public function findById($car_id);

    /**
     *Find all cars.
     * @return array
     */
    public function findAll();

    /**
     *Create a new car.
     * @param array $dataCar data
     * @return int New car ID
     */
    public function create($data);

    public function isCarSold(int $car_id): bool;
    public function insertTransaction(array $data): int;

}
?>