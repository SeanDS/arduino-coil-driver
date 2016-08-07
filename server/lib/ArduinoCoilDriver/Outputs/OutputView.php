<?php

namespace ArduinoCoilDriver\Outputs;

use Propel\Runtime\Propel;
use Propel\Runtime\Connection\ConnectionInterface;
use ArduinoCoilDriver\Outputs\Base\OutputView as BaseOutputView;
use ArduinoCoilDriver\Outputs\Map\OutputViewTableMap;
use ArduinoCoilDriver\Outputs\OutputViewOutput;
use ArduinoCoilDriver\Exceptions\ValidationException;

/**
 * Skeleton subclass for representing a row from the 'output_views' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class OutputView extends BaseOutputView
{
    public static function create($name, $displayOrder, $driverOutputs) {
        // create output view
        $outputView = new self();
        
        // get a write connection
        $connection = Propel::getWriteConnection(OutputViewTableMap::DATABASE_NAME);
        
        // start transaction
        $connection->beginTransaction();
        
        // set parameters
        $outputView->setName($name);
        $outputView->setDisplayOrder($displayOrder);
        
        // validate
        if (! $outputView->validate()) {
            $connection->rollback();
            
            throw new ValidationException($outputView);
        }
        
        // save
        $outputView->save();
        
        // add output view driver outputs
        foreach ($driverOutputs as $driverOutput) {
            // only do anything if the box is ticked
            if ($driverOutput[0]) {
                // first index is the driver output, second is display order
                OutputViewOutput::createFromDriverOutput($driverOutput[0], $driverOutput[1], $outputView);
            }
        }            
        
        // commit transaction
        $connection->commit();
        
        return $outputView;
    }
    
    public function replaceOutputs($driverOutputs) {
        // replace existing outputs with new list
        
        global $infoLogger;
        
        $infoLogger->addInfo(sprintf('Replacing outputs associated with output view id %d', $this->getId()));
        
        // get a write connection
        $connection = Propel::getWriteConnection(OutputViewTableMap::DATABASE_NAME);
        
        // start transaction
        $connection->beginTransaction();
        
        // delete existing outputs
        foreach ($this->getOutputViewOutputs() as $outputViewOutput) {
            $outputViewOutput->delete();
        }
        
        // add new outputs
        foreach ($driverOutputs as $outputViewOutput) {
            // only do anything if the box is ticked
            if ($outputViewOutput[0]) {
                // first index is the driver output, second is display order
                OutputViewOutput::createFromDriverOutput($outputViewOutput[0], $outputViewOutput[1], $this);
            }
        }
        
        // commit transaction
        $connection->commit();
    }
    
    public function postInsert(ConnectionInterface $connection = null) {
        global $infoLogger;
        
        $infoLogger->addInfo(sprintf('Output view inserted with id %d', $this->getId()));
    }
    
    public function postUpdate(ConnectionInterface $connection = null) {
        global $infoLogger;
        
        $infoLogger->addInfo(sprintf('Output view id %d updated', $this->getId()));
    }
    
    public function preDelete(ConnectionInterface $connection = null) {
        if (is_null($connection)) {
            // get a write connection
            $connection = Propel::getWriteConnection(OutputViewTableMap::DATABASE_NAME);
        }
        
        // start transaction
        $connection->beginTransaction();
        
        // delete output view driver outputs
        foreach ($this->getOutputViewOutputs() as $outputViewOutput) {
            $outputViewOutput->delete();
        }
        
        // commit transaction
        $connection->commit();
        
        return true;
    }
    
    public function postDelete(ConnectionInterface $connection = null) {
        global $infoLogger;
        
        $infoLogger->addInfo(sprintf('Output view id %d deleted', $this->getId()));
    }
}
