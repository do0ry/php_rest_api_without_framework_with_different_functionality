<?php

namespace App\Layers;

use App\Utils\ChainFunctionality\LayerRegistration;

class AddLog extends LayerRegistration
{
    public function execute()
    {
        print "IN EXECUTION add LOG\n";
        return parent::execute();
    }

    public function rollback()
    {
        print "IN ROLLBACK undo LOG\n";
        return parent::rollback();
    }
}
