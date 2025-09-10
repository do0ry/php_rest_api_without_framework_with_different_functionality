<?php

namespace App\Layers;

use App\Utils\ChainFunctionality\LayerRegistration;

class ReserveCapacity extends LayerRegistration
{
    public function execute()
    {
        print "IN EXECUTION reserve capacity\n";
        return parent::execute();
    }

    public function rollback()
    {
        print "IN ROLLBACK undo capacity\n";
        return parent::rollback();
    }
}
