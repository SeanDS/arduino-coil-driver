<?php

namespace ArduinoCoilDriver\Drivers\Base;

use \Exception;
use \PDO;
use ArduinoCoilDriver\Drivers\Driver as ChildDriver;
use ArduinoCoilDriver\Drivers\DriverPin as ChildDriverPin;
use ArduinoCoilDriver\Drivers\DriverPinQuery as ChildDriverPinQuery;
use ArduinoCoilDriver\Drivers\DriverPinValue as ChildDriverPinValue;
use ArduinoCoilDriver\Drivers\DriverPinValueQuery as ChildDriverPinValueQuery;
use ArduinoCoilDriver\Drivers\DriverQuery as ChildDriverQuery;
use ArduinoCoilDriver\Drivers\Map\DriverPinTableMap;
use ArduinoCoilDriver\Outputs\OutputPin;
use ArduinoCoilDriver\Outputs\OutputPinQuery;
use ArduinoCoilDriver\Outputs\Base\OutputPin as BaseOutputPin;
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

/**
 * Base class that represents a row from the 'driver_pins' table.
 *
 *
 *
* @package    propel.generator.ArduinoCoilDriver.Drivers.Base
*/
abstract class DriverPin implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\ArduinoCoilDriver\\Drivers\\Map\\DriverPinTableMap';


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
     * The value for the driver_id field.
     *
     * @var        int
     */
    protected $driver_id;

    /**
     * The value for the pin field.
     *
     * @var        int
     */
    protected $pin;

    /**
     * The value for the type field.
     *
     * Note: this column has a database default value of: 'coarse'
     * @var        string
     */
    protected $type;

    /**
     * @var        ChildDriver
     */
    protected $aDriver;

    /**
     * @var        ObjectCollection|ChildDriverPinValue[] Collection to store aggregation of ChildDriverPinValue objects.
     */
    protected $collDriverPinValues;
    protected $collDriverPinValuesPartial;

    /**
     * @var        ObjectCollection|OutputPin[] Collection to store aggregation of OutputPin objects.
     */
    protected $collOutputPins;
    protected $collOutputPinsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildDriverPinValue[]
     */
    protected $driverPinValuesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|OutputPin[]
     */
    protected $outputPinsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->type = 'coarse';
    }

    /**
     * Initializes internal state of ArduinoCoilDriver\Drivers\Base\DriverPin object.
     * @see applyDefaults()
     */
    public function __construct()
    {
        $this->applyDefaultValues();
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
     * Compares this with another <code>DriverPin</code> instance.  If
     * <code>obj</code> is an instance of <code>DriverPin</code>, delegates to
     * <code>equals(DriverPin)</code>.  Otherwise, returns <code>false</code>.
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
     * @return $this|DriverPin The current object, for fluid interface
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
     * Get the [driver_id] column value.
     *
     * @return int
     */
    public function getDriverId()
    {
        return $this->driver_id;
    }

    /**
     * Get the [pin] column value.
     *
     * @return int
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * Get the [type] column value.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return $this|\ArduinoCoilDriver\Drivers\DriverPin The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[DriverPinTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [driver_id] column.
     *
     * @param int $v new value
     * @return $this|\ArduinoCoilDriver\Drivers\DriverPin The current object (for fluent API support)
     */
    public function setDriverId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->driver_id !== $v) {
            $this->driver_id = $v;
            $this->modifiedColumns[DriverPinTableMap::COL_DRIVER_ID] = true;
        }

        if ($this->aDriver !== null && $this->aDriver->getId() !== $v) {
            $this->aDriver = null;
        }

        return $this;
    } // setDriverId()

    /**
     * Set the value of [pin] column.
     *
     * @param int $v new value
     * @return $this|\ArduinoCoilDriver\Drivers\DriverPin The current object (for fluent API support)
     */
    public function setPin($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->pin !== $v) {
            $this->pin = $v;
            $this->modifiedColumns[DriverPinTableMap::COL_PIN] = true;
        }

        return $this;
    } // setPin()

    /**
     * Set the value of [type] column.
     *
     * @param string $v new value
     * @return $this|\ArduinoCoilDriver\Drivers\DriverPin The current object (for fluent API support)
     */
    public function setType($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->type !== $v) {
            $this->type = $v;
            $this->modifiedColumns[DriverPinTableMap::COL_TYPE] = true;
        }

        return $this;
    } // setType()

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
            if ($this->type !== 'coarse') {
                return false;
            }

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

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : DriverPinTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : DriverPinTableMap::translateFieldName('DriverId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->driver_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : DriverPinTableMap::translateFieldName('Pin', TableMap::TYPE_PHPNAME, $indexType)];
            $this->pin = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : DriverPinTableMap::translateFieldName('Type', TableMap::TYPE_PHPNAME, $indexType)];
            $this->type = (null !== $col) ? (string) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 4; // 4 = DriverPinTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\ArduinoCoilDriver\\Drivers\\DriverPin'), 0, $e);
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
        if ($this->aDriver !== null && $this->driver_id !== $this->aDriver->getId()) {
            $this->aDriver = null;
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
            $con = Propel::getServiceContainer()->getReadConnection(DriverPinTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildDriverPinQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aDriver = null;
            $this->collDriverPinValues = null;

            $this->collOutputPins = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see DriverPin::setDeleted()
     * @see DriverPin::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(DriverPinTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildDriverPinQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(DriverPinTableMap::DATABASE_NAME);
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
                DriverPinTableMap::addInstanceToPool($this);
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

            if ($this->aDriver !== null) {
                if ($this->aDriver->isModified() || $this->aDriver->isNew()) {
                    $affectedRows += $this->aDriver->save($con);
                }
                $this->setDriver($this->aDriver);
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

            if ($this->outputPinsScheduledForDeletion !== null) {
                if (!$this->outputPinsScheduledForDeletion->isEmpty()) {
                    \ArduinoCoilDriver\Outputs\OutputPinQuery::create()
                        ->filterByPrimaryKeys($this->outputPinsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->outputPinsScheduledForDeletion = null;
                }
            }

            if ($this->collOutputPins !== null) {
                foreach ($this->collOutputPins as $referrerFK) {
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

        $this->modifiedColumns[DriverPinTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . DriverPinTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(DriverPinTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(DriverPinTableMap::COL_DRIVER_ID)) {
            $modifiedColumns[':p' . $index++]  = 'driver_id';
        }
        if ($this->isColumnModified(DriverPinTableMap::COL_PIN)) {
            $modifiedColumns[':p' . $index++]  = 'pin';
        }
        if ($this->isColumnModified(DriverPinTableMap::COL_TYPE)) {
            $modifiedColumns[':p' . $index++]  = 'type';
        }

        $sql = sprintf(
            'INSERT INTO driver_pins (%s) VALUES (%s)',
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
                    case 'driver_id':
                        $stmt->bindValue($identifier, $this->driver_id, PDO::PARAM_INT);
                        break;
                    case 'pin':
                        $stmt->bindValue($identifier, $this->pin, PDO::PARAM_INT);
                        break;
                    case 'type':
                        $stmt->bindValue($identifier, $this->type, PDO::PARAM_STR);
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
        $pos = DriverPinTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getDriverId();
                break;
            case 2:
                return $this->getPin();
                break;
            case 3:
                return $this->getType();
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

        if (isset($alreadyDumpedObjects['DriverPin'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['DriverPin'][$this->hashCode()] = true;
        $keys = DriverPinTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getDriverId(),
            $keys[2] => $this->getPin(),
            $keys[3] => $this->getType(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aDriver) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'driver';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'drivers';
                        break;
                    default:
                        $key = 'Driver';
                }

                $result[$key] = $this->aDriver->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
            if (null !== $this->collOutputPins) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'outputPins';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'output_pinss';
                        break;
                    default:
                        $key = 'OutputPins';
                }

                $result[$key] = $this->collOutputPins->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
     * @return $this|\ArduinoCoilDriver\Drivers\DriverPin
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = DriverPinTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\ArduinoCoilDriver\Drivers\DriverPin
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setDriverId($value);
                break;
            case 2:
                $this->setPin($value);
                break;
            case 3:
                $this->setType($value);
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
        $keys = DriverPinTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setDriverId($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setPin($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setType($arr[$keys[3]]);
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
     * @return $this|\ArduinoCoilDriver\Drivers\DriverPin The current object, for fluid interface
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
        $criteria = new Criteria(DriverPinTableMap::DATABASE_NAME);

        if ($this->isColumnModified(DriverPinTableMap::COL_ID)) {
            $criteria->add(DriverPinTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(DriverPinTableMap::COL_DRIVER_ID)) {
            $criteria->add(DriverPinTableMap::COL_DRIVER_ID, $this->driver_id);
        }
        if ($this->isColumnModified(DriverPinTableMap::COL_PIN)) {
            $criteria->add(DriverPinTableMap::COL_PIN, $this->pin);
        }
        if ($this->isColumnModified(DriverPinTableMap::COL_TYPE)) {
            $criteria->add(DriverPinTableMap::COL_TYPE, $this->type);
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
        $criteria = ChildDriverPinQuery::create();
        $criteria->add(DriverPinTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \ArduinoCoilDriver\Drivers\DriverPin (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDriverId($this->getDriverId());
        $copyObj->setPin($this->getPin());
        $copyObj->setType($this->getType());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getDriverPinValues() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDriverPinValue($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getOutputPins() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addOutputPin($relObj->copy($deepCopy));
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
     * @return \ArduinoCoilDriver\Drivers\DriverPin Clone of current object.
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
     * Declares an association between this object and a ChildDriver object.
     *
     * @param  ChildDriver $v
     * @return $this|\ArduinoCoilDriver\Drivers\DriverPin The current object (for fluent API support)
     * @throws PropelException
     */
    public function setDriver(ChildDriver $v = null)
    {
        if ($v === null) {
            $this->setDriverId(NULL);
        } else {
            $this->setDriverId($v->getId());
        }

        $this->aDriver = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildDriver object, it will not be re-added.
        if ($v !== null) {
            $v->addDriverPin($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildDriver object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildDriver The associated ChildDriver object.
     * @throws PropelException
     */
    public function getDriver(ConnectionInterface $con = null)
    {
        if ($this->aDriver === null && ($this->driver_id !== null)) {
            $this->aDriver = ChildDriverQuery::create()->findPk($this->driver_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aDriver->addDriverPins($this);
             */
        }

        return $this->aDriver;
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
        if ('OutputPin' == $relationName) {
            return $this->initOutputPins();
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
     * Gets an array of ChildDriverPinValue objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDriverPin is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildDriverPinValue[] List of ChildDriverPinValue objects
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
                $collDriverPinValues = ChildDriverPinValueQuery::create(null, $criteria)
                    ->filterByDriverPin($this)
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
     * Sets a collection of ChildDriverPinValue objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $driverPinValues A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildDriverPin The current object (for fluent API support)
     */
    public function setDriverPinValues(Collection $driverPinValues, ConnectionInterface $con = null)
    {
        /** @var ChildDriverPinValue[] $driverPinValuesToDelete */
        $driverPinValuesToDelete = $this->getDriverPinValues(new Criteria(), $con)->diff($driverPinValues);


        $this->driverPinValuesScheduledForDeletion = $driverPinValuesToDelete;

        foreach ($driverPinValuesToDelete as $driverPinValueRemoved) {
            $driverPinValueRemoved->setDriverPin(null);
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
     * Returns the number of related DriverPinValue objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DriverPinValue objects.
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

            $query = ChildDriverPinValueQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDriverPin($this)
                ->count($con);
        }

        return count($this->collDriverPinValues);
    }

    /**
     * Method called to associate a ChildDriverPinValue object to this object
     * through the ChildDriverPinValue foreign key attribute.
     *
     * @param  ChildDriverPinValue $l ChildDriverPinValue
     * @return $this|\ArduinoCoilDriver\Drivers\DriverPin The current object (for fluent API support)
     */
    public function addDriverPinValue(ChildDriverPinValue $l)
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
     * @param ChildDriverPinValue $driverPinValue The ChildDriverPinValue object to add.
     */
    protected function doAddDriverPinValue(ChildDriverPinValue $driverPinValue)
    {
        $this->collDriverPinValues[]= $driverPinValue;
        $driverPinValue->setDriverPin($this);
    }

    /**
     * @param  ChildDriverPinValue $driverPinValue The ChildDriverPinValue object to remove.
     * @return $this|ChildDriverPin The current object (for fluent API support)
     */
    public function removeDriverPinValue(ChildDriverPinValue $driverPinValue)
    {
        if ($this->getDriverPinValues()->contains($driverPinValue)) {
            $pos = $this->collDriverPinValues->search($driverPinValue);
            $this->collDriverPinValues->remove($pos);
            if (null === $this->driverPinValuesScheduledForDeletion) {
                $this->driverPinValuesScheduledForDeletion = clone $this->collDriverPinValues;
                $this->driverPinValuesScheduledForDeletion->clear();
            }
            $this->driverPinValuesScheduledForDeletion[]= clone $driverPinValue;
            $driverPinValue->setDriverPin(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this DriverPin is new, it will return
     * an empty collection; or if this DriverPin has previously
     * been saved, it will retrieve related DriverPinValues from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in DriverPin.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildDriverPinValue[] List of ChildDriverPinValue objects
     */
    public function getDriverPinValuesJoinState(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildDriverPinValueQuery::create(null, $criteria);
        $query->joinWith('State', $joinBehavior);

        return $this->getDriverPinValues($query, $con);
    }

    /**
     * Clears out the collOutputPins collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addOutputPins()
     */
    public function clearOutputPins()
    {
        $this->collOutputPins = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collOutputPins collection loaded partially.
     */
    public function resetPartialOutputPins($v = true)
    {
        $this->collOutputPinsPartial = $v;
    }

    /**
     * Initializes the collOutputPins collection.
     *
     * By default this just sets the collOutputPins collection to an empty array (like clearcollOutputPins());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initOutputPins($overrideExisting = true)
    {
        if (null !== $this->collOutputPins && !$overrideExisting) {
            return;
        }
        $this->collOutputPins = new ObjectCollection();
        $this->collOutputPins->setModel('\ArduinoCoilDriver\Outputs\OutputPin');
    }

    /**
     * Gets an array of OutputPin objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDriverPin is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|OutputPin[] List of OutputPin objects
     * @throws PropelException
     */
    public function getOutputPins(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collOutputPinsPartial && !$this->isNew();
        if (null === $this->collOutputPins || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collOutputPins) {
                // return empty collection
                $this->initOutputPins();
            } else {
                $collOutputPins = OutputPinQuery::create(null, $criteria)
                    ->filterByDriverPin($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collOutputPinsPartial && count($collOutputPins)) {
                        $this->initOutputPins(false);

                        foreach ($collOutputPins as $obj) {
                            if (false == $this->collOutputPins->contains($obj)) {
                                $this->collOutputPins->append($obj);
                            }
                        }

                        $this->collOutputPinsPartial = true;
                    }

                    return $collOutputPins;
                }

                if ($partial && $this->collOutputPins) {
                    foreach ($this->collOutputPins as $obj) {
                        if ($obj->isNew()) {
                            $collOutputPins[] = $obj;
                        }
                    }
                }

                $this->collOutputPins = $collOutputPins;
                $this->collOutputPinsPartial = false;
            }
        }

        return $this->collOutputPins;
    }

    /**
     * Sets a collection of OutputPin objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $outputPins A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildDriverPin The current object (for fluent API support)
     */
    public function setOutputPins(Collection $outputPins, ConnectionInterface $con = null)
    {
        /** @var OutputPin[] $outputPinsToDelete */
        $outputPinsToDelete = $this->getOutputPins(new Criteria(), $con)->diff($outputPins);


        $this->outputPinsScheduledForDeletion = $outputPinsToDelete;

        foreach ($outputPinsToDelete as $outputPinRemoved) {
            $outputPinRemoved->setDriverPin(null);
        }

        $this->collOutputPins = null;
        foreach ($outputPins as $outputPin) {
            $this->addOutputPin($outputPin);
        }

        $this->collOutputPins = $outputPins;
        $this->collOutputPinsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related BaseOutputPin objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related BaseOutputPin objects.
     * @throws PropelException
     */
    public function countOutputPins(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collOutputPinsPartial && !$this->isNew();
        if (null === $this->collOutputPins || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collOutputPins) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getOutputPins());
            }

            $query = OutputPinQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDriverPin($this)
                ->count($con);
        }

        return count($this->collOutputPins);
    }

    /**
     * Method called to associate a OutputPin object to this object
     * through the OutputPin foreign key attribute.
     *
     * @param  OutputPin $l OutputPin
     * @return $this|\ArduinoCoilDriver\Drivers\DriverPin The current object (for fluent API support)
     */
    public function addOutputPin(OutputPin $l)
    {
        if ($this->collOutputPins === null) {
            $this->initOutputPins();
            $this->collOutputPinsPartial = true;
        }

        if (!$this->collOutputPins->contains($l)) {
            $this->doAddOutputPin($l);
        }

        return $this;
    }

    /**
     * @param OutputPin $outputPin The OutputPin object to add.
     */
    protected function doAddOutputPin(OutputPin $outputPin)
    {
        $this->collOutputPins[]= $outputPin;
        $outputPin->setDriverPin($this);
    }

    /**
     * @param  OutputPin $outputPin The OutputPin object to remove.
     * @return $this|ChildDriverPin The current object (for fluent API support)
     */
    public function removeOutputPin(OutputPin $outputPin)
    {
        if ($this->getOutputPins()->contains($outputPin)) {
            $pos = $this->collOutputPins->search($outputPin);
            $this->collOutputPins->remove($pos);
            if (null === $this->outputPinsScheduledForDeletion) {
                $this->outputPinsScheduledForDeletion = clone $this->collOutputPins;
                $this->outputPinsScheduledForDeletion->clear();
            }
            $this->outputPinsScheduledForDeletion[]= clone $outputPin;
            $outputPin->setDriverPin(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this DriverPin is new, it will return
     * an empty collection; or if this DriverPin has previously
     * been saved, it will retrieve related OutputPins from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in DriverPin.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|OutputPin[] List of OutputPin objects
     */
    public function getOutputPinsJoinOutput(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = OutputPinQuery::create(null, $criteria);
        $query->joinWith('Output', $joinBehavior);

        return $this->getOutputPins($query, $con);
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        if (null !== $this->aDriver) {
            $this->aDriver->removeDriverPin($this);
        }
        $this->id = null;
        $this->driver_id = null;
        $this->pin = null;
        $this->type = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
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
            if ($this->collOutputPins) {
                foreach ($this->collOutputPins as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collDriverPinValues = null;
        $this->collOutputPins = null;
        $this->aDriver = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(DriverPinTableMap::DEFAULT_STRING_FORMAT);
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
