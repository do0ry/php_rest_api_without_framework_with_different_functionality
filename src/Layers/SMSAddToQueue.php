<?php

namespace App\Layers;

use App\Utils\ChainFunctionality\LayerRegistration;

class SMSAddToQueue extends LayerRegistration
{
    public function execute()
    {
        print "IN EXECUTION add into send sms queue\n";
        return parent::execute();
    }

    public function rollback()
    {
        print "IN ROLLBACK undo add into send sms queue\n";
        return parent::rollback();
    }
}
