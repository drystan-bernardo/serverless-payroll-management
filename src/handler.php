<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Bref\Runtime\LambdaRuntime;
use Bramus\Router\Router;
use App\Employee;
use App\Payroll;

$router = new Router();

// Helper to load CSV data
function loadCsv(string $path): array {
    $rows = [];
    if (!file_exists($path)) return $rows;
    $handle = fopen($path, 'r');
    while (($data = fgetcsv($handle)) !== false) {
        $rows[] = $data;
    }
    fclose($handle);
    return $rows;
}

$router->get('/api/employees', function() {
    $employees = [];
    foreach (loadCsv(__DIR__.'/../data/employees.csv') as $row) {
        $employees[] = new Employee($row);
    }
    header('Content-Type: application/json');
    echo json_encode($employees);
});

$router->get('/api/reports/payroll', function() {
    $payroll = new Payroll(
        loadCsv(__DIR__.'/../data/employees.csv'),
        loadCsv(__DIR__.'/../data/payroll.csv')
    );
    header('Content-Type: application/json');
    echo json_encode($payroll->summary());
});

$router->get('/api/payroll/([0-9]+)', function($employeeId) {
    $payroll = new Payroll(
        loadCsv(__DIR__.'/../data/employees.csv'),
        loadCsv(__DIR__.'/../data/payroll.csv')
    );
    $payslip = $payroll->payslipFor((string)$employeeId);
    if ($payslip === null) {
        http_response_code(404);
        echo json_encode(['error' => 'Employee not found']);
        return;
    }
    header('Content-Type: application/json');
    echo json_encode($payslip);
});

$router->run();
?>
