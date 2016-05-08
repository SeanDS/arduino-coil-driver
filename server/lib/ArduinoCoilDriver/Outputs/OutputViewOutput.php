<?php

namespace ArduinoCoilDriver\Outputs;

use Propel\Runtime\Propel;
use Propel\Runtime\Connection\ConnectionInterface;
use ArduinoCoilDriver\Outputs\Base\OutputViewOutput as BaseOutputViewOutput;
use ArduinoCoilDriver\Outputs\Map\OutputViewOutputTableMap;
use ArduinoCoilDriver\Exceptions\ValidationException;

/**
 * Skeleton subclass for representing a row from the 'output_view_output' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class OutputViewOutput extends BaseOutputViewOutput
{
    public static function createFromDriverOutput($driverOutput, $displayOrder, $outputView) {
        // create new output view output
        $outputViewOutput = new self();
        
        // get a write connection
        $connection = Propel::getWriteConnection(OutputViewOutputTableMap::DATABASE_NAME);
        
        // start transaction
        $connection->beginTransaction();
        
        // set parameters
        $outputViewOutput->setOutputId($outputView->getId());
        $outputViewOutput->setDriverOutputId($driverOutput->getId());
        $outputViewOutput->setDisplayOrder($displayOrder);
        
        // validate
        if (! $outputViewOutput->validate()) {
            $connection->rollback();
            
            throw new ValidationException($outputViewOutput, "output_view_output_");
        }
        
        // save
        $outputViewOutput->save();
        
        // commit transaction
        $connection->commit();
        
        return $outputViewOutput;
    }
    
    public function postInsert(ConnectionInterface $connection = null) {
        global $logger;
        
        $logger->addInfo(sprintf('Output view output inserted with id %d', $this->getId()));
    }
    
    public function postUpdate(ConnectionInterface $connection = null) {
        global $logger;
        
        $logger->addInfo(sprintf('Output view output id %d updated', $this->getId()));
    }
    
    public function postDelete(ConnectionInterface $connection = null) {
        global $logger;
        
        $logger->addInfo(sprintf('Output view output id %d deleted', $this->getId()));
    }
}
