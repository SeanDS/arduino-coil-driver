<?php

require('require.php');

use Propel\Runtime\Propel;
use ArduinoCoilDriver\States\State;
use ArduinoCoilDriver\States\StateQuery;
use ArduinoCoilDriver\States\StateBookmarkQuery;
use ArduinoCoilDriver\States\Map\StateTableMap;
use ArduinoCoilDriver\States\Map\StateBookmarkTableMap;
use ArduinoCoilDriver\States\StateBookmark;
use ArduinoCoilDriver\Exceptions\ValidationException;
use ArduinoCoilDriver\Exceptions\CurrentStateUndeletableException;
use ArduinoCoilDriver\Exceptions\LatestStateAlreadyLoadedException;

function getStateFromGet($returnUrl = 'states.php') {
    global $logger;
    global $templates;

    // get HTTP_GET id
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    // get state
    $state = StateQuery::create()->findPK($id);
    
    if ($state === null) {
        $logger->addError(sprintf('Specified state bookmark id %d doesn\'t exist', $id));
    
        echo $templates->render('error', ['message' => 'Specified state bookmark not found.', 'returnUrl' => $returnUrl]);
        
        exit();
    }
    
    return $state;
}

function getStateBookmarkFromGet($returnUrl = 'states.php') {
    global $logger;
    global $templates;

    // get HTTP_GET id
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    // get state
    $state = StateBookmarkQuery::create()->findPK($id);
    
    if ($state === null) {
        $logger->addError(sprintf('Specified state id %d doesn\'t exist', $id));
    
        echo $templates->render('error', ['message' => 'Specified state not found.', 'returnUrl' => $returnUrl]);
        
        exit();
    }
    
    return $state;
}

$do = filter_input(INPUT_GET, 'do', FILTER_SANITIZE_STRING);

if (empty($do)) {
    // list output groups

    $get = filter_input_array(
        INPUT_GET,
        array(
            'mid'   =>  FILTER_VALIDATE_INT,
            'page'  =>  FILTER_VALIDATE_INT,
            'spage' =>  FILTER_VALIDATE_INT
        )
    );
    
    if ($get['page'] <= 0) {
        $get['page'] = 1;
    }
    
    if ($get['spage'] <= 0) {
        $get['spage'] = 1;
    }
    
    // get current state
    $currentState = State::getCurrentState();
    
    $bookmarkedStatesPager = StateQuery::create()->orderByTime('desc')->innerJoinStateBookmark()->paginate($page = $get['page'], $maxPerPage = 25);
    $statesPager = StateQuery::create()->orderByTime('desc')->paginate($page = $get['spage'], $maxPerPage = 25);
    
    echo $templates->render('states', ['currentState' => $currentState, 'bookmarksPager' => $bookmarkedStatesPager, 'statesPager' => $statesPager, 'messageId' => $get['mid']]);
} elseif ($do === 'newbookmark') {
    // bookmark a state
    
    // get state
    $state = getStateFromGet();
    
    // check if bookmark already exists
    if ($state->getStateBookmark() != null) {
        $logger->addError(sprintf('Specified state %d already has a bookmark', $id));
    
        echo $templates->render('error', ['message' => 'Specified state already has a bookmark.', 'returnUrl' => 'states.php']);
        
        exit();
    }
    
    // check for POST data
    $post = filter_input_array(
        INPUT_POST,
        array(
            'description'   =>  FILTER_SANITIZE_STRING
        )
    );
    
    if (! in_array(null, array($post['description']), true)) {
        // new bookmark submitted
        
        // create bookmark
        try {
            StateBookmark::create($state, $post['description']);
            
            header('Location: states.php?mid=1');
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
        }
    }
    
    echo $templates->render('state-bookmarks-add', ['state' => $state, 'errors' => $errors]);
} elseif ($do === 'editbookmark') {
    // edit a bookmark
    
    // get bookmark
    $bookmark = getStateBookmarkFromGet();
    
    // check for POST data
    $post = filter_input_array(
        INPUT_POST,
        array(
            'description'   =>  FILTER_SANITIZE_STRING
        )
    );
    
    if (! in_array(null, array($post['description']), true)) {
        // bookmark edit submitted
        
        // edit bookmark
        try {
            $bookmark->setDescription($post['description']);
            $bookmark->save();
            
            header('Location: states.php?mid=2');
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
        }
    }
    
    echo $templates->render('state-bookmarks-edit', ['bookmark' => $bookmark, 'errors' => $errors]);
} elseif ($do === 'deletebookmark') {
    // delete a bookmark
    
    // get bookmark
    $bookmark = getStateBookmarkFromGet();
    
    // check for POST data
    $confirm = filter_input(INPUT_POST, 'confirm', FILTER_VALIDATE_BOOLEAN);
    
    // process HTTP_POST data if submitted
    if ($confirm) {
        // get a write connection
        $connection = Propel::getWriteConnection(StateBookmarkTableMap::DATABASE_NAME);
        
        // start a transaction
        $connection->beginTransaction();
        
        // delete
        $bookmark->delete();
        
        // commit
        $connection->commit();
        
        header('Location: states.php?mid=3');
    }
    
    echo $templates->render('state-bookmarks-delete', ['bookmark' => $bookmark]);
} elseif ($do === 'load') {
    // load a state
    
    // get state
    $state = getStateFromGet();
    
    // load state
    try {
        $state->load();
        
        // redirect to main screen with message
        header('Location: states.php?mid=5');
    } catch (LatestStateAlreadyLoadedException $e) {
        // show message saying current state is already loaded
        header('Location: states.php?mid=6');
    }
} elseif ($do === 'delete') {
    // delete a state
    
    // get state
    $state = getStateFromGet();
    
    // check for POST data
    $confirm = filter_input(INPUT_POST, 'confirm', FILTER_VALIDATE_BOOLEAN);
    
    // process HTTP_POST data if submitted
    if ($confirm) {
        // get a write connection
        $connection = Propel::getWriteConnection(StateTableMap::DATABASE_NAME);
        
        // start a transaction
        $connection->beginTransaction();
        
        try {
            // delete
            $state->delete();
        } catch (CurrentStateUndeletableException $e) {
            $connection->rollback();
    
            echo $templates->render('error', ['message' => $e->getMessage(), 'returnUrl' => 'states.php']);
            
            exit();
        }
        
        // commit
        $connection->commit();
        
        header('Location: states.php?mid=4');
    }
    
    echo $templates->render('states-delete', ['state' => $state]);
}

?>