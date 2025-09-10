<?php

namespace App\Utils\ChainFunctionality;

use App\Models\BaseEntity;


abstract class LayerRegistration
{
    private ?LayerRegistration $next = null;
    private ?LayerRegistration $previous = null;
    public function linkWith(LayerRegistration $next): LayerRegistration
    {
        $this->next = $next;
        $this->next->previous = $this;
        return $next;
    }

    public function execute()
    {
        if (!$this->next) {
            return true;
        }

        try {
            $this->next->execute();
        } catch (\Exception $e) {
            $this->rollback();
        }

        return $this;
    }

    public function rollback()
    {
        //if transaction is open
        $connector = BaseEntity::getConnector();
        if ($connector->isTransactionActive()) {
            $connector->rollback();
        }
        
        if (!$this->previous) {
            return true;
        }

        $this->previous->rollback();
        return $this;
    }
}
