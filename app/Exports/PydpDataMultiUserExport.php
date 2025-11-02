<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PydpDataMultiUserExport implements WithMultipleSheets
{
    use Exportable;

    private $exports;

    public function __construct($exports)
    {
        $this->exports = $exports;
    }

    public function sheets(): array
    {
        return $this->exports;
    }
}