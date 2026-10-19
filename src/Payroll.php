<?php
namespace App;

class Payroll
{
    private array $employees; // each entry is raw CSV row
    private array $payrollRows; // each entry is raw CSV row

    public function __construct(array $employeesCsv, array $payrollCsv)
    {
        $this->employees = $employeesCsv;
        $this->payrollRows = $payrollCsv;
    }

    /**
     * Compute a payroll summary: total gross, total deductions, total net.
     */
    public function summary(): array
    {
        $totalGross = 0.0;
        $totalDeduction = 0.0;
        $totalNet = 0.0;
        foreach ($this->payrollRows as $row) {
            // Expected columns: employee_id,gross,deductions,net
            $gross = (float)($row[1] ?? 0);
            $deduct = (float)($row[2] ?? 0);
            $net = (float)($row[3] ?? 0);
            $totalGross += $gross;
            $totalDeduction += $deduct;
            $totalNet += $net;
        }
        return [
            'totalGross' => $totalGross,
            'totalDeductions' => $totalDeduction,
            'totalNet' => $totalNet,
            'recordCount' => count($this->payrollRows),
        ];
    }

    /**
     * Return a payslip for a specific employee id or null if not found.
     */
    public function payslipFor(string $employeeId): ?array
    {
        // Find employee info first
        $employeeInfo = null;
        foreach ($this->employees as $empRow) {
            if (($empRow[0] ?? '') === $employeeId) {
                $employeeInfo = [
                    'id' => $empRow[0] ?? '',
                    'name' => $empRow[1] ?? '',
                    'department' => $empRow[2] ?? '',
                    'position' => $empRow[3] ?? '',
                ];
                break;
            }
        }
        if ($employeeInfo === null) {
            return null;
        }
        // Find payroll record
        foreach ($this->payrollRows as $payRow) {
            if (($payRow[0] ?? '') === $employeeId) {
                return array_merge($employeeInfo, [
                    'gross' => (float)($payRow[1] ?? 0),
                    'deductions' => (float)($payRow[2] ?? 0),
                    'net' => (float)($payRow[3] ?? 0),
                ]);
            }
        }
        return null;
    }
}
?>
