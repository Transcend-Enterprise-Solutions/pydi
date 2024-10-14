<?php
namespace App\Models;

use Exception;

class SubdivisionLots
{
    private $blocks;

    public function __construct()
    {
        $this->blocks = [
            1 => range(1, 34),
            2 => range(1, 40),
            3 => range(1, 61),
            4 => range(1, 55),
            5 => range(1, 49),
            6 => range(1, 28),
            7 => range(1, 13),
            8 => range(1, 9),
            9 => range(1, 3),
            10 => range(1, 18),
            11 => range(1, 4)
        ];
    }

    public function getBlocks()
    {
        return array_keys($this->blocks);
    }

    public function getLotsInBlock($blockNumber)
    {
        if (!isset($this->blocks[$blockNumber])) {
            throw new Exception("Invalid block number");
        }
        return $this->blocks[$blockNumber];
    }

    public function isValidLot($blockNumber, $lotNumber)
    {
        if (!isset($this->blocks[$blockNumber])) {
            return false;
        }
        return in_array($lotNumber, $this->blocks[$blockNumber]);
    }
}