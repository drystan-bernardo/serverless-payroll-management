<?php
namespace App;

class Employee
{
    public string $id;
    public string $name;
    public string $department;
    public string $position;

    public function __construct(array $row)
    {
        // Expected CSV columns: id,name,department,position
        $this->id = $row[0] ?? '';
        $this->name = $row[1] ?? '';
        $this->department = $row[2] ?? '';
        $this->position = $row[3] ?? '';
    }
}
?>
