<?php
class CarController
{
    private $carModel;

    public function __construct(CarRepositoryInterface $carModel)
    {
        $this->carModel = $carModel;
    }


    public function getCarDetails()
    {
        try {
            $car_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

            if ($car_id <= 0) {
                jsonResponse(['success' => false, 'error' => 'Invalid car ID.'], 400);
            }

            $car = $this->carModel->findById($car_id);

            if (!$car) {
                jsonResponse(['success' => false, 'error' => 'The car was not found.'], 404);
            }

            $car['image_mime'] = !empty($car['image_base64']) ? 'image/jpeg' : null;

            jsonResponse(['success' => true, 'car' => $car]);

        } catch (Throwable $e) {
            jsonResponse(['success' => false, 'error' => "Eroare: " . $e->getMessage()], 500);
        }
    }


    public function getAllCars()
    {
        try {
            $rows = $this->carModel->findAll();

            $out = [];
            foreach ($rows as $r) {
                $out[] = [
                    'car_id' => (int) $r['car_id'],
                    'brand_name' => $r['brand_name'],
                    'model_name' => $r['model_name'],
                    'image_base64' => $r['image_base64'],
                    'image_mime' => !empty($r['image_base64']) ? 'image/jpeg' : null,
                ];
            }

            jsonResponse(['success' => true, 'cars' => $out]);

        } catch (Throwable $e) {
            jsonResponse(['success' => false, 'error' => "Eroare: " . $e->getMessage()], 500);
        }
    }


    public function addCar()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if ($data === null) {
                jsonResponse(["success" => false, "message" => "Invalid JSON data."], 400);
            }


            $required_fields = [
                'brand_name',
                'model_name',
                'seller_id',
                'year',
                'price',
                'mileage',
                'fuel_type',
                'condition',
                'vin',
                'color',
                'image_url'
            ];

            foreach ($required_fields as $field) {
                if (empty($data[$field])) {
                    jsonResponse(["success" => false, "message" => "The field '$field' it is mandatory."], 400);
                }
            }

            $new_car_id = $this->carModel->create($data);

            jsonResponse([
                "success" => true,
                "message" => "The car was added successfully.!",
                "new_car_id" => $new_car_id
            ], 201);

        } catch (Throwable $e) {
            jsonResponse(["success" => false, "message" => "Eroare: " . $e->getMessage()], 500);
        }
    }

    public function buyCar()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if ($data === null) {
                jsonResponse(["success" => false, "error" => "Date JSON invalide."], 400);
            }

            // câmpuri obligatorii
            $required = ['car_id', 'buyer_id', 'final_price', 'payment_method'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    jsonResponse(["success" => false, "error" => "Câmpul '$field' este obligatoriu."], 400);
                }
            }

            // verifică dacă mașina există
            $car = $this->carModel->findById((int) $data['car_id']);
            if (!$car) {
                jsonResponse(["success" => false, "error" => "Mașina nu există."], 404);
            }

            // verifică dacă mașina nu a fost deja cumpărată
            if (method_exists($this->carModel, 'isCarSold') && $this->carModel->isCarSold((int) $data['car_id'])) {
                jsonResponse(["success" => false, "error" => "Mașina a fost deja vândută."], 409);
            }

            // inserează tranzacția
            if (!method_exists($this->carModel, 'insertTransaction')) {
                jsonResponse(["success" => false, "error" => "Funcția insertTransaction lipsește din model."], 500);
            }

            $transactionId = $this->carModel->insertTransaction([
                'car_id' => (int) $data['car_id'],
                'buyer_id' => (int) $data['buyer_id'],
                'final_price' => (float) $data['final_price'],
                'payment_method' => $data['payment_method']
            ]);

            jsonResponse([
                "success" => true,
                "message" => "Tranzacția a fost înregistrată cu succes.",
                "transaction_id" => $transactionId
            ], 201);

        } catch (Throwable $e) {
            jsonResponse(["success" => false, "error" => "Eroare: " . $e->getMessage()], 500);
        }
    }

}
?>