<?php

namespace App\Utils\ChainFunctionality;

use App\Config\TransactionManager;
use App\Utils\ChainFunctionality\LayerRegistration;
use App\Config\Database;
use App\Models\BaseEntity;

class Executor
{
    private LayerRegistration $registeredLayer;

    public function setLogic(LayerRegistration $registeredLayer): void
    {
        $this->registeredLayer = $registeredLayer;
    }

    public function runLogic(): bool
    {
        // Begin transaction
        $connector = BaseEntity::getConnector();
        if (!$connector->isTransactionActive()) {
            $connector->beginTransaction();
        }

        try {
            $this->registeredLayer->execute();
            
            // Commit transaction if everything succeeds
            if ($connector && $connector->isTransactionActive()) {
                $connector->commit();
            }
            
            return true;
        } catch (\Exception $e) {
            $this->registeredLayer->rollback();
            // Rollback transaction on any error
            if ($connector->isTransactionActive()) {
                $connector->rollback();
            }
            
            return false;
        }
    }

    /**
     * Run logic with custom transaction manager
     */
    public function runWithTransaction(): bool
    {
        return $this->runLogic();
    }

    /**
     * Run logic without transaction
     */
    public function runWithoutTransaction(): bool
    {
        try {
            $this->registeredLayer->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

}
