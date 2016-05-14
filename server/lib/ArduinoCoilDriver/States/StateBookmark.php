<?php

namespace ArduinoCoilDriver\States;

use Propel\Runtime\Propel;
use ArduinoCoilDriver\States\Base\StateBookmark as BaseStateBookmark;
use ArduinoCoilDriver\States\Map\StateBookmarkTableMap;
use ArduinoCoilDriver\States\State;
use ArduinoCoilDriver\Exceptions\ValidationException;

/**
 * Skeleton subclass for representing a row from the 'state_bookmarks' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class StateBookmark extends BaseStateBookmark
{
    public static function create(State $state, $description) {
        // create state bookmark
        $bookmark = new self();
    
        // get a write connection
        $connection = Propel::getWriteConnection(StateBookmarkTableMap::DATABASE_NAME);
        
        // start transaction
        $connection->beginTransaction();
        
        // set parameters
        $bookmark->setDescription($description);
        
        // validate
        if (! $bookmark->validate()) {
            $connection->rollback();
            
            throw new ValidationException($bookmark);
        }
        
        // set state's bookmark to this
        $state->setStateBookmark($bookmark);
        
        // save
        $state->save();
        $bookmark->save();
        
        // commit transaction
        $connection->commit();
        
        return $bookmark;
    }
}
