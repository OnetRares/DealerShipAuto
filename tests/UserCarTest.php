<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../backend/models/User.php';
require_once __DIR__ . '/../backend/models/Car.php';

class UserCarTest extends TestCase
{
    private $pdoMock;
    private $stmtMock;

    protected function setUp(): void
    {
        $this->pdoMock = $this->createMock(PDO::class);
        $this->stmtMock = $this->createMock(PDOStatement::class);
    }

    // ===================== User Tests =====================
    public function testFindByEmailReturnsUserData()
    {
        $expected = [
            'user_id' => 999,
            'email' => 'test@example.com',
            'role_name' => 'Client'
        ];

        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->expects($this->once())->method('execute')->with(['email' => 'test@example.com']);
        $this->stmtMock->method('fetch')->willReturn($expected);

        $result = User::findByEmail($this->pdoMock, 'test@example.com');
        $this->assertEquals($expected, $result);
    }

    public function testCreateUserReturnsUserId()
    {
        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('fetchColumn')->willReturn(42);

        $result = User::create($this->pdoMock, 'new@example.com', 'secret', 'John Doe');
        $this->assertEquals(42, $result);
    }

    // ===================== Car Tests =====================
    public function testFindByIdReturnsCarData()
    {
        $expected = [
            'car_id' => 10,
            'model_name' => 'Civic',
            'brand_name' => 'Honda'
        ];

        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('fetch')->willReturn($expected);

        $carRepo = new Car($this->pdoMock);
        $result = $carRepo->findById(10);

        $this->assertEquals($expected, $result);
    }

    public function testIsCarSoldReturnsTrueWhenTransactionExists()
    {
        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('fetchColumn')->willReturn(1);

        $carRepo = new Car($this->pdoMock);
        $this->assertTrue($carRepo->isCarSold(5));
    }

    public function testInsertTransactionReturnsTransactionId()
    {
        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('fetchColumn')->willReturn(99);

        $carRepo = new Car($this->pdoMock);
        $data = [
            'car_id' => 1,
            'buyer_id' => 2,
            'final_price' => 12000,
            'payment_method' => 'cash'
        ];

        $result = $carRepo->insertTransaction($data);
        $this->assertEquals(99, $result);
    }
}
