<?php

require('require.php');

use Propel\Runtime\Propel;
use ArduinoCoilDriver\Outputs\OutputViewQuery;
use ArduinoCoilDriver\Drivers\DriverOutputQuery;
use ArduinoCoilDriver\Outputs\OutputView;
use ArduinoCoilDriver\Outputs\Map\OutputViewTableMap;
use ArduinoCoilDriver\Exceptions\ValidationException;

function getOutputGroupFromGet($returnUrl = 'groups.php') {
    global $logger;
    global $templates;

    // get HTTP_GET id
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    // get group
    $group = OutputViewQuery::create()->findPK($id);
    
    if ($group === null) {
        $logger->addError(sprintf('Specified output group id %d doesn\'t exist', $id));
    
        echo $templates->render('error', ['message' => 'Specified output group not found.', 'returnUrl' => $returnUrl]);
        
        exit();
    }
    
    return $group;
}

$do = filter_input(INPUT_GET, 'do', FILTER_SANITIZE_STRING);

if (empty($do)) {
    // list output groups

    $get = filter_input_array(
        INPUT_GET,
        array(
            'mid'    =>  FILTER_VALIDATE_INT
        )
    );
    
    $groups = OutputViewQuery::create()->orderByName()->find();
    
    echo $templates->render('groups', ['groups' => $groups, 'messageId' => $get['mid']]);
} elseif ($do === 'new') {
    // create a new group
    
    // check for POST data
    $post = filter_input_array(
        INPUT_POST,
        array(
            'display_order'     =>  FILTER_VALIDATE_INT,
            'name'              =>  FILTER_SANITIZE_STRING,
            'driver_outputs'    =>  array(
                                    'filter'    => FILTER_VALIDATE_INT,
                                    'flags'     => FILTER_REQUIRE_ARRAY
                                ),
            'driver_outputs_display_order'  =>  array(
                                    'filter'    => FILTER_VALIDATE_INT,
                                    'flags'     => FILTER_REQUIRE_ARRAY
                                )
        )
    );
    
    if (! empty($post)) {
        // new group submitted
        
        // get driver outputs specified
        $driverOutputs = array();

        if (count($post['driver_outputs'])) {
            for ($i = 0; $i < count($post['driver_outputs']); $i++) {
                $driverOutputId = $post['driver_outputs'][$i];
                $displayOrder = $post['driver_outputs_display_order'][$i];
                
                $driverOutput = DriverOutputQuery::create()->findPK($driverOutputId);
                
                if ($driverOutput === null) {
                    // invalid driver output specified
                    
                    $logger->addError(sprintf('Specified driver output id %d doesn\'t exist', $driverOutputId));
                    
                    echo $templates->render('error', ['message' => 'Invalid driver output id specified', 'returnUrl' => 'groups.php?do=new']);
                    exit();
                }
                
                $driverOutputs[] = array($driverOutput, $displayOrder);
            }
        }
        
        // create group
        try {
            OutputView::create($post['name'], $post['display_order'], $driverOutputs);
                
            header('Location: groups.php?mid=1');
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
        }
    }
    
    echo $templates->render('groups-add', ['driverOutputs' => DriverOutputQuery::create()->orderByDriverId()->orderByName()->find(), 'errors' => $errors]);
} elseif ($do === 'edit') {
    // edit a group
    
    // get group
    $group = getOutputGroupFromGet();
    
    // check for POST data
    $post = filter_input_array(
        INPUT_POST,
        array(
            'display_order'     =>  FILTER_VALIDATE_INT,
            'name'              =>  FILTER_SANITIZE_STRING,
            'driver_outputs'    =>  array(
                                    'filter'    => FILTER_VALIDATE_INT,
                                    'flags'     => FILTER_REQUIRE_ARRAY
                                ),
            'driver_outputs_display_order'  =>  array(
                                    'filter'    => FILTER_VALIDATE_INT,
                                    'flags'     => FILTER_REQUIRE_ARRAY
                                )
        )
    );
    
    // process HTTP_POST data if submitted
    if (! empty($post)) {
        $group->setDisplayOrder($post['display_order']);
        $group->setName($post['name']);
        
        // get driver outputs specified
        $driverOutputs = array();

        if (count($post['driver_outputs'])) {
            for ($i = 0; $i < count($post['driver_outputs']); $i++) {
                $driverOutputId = $post['driver_outputs'][$i];
                $displayOrder = $post['driver_outputs_display_order'][$i];
                
                $driverOutput = DriverOutputQuery::create()->findPK($driverOutputId);
                
                if ($driverOutput === null) {
                    // invalid driver output specified
                    
                    $logger->addError(sprintf('Specified driver output id %d doesn\'t exist', $driverOutputId));
                    
                    echo $templates->render('error', ['message' => 'Invalid driver output id specified', 'returnUrl' => 'groups.php?do=edit&amp;id=' . $group->getId()]);
                    exit();
                }
                
                $driverOutputs[] = array($driverOutput, $displayOrder);
            }
        }
        
        // empty errors array
        $errors = array();
        
        try {
            // replace existing driver outputs
            $group->replaceOutputs($driverOutputs);
        } catch (ValidationException $e) {
            $errors = array_merge($errors, $e->getErrors());
        }
        
        if ($group->validate() && count($errors) === 0) {
            $group->save();
        
            header('Location: groups.php?mid=2');
        } else {
            // compile list of errors
            $errors = array_merge($errors, sortValidationErrorsByProperty($group));
        }
    }
    
    // create list of checked driver output ids
    $checkedOutputIds = array();
    $checkedOutputDisplayOrders = array();
    
    foreach ($group->getOutputViewOutputs() as $outputViewOutput) {
        $id = $outputViewOutput->getDriverOutput()->getId();
        
        $checkedOutputIds[] = $id;
        $checkedOutputDisplayOrders[$id] = $outputViewOutput->getDisplayOrder();
    }
    
    echo $templates->render('groups-edit', ['group' => $group, 'checkedOutputIds' => $checkedOutputIds, 'checkedOutputDisplayOrders' => $checkedOutputDisplayOrders, 'driverOutputs' => DriverOutputQuery::create()->orderByDriverId()->orderByName()->find(), 'errors' => $errors]);
} elseif ($do === 'delete') {
    // delete a group
    
    // get output group
    $group = getOutputGroupFromGet();
    
    // check for POST data
    $confirm = filter_input(INPUT_POST, 'confirm', FILTER_VALIDATE_BOOLEAN);
    
    // process HTTP_POST data if submitted
    if ($confirm) {
        // get a write connection
        $connection = Propel::getWriteConnection(OutputViewTableMap::DATABASE_NAME);
        
        // start a transaction
        $connection->beginTransaction();
        
        // delete
        $group->delete();
        
        // commit
        $connection->commit();
        
        header('Location: groups.php?mid=3');
    }
    
    echo $templates->render('groups-delete', ['group' => $group]);
}

?>