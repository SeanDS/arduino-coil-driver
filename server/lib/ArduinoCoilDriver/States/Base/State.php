<?php

namespace ArduinoCoilDriver\States\Base;

use \DateTime;
use \Exception;
use \PDO;
use ArduinoCoilDriver\Drivers\DriverPinValue;
use ArduinoCoilDriver\Drivers\DriverPinValueQuery;
use ArduinoCoilDriver\Drivers\Base\DriverPinValue as BaseDriverPinValue;
use ArduinoCoilDriver\States\State as ChildState;
use ArduinoCoilDriver\States\StateBookmark as ChildStateBookmark;
use ArduinoCoilDriver\States\StateBookmarkQuery as ChildStateBookmarkQuery;
use ArduinoCoilDriver\States\StateQuery as ChildStateQuery;
use ArduinoCoilDriver\States\Map\StateTableMap;
use ArduinoCoilDriver\Users\User;
use ArduinoCoilDriver\Users\UserQuery;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\LogicException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;

/**
 * Base class that represents a row from the 'states' table.
 *
 *
 *
* @package    propel.generator.ArduinoCoilDriver.States.Base
*/
abstract class State implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\ArduinoCoilDriver\\States\\Map\\StateTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var boolean
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var boolean
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = array();

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = array();

    /**
     * The value for the id field.
     *
     * @var        int
     */
    protected $id;

    /**
     * The value for the user_id field.
     *
     * @var        int
     */
    protected $user_id;

    /**
     * The value for the time field.
     *
     * @var        \DateTime
     */
    protected $time;

    /**
     * @var        User
     */
    protected $aUser;

    /**
     * @var        ObjectCollection|DriverPinValue[] Collection to store aggregation of DriverPinValue objects.
     */
    protected $collDriverPinValues;
    protected $collDriverPinValuesPartial;

    /**
     * @var        ObjectCollection|ChildStateBookmark[] Collection to store aggregation of ChildStateBookmark objects.
     */
    protected $collStateBookmarks;
    protected $collStateBookmarksPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|DriverPinValue[]
     */
    protected $driverPinValuesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildStateBookmark[]
     */
    protected $stateBookmarksScheduledForDeletion = null;

    /**
     * Initializes internal state of ArduinoCoilDriver\States\Base\State object.
     */
    public function __construct()
    {
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return boolean True if the object has been modified.
     */
    public function isModified()
    {
        return !!$this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return boolean True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return boolean true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param boolean $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (boolean) $b;
    }

    /**
     * Whether this object has been deleted.
     * @return boolean The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param  boolean $b The deleted state of this object.
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (boolean) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            if (isset($this->modifiedColumns[$col])) {
                unset($this->modifiedColumns[$col]);
            }
        } else {
            $this->modifiedColumns = array();
        }
    }

    /**
     * Compares this with another <code>State</code> instance.  If
     * <code>obj</code> is an instance of <code>State</code>, delegates to
     * <code>equals(State)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        if (!$obj instanceof static) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey() || null === $obj->getPrimaryKey()) {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param  string  $name The virtual column name
     * @return boolean
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string $name The virtual column name
     * @return mixed
     *
     * @throws PropelException
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return $this|State The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param  string  $msg
     * @param  int     $priority One of the Propel::LOG_* logging levels
     * @return boolean
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @return string  The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        $cls = new \ReflectionClass($this);
        $propertyNames = [];
        foreach($cls->getProperties() as $property) {
            $propertyNames[] = $property->getName();
        }
        return $propertyNames;
    }

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the [user_id] column value.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Get the [optionally formatted] temporal [time] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getTime($format = NULL)
    {
        if ($format === null) {
            return $this->time;
        } else {
            return $this->time instanceof \DateTime ? $this->time->format($format) : null;
        }
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return $this|\ArduinoCoilDriver\States\State The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[StateTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [user_id] column.
     *
     * @param int $v new value
     * @return $this|\ArduinoCoilDriver\States\State The current object (for fluent API support)
     */
    public function setUserId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->user_id !== $v) {
            $this->user_id = $v;
            $this->modifiedColumns[StateTableMap::COL_USER_ID] = true;
        }

        if ($this->aUser !== null && $this->aUser->getId() !== $v) {
            $this->aUser = null;
        }

        return $this;
    } // setUserId()

    /**
     * Sets the value of [time] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\ArduinoCoilDriver\States\State The current object (for fluent API support)
     */
    public function setTime($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->time !== null || $dt !== null) {
            if ($this->time === null || $dt === null || $dt->format("Y-m-d H:i:s") !== $this->time->format("Y-m-d H:i:s")) {
                $this->time = $dt === null ? null : clone $dt;
                $this->modifiedColumns[StateTableMap::COL_TIME] = true;
            }
        } // if either are not null

        return $this;
    } // setTime()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array   $row       The row returned by DataFetcher->fetch().
     * @param int     $startcol  0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @param string  $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : StateTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : StateTableMap::translateFieldName('UserId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->user_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : StateTableMap::translateFieldName('Time', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->time = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 3; // 3 = StateTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\ArduinoCoilDriver\\States\\State'), 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
        if ($this->aUser !== null && $this->user_id !== $this->aUser->getId()) {
            $this->aUser = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(StateTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildStateQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aUser = null;
            $this->collDriverPinValues = null;

            $this->collStateBookmarks = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see State::setDeleted()
     * @see State::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(StateTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildStateQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $this->setDeleted(true);
            }
        });
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(StateTableMap::DATABASE_NAME);
        }

        return $con->transaction(function () use ($con) {
            $isInsert = $this->isNew();
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                StateTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }

            return $affectedRows;
        });
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aUser !== null) {
                if ($this->aUser->isModified() || $this->aUser->isNew()) {
                    $affectedRows += $this->aUser->save($con);
                }
                $this->setUser($this->aUser);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                    $affectedRows += 1;
                } else {
                    $affectedRows += $this->doUpdate($con);
                }
                $this->resetModified();
            }

            if ($this->driverPinValuesScheduledForDeletion !== null) {
                if (!$this->driverPinValuesScheduledForDeletion->isEmpty()) {
                    \ArduinoCoilDriver\Drivers\DriverPinValueQuery::create()
                        ->filterByPrimaryKeys($this->driverPinValuesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->driverPinValuesScheduledForDeletion = null;
                }
            }

            if ($this->collDriverPinValues !== null) {
                foreach ($this->collDriverPinValues as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->stateBookmarksScheduledForDeletion !== null) {
                if (!$this->stateBookmarksScheduledForDeletion->isEmpty()) {
                    \ArduinoCoilDriver\States\StateBookmarkQuery::create()
                        ->filterByPrimaryKeys($this->stateBookmarksScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->stateBookmarksScheduledForDeletion = null;
                }
            }

            if ($this->collStateBookmarks !== null) {
                foreach ($this->collStateBookmarks as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @throws PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[StateTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . StateTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(StateTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(StateTableMap::COL_USER_ID)) {
            $modifiedColumns[':p' . $index++]  = 'user_id';
        }
        if ($this->isColumnModified(StateTableMap::COL_TIME)) {
            $modifiedColumns[':p' . $index++]  = 'time';
        }

        $sql = sprintf(
            'INSERT INTO states (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'id':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'user_id':
                        $stmt->bindValue($identifier, $this->user_id, PDO::PARAM_INT);
                        break;
                    case 'time':
                        $stmt->bindValue($identifier, $this->time ? $this->time->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @return Integer Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = StateTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getUserId();
                break;
            case 2:
                return $this->getTime();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {

        if (isset($alreadyDumpedObjects['State'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['State'][$this->hashCode()] = true;
        $keys = StateTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getUserId(),
            $keys[2] => $this->getTime(),
        );

        $utc = new \DateTimeZone('utc');
        if ($result[$keys[2]] instanceof \DateTime) {
            // When changing timezone we don't want to change existing instances
            $dateTime = clone $result[$keys[2]];
            $result[$keys[2]] = $dateTime->setTimezone($utc)->format('Y-m-d\TH:i:s\Z');
        }

        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aUser) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'user';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'users';
                        break;
                    default:
                        $key = 'User';
                }

                $result[$key] = $this->aUser->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collDriverPinValues) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'driverPinValues';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'driver_pin_valuess';
                        break;
                    default:
                        $key = 'DriverPinValues';
                }

                $result[$key] = $this->collDriverPinValues->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collStateBookmarks) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'stateBookmarks';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'state_bookmarkss';
                        break;
                    default:
                        $key = 'StateBookmarks';
                }

                $result[$key] = $this->collStateBookmarks->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param  string $name
     * @param  mixed  $value field value
     * @param  string $type The type of fieldname the $name is of:
     *                one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                Defaults to TableMap::TYPE_PHPNAME.
     * @return $this|\ArduinoCoilDriver\States\State
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = StateTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\ArduinoCoilDriver\States\State
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setUserId($value);
                break;
            case 2:
                $this->setTime($value);
                break;
        } // switch()

        return $this;
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = StateTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setUserId($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setTime($arr[$keys[2]]);
        }
    }

     /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     * @param string $keyType The type of keys the array uses.
     *
     * @return $this|\ArduinoCoilDriver\States\State The current object, for fluid interface
     */
    public function importFrom($parser, $data, $keyType = TableMap::TYPE_PHPNAME)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), $keyType);

        return $this;
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(StateTableMap::DATABASE_NAME);

        if ($this->isColumnModified(StateTableMap::COL_ID)) {
            $criteria->add(StateTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(StateTableMap::COL_USER_ID)) {
            $criteria->add(StateTableMap::COL_USER_ID, $this->user_id);
        }
        if ($this->isColumnModified(StateTableMap::COL_TIME)) {
            $criteria->add(StateTableMap::COL_TIME, $this->time);
        }

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @throws LogicException if no primary key is defined
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = ChildStateQuery::create();
        $criteria->add(StateTableMap::COL_ID, $this->id);

        return $criteria;
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        $validPk = null !== $this->getId();

        $validPrimaryKeyFKs = 0;
        $primaryKeyFKs = [];

        if ($validPk) {
            return crc32(json_encode($this->getPrimaryKey(), JSON_UNESCAPED_UNICODE));
        } elseif ($validPrimaryKeyFKs) {
            return crc32(json_encode($primaryKeyFKs, JSON_UNESCAPED_UNICODE));
        }

        return spl_object_hash($this);
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {
        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \ArduinoCoilDriver\States\State (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setUserId($this->getUserId());
        $copyObj->setTime($this->getTime());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getDriverPinValues() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDriverPinValue($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getStateBookmarks() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addStateBookmark($relObj->copy($deepCopy));
                }
            }

        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param  boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return \ArduinoCoilDriver\States\State Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Declares an association between this object and a User object.
     *
     * @param  User $v
     * @return $this|\ArduinoCoilDriver\States\State The current object (for fluent API support)
     * @throws PropelException
     */
    public function setUser(User $v = null)
    {
        if ($v === null) {
            $this->setUserId(NULL);
        } else {
            $this->setUserId($v->getId());
        }

        $this->aUser = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the User object, it will not be re-added.
        if ($v !== null) {
            $v->addState($this);
        }


        return $this;
    }


    /**
     * Get the associated User object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return User The associated User object.
     * @throws PropelException
     */
    public function getUser(ConnectionInterface $con = null)
    {
        if ($this->aUser === null && ($this->user_id !== null)) {
            $this->aUser = UserQuery::create()->findPk($this->user_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aUser->addStates($this);
             */
        }

        return $this->aUser;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('DriverPinValue' == $relationName) {
            return $this->initDriverPinValues();
        }
        if ('StateBookmark' == $relationName) {
            return $this->initStateBookmarks();
        }
    }

    /**
     * Clears out the collDriverPinValues collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDriverPinValues()
     */
    public function clearDriverPinValues()
    {
        $this->collDriverPinValues = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDriverPinValues collection loaded partially.
     */
    public function resetPartialDriverPinValues($v = true)
    {
        $this->collDriverPinValuesPartial = $v;
    }

    /**
     * Initializes the collDriverPinValues collection.
     *
     * By default this just sets the collDriverPinValues collection to an empty array (like clearcollDriverPinValues());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDriverPinValues($overrideExisting = true)
    {
        if (null !== $this->collDriverPinValues && !$overrideExisting) {
            return;
        }
        $this->collDriverPinValues = new ObjectCollection();
        $this->collDriverPinValues->setModel('\ArduinoCoilDriver\Drivers\DriverPinValue');
    }

    /**
     * Gets an array of DriverPinValue objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildState is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|DriverPinValue[] List of DriverPinValue objects
     * @throws PropelException
     */
    public function getDriverPinValues(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDriverPinValuesPartial && !$this->isNew();
        if (null === $this->collDriverPinValues || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDriverPinValues) {
                // return empty collection
                $this->initDriverPinValues();
            } else {
                $collDriverPinValues = DriverPinValueQuery::create(null, $criteria)
                    ->filterByState($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDriverPinValuesPartial && count($collDriverPinValues)) {
                        $this->initDriverPinValues(false);

                        foreach ($collDriverPinValues as $obj) {
                            if (false == $this->collDriverPinValues->contains($obj)) {
                                $this->collDriverPinValues->append($obj);
                            }
                        }

                        $this->collDriverPinValuesPartial = true;
                    }

                    return $collDriverPinValues;
                }

                if ($partial && $this->collDriverPinValues) {
                    foreach ($this->collDriverPinValues as $obj) {
                        if ($obj->isNew()) {
                            $collDriverPinValues[] = $obj;
                        }
                    }
                }

                $this->collDriverPinValues = $collDriverPinValues;
                $this->collDriverPinValuesPartial = false;
            }
        }

        return $this->collDriverPinValues;
    }

    /**
     * Sets a collection of DriverPinValue objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $driverPinValues A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildState The current object (for fluent API support)
     */
    public function setDriverPinValues(Collection $driverPinValues, ConnectionInterface $con = null)
    {
        /** @var DriverPinValue[] $driverPinValuesToDelete */
        $driverPinValuesToDelete = $this->getDriverPinValues(new Criteria(), $con)->diff($driverPinValues);


        $this->driverPinValuesScheduledForDeletion = $driverPinValuesToDelete;

        foreach ($driverPinValuesToDelete as $driverPinValueRemoved) {
            $driverPinValueRemoved->setState(null);
        }

        $this->collDriverPinValues = null;
        foreach ($driverPinValues as $driverPinValue) {
            $this->addDriverPinValue($driverPinValue);
        }

        $this->collDriverPinValues = $driverPinValues;
        $this->collDriverPinValuesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related BaseDriverPinValue objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related BaseDriverPinValue objects.
     * @throws PropelException
     */
    public function countDriverPinValues(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDriverPinValuesPartial && !$this->isNew();
        if (null === $this->collDriverPinValues || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDriverPinValues) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDriverPinValues());
            }

            $query = DriverPinValueQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByState($this)
                ->count($con);
        }

        return count($this->collDriverPinValues);
    }

    /**
     * Method called to associate a DriverPinValue object to this object
     * through the DriverPinValue foreign key attribute.
     *
     * @param  DriverPinValue $l DriverPinValue
     * @return $this|\ArduinoCoilDriver\States\State The current object (for fluent API support)
     */
    public function addDriverPinValue(DriverPinValue $l)
    {
        if ($this->collDriverPinValues === null) {
            $this->initDriverPinValues();
            $this->collDriverPinValuesPartial = true;
        }

        if (!$this->collDriverPinValues->contains($l)) {
            $this->doAddDriverPinValue($l);
        }

        return $this;
    }

    /**
     * @param DriverPinValue $driverPinValue The DriverPinValue object to add.
     */
    protected function doAddDriverPinValue(DriverPinValue $driverPinValue)
    {
        $this->collDriverPinValues[]= $driverPinValue;
        $driverPinValue->setState($this);
    }

    /**
     * @param  DriverPinValue $driverPinValue The DriverPinValue object to remove.
     * @return $this|ChildState The current object (for fluent API support)
     */
    public function removeDriverPinValue(DriverPinValue $driverPinValue)
    {
        if ($this->getDriverPinValues()->contains($driverPinValue)) {
            $pos = $this->collDriverPinValues->search($driverPinValue);
            $this->collDriverPinValues->remove($pos);
            if (null === $this->driverPinValuesScheduledForDeletion) {
                $this->driverPinValuesScheduledForDeletion = clone $this->collDriverPinValues;
                $this->driverPinValuesScheduledForDeletion->clear();
            }
            $this->driverPinValuesScheduledForDeletion[]= clone $driverPinValue;
            $driverPinValue->setState(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this State is new, it will return
     * an empty collection; or if this State has previously
     * been saved, it will retrieve related DriverPinValues from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in State.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|DriverPinValue[] List of DriverPinValue objects
     */
    public function getDriverPinValuesJoinDriverPin(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = DriverPinValueQuery::create(null, $criteria);
        $query->joinWith('DriverPin', $joinBehavior);

        return $this->getDriverPinValues($query, $con);
    }

    /**
     * Clears out the collStateBookmarks collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addStateBookmarks()
     */
    public function clearStateBookmarks()
    {
        $this->collStateBookmarks = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collStateBookmarks collection loaded partially.
     */
    public function resetPartialStateBookmarks($v = true)
    {
        $this->collStateBookmarksPartial = $v;
    }

    /**
     * Initializes the collStateBookmarks collection.
     *
     * By default this just sets the collStateBookmarks collection to an empty array (like clearcollStateBookmarks());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initStateBookmarks($overrideExisting = true)
    {
        if (null !== $this->collStateBookmarks && !$overrideExisting) {
            return;
        }
        $this->collStateBookmarks = new ObjectCollection();
        $this->collStateBookmarks->setModel('\ArduinoCoilDriver\States\StateBookmark');
    }

    /**
     * Gets an array of ChildStateBookmark objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildState is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildStateBookmark[] List of ChildStateBookmark objects
     * @throws PropelException
     */
    public function getStateBookmarks(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collStateBookmarksPartial && !$this->isNew();
        if (null === $this->collStateBookmarks || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collStateBookmarks) {
                // return empty collection
                $this->initStateBookmarks();
            } else {
                $collStateBookmarks = ChildStateBookmarkQuery::create(null, $criteria)
                    ->filterByState($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collStateBookmarksPartial && count($collStateBookmarks)) {
                        $this->initStateBookmarks(false);

                        foreach ($collStateBookmarks as $obj) {
                            if (false == $this->collStateBookmarks->contains($obj)) {
                                $this->collStateBookmarks->append($obj);
                            }
                        }

                        $this->collStateBookmarksPartial = true;
                    }

                    return $collStateBookmarks;
                }

                if ($partial && $this->collStateBookmarks) {
                    foreach ($this->collStateBookmarks as $obj) {
                        if ($obj->isNew()) {
                            $collStateBookmarks[] = $obj;
                        }
                    }
                }

                $this->collStateBookmarks = $collStateBookmarks;
                $this->collStateBookmarksPartial = false;
            }
        }

        return $this->collStateBookmarks;
    }

    /**
     * Sets a collection of ChildStateBookmark objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $stateBookmarks A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildState The current object (for fluent API support)
     */
    public function setStateBookmarks(Collection $stateBookmarks, ConnectionInterface $con = null)
    {
        /** @var ChildStateBookmark[] $stateBookmarksToDelete */
        $stateBookmarksToDelete = $this->getStateBookmarks(new Criteria(), $con)->diff($stateBookmarks);


        $this->stateBookmarksScheduledForDeletion = $stateBookmarksToDelete;

        foreach ($stateBookmarksToDelete as $stateBookmarkRemoved) {
            $stateBookmarkRemoved->setState(null);
        }

        $this->collStateBookmarks = null;
        foreach ($stateBookmarks as $stateBookmark) {
            $this->addStateBookmark($stateBookmark);
        }

        $this->collStateBookmarks = $stateBookmarks;
        $this->collStateBookmarksPartial = false;

        return $this;
    }

    /**
     * Returns the number of related StateBookmark objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related StateBookmark objects.
     * @throws PropelException
     */
    public function countStateBookmarks(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collStateBookmarksPartial && !$this->isNew();
        if (null === $this->collStateBookmarks || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collStateBookmarks) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getStateBookmarks());
            }

            $query = ChildStateBookmarkQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByState($this)
                ->count($con);
        }

        return count($this->collStateBookmarks);
    }

    /**
     * Method called to associate a ChildStateBookmark object to this object
     * through the ChildStateBookmark foreign key attribute.
     *
     * @param  ChildStateBookmark $l ChildStateBookmark
     * @return $this|\ArduinoCoilDriver\States\State The current object (for fluent API support)
     */
    public function addStateBookmark(ChildStateBookmark $l)
    {
        if ($this->collStateBookmarks === null) {
            $this->initStateBookmarks();
            $this->collStateBookmarksPartial = true;
        }

        if (!$this->collStateBookmarks->contains($l)) {
            $this->doAddStateBookmark($l);
        }

        return $this;
    }

    /**
     * @param ChildStateBookmark $stateBookmark The ChildStateBookmark object to add.
     */
    protected function doAddStateBookmark(ChildStateBookmark $stateBookmark)
    {
        $this->collStateBookmarks[]= $stateBookmark;
        $stateBookmark->setState($this);
    }

    /**
     * @param  ChildStateBookmark $stateBookmark The ChildStateBookmark object to remove.
     * @return $this|ChildState The current object (for fluent API support)
     */
    public function removeStateBookmark(ChildStateBookmark $stateBookmark)
    {
        if ($this->getStateBookmarks()->contains($stateBookmark)) {
            $pos = $this->collStateBookmarks->search($stateBookmark);
            $this->collStateBookmarks->remove($pos);
            if (null === $this->stateBookmarksScheduledForDeletion) {
                $this->stateBookmarksScheduledForDeletion = clone $this->collStateBookmarks;
                $this->stateBookmarksScheduledForDeletion->clear();
            }
            $this->stateBookmarksScheduledForDeletion[]= clone $stateBookmark;
            $stateBookmark->setState(null);
        }

        return $this;
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        if (null !== $this->aUser) {
            $this->aUser->removeState($this);
        }
        $this->id = null;
        $this->user_id = null;
        $this->time = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references and back-references to other model objects or collections of model objects.
     *
     * This method is used to reset all php object references (not the actual reference in the database).
     * Necessary for object serialisation.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collDriverPinValues) {
                foreach ($this->collDriverPinValues as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collStateBookmarks) {
                foreach ($this->collStateBookmarks as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collDriverPinValues = null;
        $this->collStateBookmarks = null;
        $this->aUser = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(StateTableMap::DEFAULT_STRING_FORMAT);
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {

    }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
