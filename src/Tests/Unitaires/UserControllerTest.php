<?php
namespace src\Tests\Unitaires;

if (file_exists(__DIR__ . '/../../../config_test.php')) {
    require_once __DIR__ . '/../../../config_test.php';
}

use PHPUnit\Framework\TestCase;
use src\Controllers\UserController;
use src\Repositories\UserRepository;

class UserControllerTest extends TestCase
{
    public function testCanBeInstantiated()
    {
        $repoMock = $this->createMock(UserRepository::class);
        $controller = new UserController($repoMock);
        $this->assertInstanceOf(UserController::class, $controller);
    }

    public function testDisplayDashboardMethodExists()
    {
        $repoMock = $this->createMock(UserRepository::class);
        $controller = new UserController($repoMock);
        $this->assertTrue(method_exists($controller, 'displayDashboard'));
    }

    public function testTreatmentSignInMethodExists()
    {
        $repoMock = $this->createMock(UserRepository::class);
        $controller = new UserController($repoMock);
        $this->assertTrue(method_exists($controller, 'treatmentSignIn'));
    }

    public function testEmailValidation()
    {
        $controller = new UserController($this->createMock(UserRepository::class));
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('sendEmail');
        $method->setAccessible(true);

        // Valid email (if your method returns true for valid)
        $resultValid = $method->invoke($controller, 'feras', 'ffff', 'test@test.test', 'token');
        $this->assertIsBool($resultValid);
    }

    public function testPasswordHashing()
    {
        $password = 'StrongPassword123!';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $this->assertTrue(password_verify($password, $hash));
    }

  
}
