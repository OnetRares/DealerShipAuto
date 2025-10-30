<?php

require_once(__DIR__ . '/CarRepositoryInterface.php');
class Car implements CarRepositoryInterface
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }


    public function findById($car_id)
    {
        $sql = "SELECT
                    c.*, m.model_name, b.brand_name, img.image_base64 
                FROM
                    public.cars AS c
                LEFT JOIN
                    public.models AS m ON c.model_id = m.model_id
                LEFT JOIN
                    public.brands AS b ON m.brand_id = b.brand_id
                LEFT JOIN (
                    SELECT DISTINCT ON (car_id)
                        car_id, encode(image, 'base64') AS image_base64
                    FROM public.images
                    WHERE car_id = :car_id_img 
                    ORDER BY car_id, image_id DESC
                ) AS img ON img.car_id = c.car_id
                WHERE
                    c.car_id = :car_id_main
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':car_id_img', $car_id, PDO::PARAM_INT);
        $stmt->bindParam(':car_id_main', $car_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function findAll()
    {
        $sql = "SELECT
                    c.car_id, m.model_name, b.brand_name,
                    encode(img.image, 'base64') AS image_base64
                FROM
                    public.cars AS c
                LEFT JOIN
                    public.models AS m ON c.model_id = m.model_id
                LEFT JOIN
                    public.brands AS b ON m.brand_id = b.brand_id
                LEFT JOIN (
                    SELECT DISTINCT ON (car_id)
                        car_id, image
                    FROM public.images
                    ORDER BY car_id, image_id DESC
                ) AS img ON img.car_id = c.car_id
                ORDER BY
                    c.car_id DESC";

        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function create($data)
    {
        try {
            $this->conn->beginTransaction();

            $brand_id = $this->findOrCreateBrand($data['brand_name']);
            $model_id = $this->findOrCreateModel($brand_id, $data['model_name']);

            $sql_car = "INSERT INTO public.cars 
                        (model_id, seller_id, year, price, mileage, fuel_type, condition, vin, color) 
                        VALUES 
                        (:model_id, :seller_id, :year, :price, :mileage, :fuel_type, :condition, :vin, :color) 
                        RETURNING car_id";

            $stmt = $this->conn->prepare($sql_car);
            $stmt->execute([
                'model_id' => $model_id,
                'seller_id' => $data['seller_id'],
                'year' => (int) $data['year'],
                'price' => (float) $data['price'],
                'mileage' => (int) $data['mileage'],
                'fuel_type' => $data['fuel_type'],
                'condition' => $data['condition'],
                'vin' => $data['vin'],
                'color' => $data['color']
            ]);
            $new_car_id = $stmt->fetchColumn();

            $image_data_bytea = @file_get_contents($data['image_url']);
            if ($image_data_bytea === false) {
                throw new Exception("Could not download image from URL: " . $data['image_url']);
            }

            $sql_image = "INSERT INTO public.images (car_id, image) VALUES (:car_id, :image_data)";
            $stmt = $this->conn->prepare($sql_image);
            $stmt->bindParam(':car_id', $new_car_id, PDO::PARAM_INT);
            $stmt->bindParam(':image_data', $image_data_bytea, PDO::PARAM_LOB);
            $stmt->execute();

            $this->conn->commit();

            return $new_car_id;

        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }
    }


    private function findOrCreateBrand($brand_name)
    {
        $stmt = $this->conn->prepare("SELECT brand_id FROM public.brands WHERE brand_name = :brand_name");
        $stmt->execute(['brand_name' => $brand_name]);
        $brand = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($brand) {
            return $brand['brand_id'];
        } else {
            $stmt = $this->conn->prepare("INSERT INTO public.brands (brand_name) VALUES (:brand_name) RETURNING brand_id");
            $stmt->execute(['brand_name' => $brand_name]);
            return $stmt->fetchColumn();
        }
    }

    private function findOrCreateModel($brand_id, $model_name)
    {
        $stmt = $this->conn->prepare("SELECT model_id FROM public.models WHERE model_name = :model_name AND brand_id = :brand_id");
        $stmt->execute(['model_name' => $model_name, 'brand_id' => $brand_id]);
        $model = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($model) {
            return $model['model_id'];
        } else {
            $stmt = $this->conn->prepare("INSERT INTO public.models (brand_id, model_name) VALUES (:brand_id, :model_name) RETURNING model_id");
            $stmt->execute(['brand_id' => $brand_id, 'model_name' => $model_name]);
            return $stmt->fetchColumn();
        }
    }
    public function isCarSold(int $car_id): bool
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM transactions WHERE car_id = :car_id");
        $stmt->execute([':car_id' => $car_id]);
        return $stmt->fetchColumn() > 0;
    }

    public function insertTransaction(array $data): int
    {
        $stmt = $this->conn->prepare("
        INSERT INTO transactions (car_id, buyer_id, transaction_date, final_price, payment_method)
        VALUES (:car_id, :buyer_id, NOW(), :final_price, :payment_method)
        RETURNING transaction_id
    ");
        $stmt->execute([
            ':car_id' => $data['car_id'],
            ':buyer_id' => $data['buyer_id'],
            ':final_price' => $data['final_price'],
            ':payment_method' => $data['payment_method']
        ]);

        return (int) $stmt->fetchColumn();
    }


}
?>