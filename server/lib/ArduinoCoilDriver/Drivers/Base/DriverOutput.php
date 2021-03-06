<?php

namespace ArduinoCoilDriver\Drivers\Base;

use \Exception;
use \PDO;
use ArduinoCoilDriver\Drivers\Driver as ChildDriver;
use ArduinoCoilDriver\Drivers\DriverOutput as ChildDriverOutput;
use ArduinoCoilDriver\Drivers\DriverOutputPin as ChildDriverOutputPin;
use ArduinoCoilDriver\Drivers\DriverOutputPinQuery as ChildDriverOutputPinQuery;
use ArduinoCoilDriver\Drivers\DriverOutputQuery as ChildDriverOutputQuery;
use ArduinoCoilDriver\Drivers\DriverQuery as ChildDriverQuery;
use ArduinoCoilDriver\Drivers\Map\DriverOutputTableMap;
use ArduinoCoilDriver\Outputs\OutputViewOutput;
use ArduinoCoilDriver\Outputs\OutputViewOutputQuery;
use ArduinoCoilDriver\Outputs\Base\OutputViewOutput as BaseOutputViewOutput;
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
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\DefaultTranslator;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Context\ExecutionContextFactory;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\Validator\LegacyValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Base class that represents a row from the 'driver_outputs' table.
 *
 *
 *
* @package    propel.generator.ArduinoCoilDriver.Drivers.Base
*/
abstract class DriverOutput implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\ArduinoCoilDriver\\Drivers\\Map\\DriverOutputTableMap';


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
     * The value for the name field.
     *
     * @var        string
     */
    protected $name;

    /**
     * The value for the mapping field.
     *
     * @var        int
     */
    protected $mapping;

    /**
     * The value for the overlap_value field.
     *
     * @var        int
     */
    protected $overlap_value;

    /**
     * The value for the central_value field.
     *
     * @var        int
     */
    protected $central_value;

    /**
     * The value for the default_delay field.
     *
     * @var        int
     */
    protected $default_delay;

    /**
     * @var        ChildDriver
     */
    protected $aDriver;

    /**
     * @var        ObjectCollection|ChildDriverOutputPin[] Collection to store aggregation of ChildDriverOutputPin objects.
     */
    protected $collDriverOutputPins;
    protected $collDriverOutputPinsPartial;

    /**
     * @var        ObjectCollection|OutputViewOutput[] Collection to store aggregation of OutputViewOutput objects.
     */
    protected $collOutputViewOutputs;
    protected $collOutputViewOutputsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    // validate behavior

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * ConstraintViolationList object
     *
     * @see     http://api.symfony.com/2.0/Symfony/Component/Validator/ConstraintViolationList.html
     * @var     ConstraintViolationList
     */
    protected $validationFailures;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildDriverOutputPin[]
     */
    protected $driverOutputPinsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|OutputViewOutput[]
     */
    protected $outputViewOutputsScheduledForDeletion = null;

    /**
     * Initializes internal state of ArduinoCoilDriver\Drivers\Base\DriverOutput object.
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
     * Compares this with another <code>DriverOutput</code> instance.  If
     * <code>obj</code> is an instance of <code>DriverOutput</code>, delegates to
     * <code>equals(DriverOutput)</code>.  Otherwise, returns <code>false</code>.
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
     * @return $this|DriverOutput The current object, for fluid interface
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
     * Get the [name] column value.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the [mapping] column value.
     *
     * @return int
     */
    public function getMapping()
    {
        return $this->mapping;
    }

    /**
     * Get the [overlap_value] column value.
     *
     * @return int
     */
    public function getOverlapValue()
    {
        return $this->overlap_value;
    }

    /**
     * Get the [central_value] column value.
     *
     * @return int
     */
    public function getCentralValue()
    {
        return $this->central_value;
    }

    /**
     * Get the [default_delay] column value.
     *
     * @return int
     */
    public function getDefaultDelay()
    {
        return $this->default_delay;
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return $this|\ArduinoCoilDriver\Drivers\DriverOutput The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[DriverOutputTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [driver_id] column.
     *
     * @param int $v new value
     * @return $this|\ArduinoCoilDriver\Drivers\DriverOutput The current object (for fluent API support)
     */
    public function setDriverId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->driver_id !== $v) {
            $this->driver_id = $v;
            $this->modifiedColumns[DriverOutputTableMap::COL_DRIVER_ID] = true;
        }

        if ($this->aDriver !== null && $this->aDriver->getId() !== $v) {
            $this->aDriver = null;
        }

        return $this;
    } // setDriverId()

    /**
     * Set the value of [name] column.
     *
     * @param string $v new value
     * @return $this|\ArduinoCoilDriver\Drivers\DriverOutput The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[DriverOutputTableMap::COL_NAME] = true;
        }

        return $this;
    } // setName()

    /**
     * Set the value of [mapping] column.
     *
     * @param int $v new value
     * @return $this|\ArduinoCoilDriver\Drivers\DriverOutput The current object (for fluent API support)
     */
    public function setMapping($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->mapping !== $v) {
            $this->mapping = $v;
            $this->modifiedColumns[DriverOutputTableMap::COL_MAPPING] = true;
        }

        return $this;
    } // setMapping()

    /**
     * Set the value of [overlap_value] column.
     *
     * @param int $v new value
     * @return $this|\ArduinoCoilDriver\Drivers\DriverOutput The current object (for fluent API support)
     */
    public function setOverlapValue($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->overlap_value !== $v) {
            $this->overlap_value = $v;
            $this->modifiedColumns[DriverOutputTableMap::COL_OVERLAP_VALUE] = true;
        }

        return $this;
    } // setOverlapValue()

    /**
     * Set the value of [central_value] column.
     *
     * @param int $v new value
     * @return $this|\ArduinoCoilDriver\Drivers\DriverOutput The current object (for fluent API support)
     */
    public function setCentralValue($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->central_value !== $v) {
            $this->central_value = $v;
            $this->modifiedColumns[DriverOutputTableMap::COL_CENTRAL_VALUE] = true;
        }

        return $this;
    } // setCentralValue()

    /**
     * Set the value of [default_delay] column.
     *
     * @param int $v new value
     * @return $this|\ArduinoCoilDriver\Drivers\DriverOutput The current object (for fluent API support)
     */
    public function setDefaultDelay($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->default_delay !== $v) {
            $this->default_delay = $v;
            $this->modifiedColumns[DriverOutputTableMap::COL_DEFAULT_DELAY] = true;
        }

        return $this;
    } // setDefaultDelay()

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

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : DriverOutputTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : DriverOutputTableMap::translateFieldName('DriverId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->driver_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : DriverOutputTableMap::translateFieldName('Name', TableMap::TYPE_PHPNAME, $indexType)];
            $this->name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : DriverOutputTableMap::translateFieldName('Mapping', TableMap::TYPE_PHPNAME, $indexType)];
            $this->mapping = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : DriverOutputTableMap::translateFieldName('OverlapValue', TableMap::TYPE_PHPNAME, $indexType)];
            $this->overlap_value = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : DriverOutputTableMap::translateFieldName('CentralValue', TableMap::TYPE_PHPNAME, $indexType)];
            $this->central_value = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : DriverOutputTableMap::translateFieldName('DefaultDelay', TableMap::TYPE_PHPNAME, $indexType)];
            $this->default_delay = (null !== $col) ? (int) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 7; // 7 = DriverOutputTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\ArduinoCoilDriver\\Drivers\\DriverOutput'), 0, $e);
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
            $con = Propel::getServiceContainer()->getReadConnection(DriverOutputTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildDriverOutputQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aDriver = null;
            $this->collDriverOutputPins = null;

            $this->collOutputViewOutputs = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see DriverOutput::setDeleted()
     * @see DriverOutput::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(DriverOutputTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildDriverOutputQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(DriverOutputTableMap::DATABASE_NAME);
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
                DriverOutputTableMap::addInstanceToPool($this);
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

            if ($this->driverOutputPinsScheduledForDeletion !== null) {
                if (!$this->driverOutputPinsScheduledForDeletion->isEmpty()) {
                    \ArduinoCoilDriver\Drivers\DriverOutputPinQuery::create()
                        ->filterByPrimaryKeys($this->driverOutputPinsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->driverOutputPinsScheduledForDeletion = null;
                }
            }

            if ($this->collDriverOutputPins !== null) {
                foreach ($this->collDriverOutputPins as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->outputViewOutputsScheduledForDeletion !== null) {
                if (!$this->outputViewOutputsScheduledForDeletion->isEmpty()) {
                    \ArduinoCoilDriver\Outputs\OutputViewOutputQuery::create()
                        ->filterByPrimaryKeys($this->outputViewOutputsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->outputViewOutputsScheduledForDeletion = null;
                }
            }

            if ($this->collOutputViewOutputs !== null) {
                foreach ($this->collOutputViewOutputs as $referrerFK) {
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

        $this->modifiedColumns[DriverOutputTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . DriverOutputTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(DriverOutputTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(DriverOutputTableMap::COL_DRIVER_ID)) {
            $modifiedColumns[':p' . $index++]  = 'driver_id';
        }
        if ($this->isColumnModified(DriverOutputTableMap::COL_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'name';
        }
        if ($this->isColumnModified(DriverOutputTableMap::COL_MAPPING)) {
            $modifiedColumns[':p' . $index++]  = 'mapping';
        }
        if ($this->isColumnModified(DriverOutputTableMap::COL_OVERLAP_VALUE)) {
            $modifiedColumns[':p' . $index++]  = 'overlap_value';
        }
        if ($this->isColumnModified(DriverOutputTableMap::COL_CENTRAL_VALUE)) {
            $modifiedColumns[':p' . $index++]  = 'central_value';
        }
        if ($this->isColumnModified(DriverOutputTableMap::COL_DEFAULT_DELAY)) {
            $modifiedColumns[':p' . $index++]  = 'default_delay';
        }

        $sql = sprintf(
            'INSERT INTO driver_outputs (%s) VALUES (%s)',
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
                    case 'name':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case 'mapping':
                        $stmt->bindValue($identifier, $this->mapping, PDO::PARAM_INT);
                        break;
                    case 'overlap_value':
                        $stmt->bindValue($identifier, $this->overlap_value, PDO::PARAM_INT);
                        break;
                    case 'central_value':
                        $stmt->bindValue($identifier, $this->central_value, PDO::PARAM_INT);
                        break;
                    case 'default_delay':
                        $stmt->bindValue($identifier, $this->default_delay, PDO::PARAM_INT);
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
        $pos = DriverOutputTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getName();
                break;
            case 3:
                return $this->getMapping();
                break;
            case 4:
                return $this->getOverlapValue();
                break;
            case 5:
                return $this->getCentralValue();
                break;
            case 6:
                return $this->getDefaultDelay();
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

        if (isset($alreadyDumpedObjects['DriverOutput'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['DriverOutput'][$this->hashCode()] = true;
        $keys = DriverOutputTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getDriverId(),
            $keys[2] => $this->getName(),
            $keys[3] => $this->getMapping(),
            $keys[4] => $this->getOverlapValue(),
            $keys[5] => $this->getCentralValue(),
            $keys[6] => $this->getDefaultDelay(),
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
            if (null !== $this->collDriverOutputPins) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'driverOutputPins';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'driver_output_pinss';
                        break;
                    default:
                        $key = 'DriverOutputPins';
                }

                $result[$key] = $this->collDriverOutputPins->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collOutputViewOutputs) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'outputViewOutputs';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'output_view_outputs';
                        break;
                    default:
                        $key = 'OutputViewOutputs';
                }

                $result[$key] = $this->collOutputViewOutputs->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
     * @return $this|\ArduinoCoilDriver\Drivers\DriverOutput
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = DriverOutputTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\ArduinoCoilDriver\Drivers\DriverOutput
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
                $this->setName($value);
                break;
            case 3:
                $this->setMapping($value);
                break;
            case 4:
                $this->setOverlapValue($value);
                break;
            case 5:
                $this->setCentralValue($value);
                break;
            case 6:
                $this->setDefaultDelay($value);
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
        $keys = DriverOutputTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setDriverId($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setName($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setMapping($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setOverlapValue($arr[$keys[4]]);
        }
        if (array_key_exists($keys[5], $arr)) {
            $this->setCentralValue($arr[$keys[5]]);
        }
        if (array_key_exists($keys[6], $arr)) {
            $this->setDefaultDelay($arr[$keys[6]]);
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
     * @return $this|\ArduinoCoilDriver\Drivers\DriverOutput The current object, for fluid interface
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
        $criteria = new Criteria(DriverOutputTableMap::DATABASE_NAME);

        if ($this->isColumnModified(DriverOutputTableMap::COL_ID)) {
            $criteria->add(DriverOutputTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(DriverOutputTableMap::COL_DRIVER_ID)) {
            $criteria->add(DriverOutputTableMap::COL_DRIVER_ID, $this->driver_id);
        }
        if ($this->isColumnModified(DriverOutputTableMap::COL_NAME)) {
            $criteria->add(DriverOutputTableMap::COL_NAME, $this->name);
        }
        if ($this->isColumnModified(DriverOutputTableMap::COL_MAPPING)) {
            $criteria->add(DriverOutputTableMap::COL_MAPPING, $this->mapping);
        }
        if ($this->isColumnModified(DriverOutputTableMap::COL_OVERLAP_VALUE)) {
            $criteria->add(DriverOutputTableMap::COL_OVERLAP_VALUE, $this->overlap_value);
        }
        if ($this->isColumnModified(DriverOutputTableMap::COL_CENTRAL_VALUE)) {
            $criteria->add(DriverOutputTableMap::COL_CENTRAL_VALUE, $this->central_value);
        }
        if ($this->isColumnModified(DriverOutputTableMap::COL_DEFAULT_DELAY)) {
            $criteria->add(DriverOutputTableMap::COL_DEFAULT_DELAY, $this->default_delay);
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
        $criteria = ChildDriverOutputQuery::create();
        $criteria->add(DriverOutputTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \ArduinoCoilDriver\Drivers\DriverOutput (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDriverId($this->getDriverId());
        $copyObj->setName($this->getName());
        $copyObj->setMapping($this->getMapping());
        $copyObj->setOverlapValue($this->getOverlapValue());
        $copyObj->setCentralValue($this->getCentralValue());
        $copyObj->setDefaultDelay($this->getDefaultDelay());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getDriverOutputPins() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDriverOutputPin($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getOutputViewOutputs() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addOutputViewOutput($relObj->copy($deepCopy));
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
     * @return \ArduinoCoilDriver\Drivers\DriverOutput Clone of current object.
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
     * @return $this|\ArduinoCoilDriver\Drivers\DriverOutput The current object (for fluent API support)
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
            $v->addDriverOutput($this);
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
                $this->aDriver->addDriverOutputs($this);
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
        if ('DriverOutputPin' == $relationName) {
            return $this->initDriverOutputPins();
        }
        if ('OutputViewOutput' == $relationName) {
            return $this->initOutputViewOutputs();
        }
    }

    /**
     * Clears out the collDriverOutputPins collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDriverOutputPins()
     */
    public function clearDriverOutputPins()
    {
        $this->collDriverOutputPins = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDriverOutputPins collection loaded partially.
     */
    public function resetPartialDriverOutputPins($v = true)
    {
        $this->collDriverOutputPinsPartial = $v;
    }

    /**
     * Initializes the collDriverOutputPins collection.
     *
     * By default this just sets the collDriverOutputPins collection to an empty array (like clearcollDriverOutputPins());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDriverOutputPins($overrideExisting = true)
    {
        if (null !== $this->collDriverOutputPins && !$overrideExisting) {
            return;
        }
        $this->collDriverOutputPins = new ObjectCollection();
        $this->collDriverOutputPins->setModel('\ArduinoCoilDriver\Drivers\DriverOutputPin');
    }

    /**
     * Gets an array of ChildDriverOutputPin objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDriverOutput is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildDriverOutputPin[] List of ChildDriverOutputPin objects
     * @throws PropelException
     */
    public function getDriverOutputPins(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDriverOutputPinsPartial && !$this->isNew();
        if (null === $this->collDriverOutputPins || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDriverOutputPins) {
                // return empty collection
                $this->initDriverOutputPins();
            } else {
                $collDriverOutputPins = ChildDriverOutputPinQuery::create(null, $criteria)
                    ->filterByDriverOutput($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDriverOutputPinsPartial && count($collDriverOutputPins)) {
                        $this->initDriverOutputPins(false);

                        foreach ($collDriverOutputPins as $obj) {
                            if (false == $this->collDriverOutputPins->contains($obj)) {
                                $this->collDriverOutputPins->append($obj);
                            }
                        }

                        $this->collDriverOutputPinsPartial = true;
                    }

                    return $collDriverOutputPins;
                }

                if ($partial && $this->collDriverOutputPins) {
                    foreach ($this->collDriverOutputPins as $obj) {
                        if ($obj->isNew()) {
                            $collDriverOutputPins[] = $obj;
                        }
                    }
                }

                $this->collDriverOutputPins = $collDriverOutputPins;
                $this->collDriverOutputPinsPartial = false;
            }
        }

        return $this->collDriverOutputPins;
    }

    /**
     * Sets a collection of ChildDriverOutputPin objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $driverOutputPins A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildDriverOutput The current object (for fluent API support)
     */
    public function setDriverOutputPins(Collection $driverOutputPins, ConnectionInterface $con = null)
    {
        /** @var ChildDriverOutputPin[] $driverOutputPinsToDelete */
        $driverOutputPinsToDelete = $this->getDriverOutputPins(new Criteria(), $con)->diff($driverOutputPins);


        $this->driverOutputPinsScheduledForDeletion = $driverOutputPinsToDelete;

        foreach ($driverOutputPinsToDelete as $driverOutputPinRemoved) {
            $driverOutputPinRemoved->setDriverOutput(null);
        }

        $this->collDriverOutputPins = null;
        foreach ($driverOutputPins as $driverOutputPin) {
            $this->addDriverOutputPin($driverOutputPin);
        }

        $this->collDriverOutputPins = $driverOutputPins;
        $this->collDriverOutputPinsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DriverOutputPin objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DriverOutputPin objects.
     * @throws PropelException
     */
    public function countDriverOutputPins(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDriverOutputPinsPartial && !$this->isNew();
        if (null === $this->collDriverOutputPins || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDriverOutputPins) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDriverOutputPins());
            }

            $query = ChildDriverOutputPinQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDriverOutput($this)
                ->count($con);
        }

        return count($this->collDriverOutputPins);
    }

    /**
     * Method called to associate a ChildDriverOutputPin object to this object
     * through the ChildDriverOutputPin foreign key attribute.
     *
     * @param  ChildDriverOutputPin $l ChildDriverOutputPin
     * @return $this|\ArduinoCoilDriver\Drivers\DriverOutput The current object (for fluent API support)
     */
    public function addDriverOutputPin(ChildDriverOutputPin $l)
    {
        if ($this->collDriverOutputPins === null) {
            $this->initDriverOutputPins();
            $this->collDriverOutputPinsPartial = true;
        }

        if (!$this->collDriverOutputPins->contains($l)) {
            $this->doAddDriverOutputPin($l);
        }

        return $this;
    }

    /**
     * @param ChildDriverOutputPin $driverOutputPin The ChildDriverOutputPin object to add.
     */
    protected function doAddDriverOutputPin(ChildDriverOutputPin $driverOutputPin)
    {
        $this->collDriverOutputPins[]= $driverOutputPin;
        $driverOutputPin->setDriverOutput($this);
    }

    /**
     * @param  ChildDriverOutputPin $driverOutputPin The ChildDriverOutputPin object to remove.
     * @return $this|ChildDriverOutput The current object (for fluent API support)
     */
    public function removeDriverOutputPin(ChildDriverOutputPin $driverOutputPin)
    {
        if ($this->getDriverOutputPins()->contains($driverOutputPin)) {
            $pos = $this->collDriverOutputPins->search($driverOutputPin);
            $this->collDriverOutputPins->remove($pos);
            if (null === $this->driverOutputPinsScheduledForDeletion) {
                $this->driverOutputPinsScheduledForDeletion = clone $this->collDriverOutputPins;
                $this->driverOutputPinsScheduledForDeletion->clear();
            }
            $this->driverOutputPinsScheduledForDeletion[]= clone $driverOutputPin;
            $driverOutputPin->setDriverOutput(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this DriverOutput is new, it will return
     * an empty collection; or if this DriverOutput has previously
     * been saved, it will retrieve related DriverOutputPins from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in DriverOutput.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildDriverOutputPin[] List of ChildDriverOutputPin objects
     */
    public function getDriverOutputPinsJoinDriverPin(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildDriverOutputPinQuery::create(null, $criteria);
        $query->joinWith('DriverPin', $joinBehavior);

        return $this->getDriverOutputPins($query, $con);
    }

    /**
     * Clears out the collOutputViewOutputs collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addOutputViewOutputs()
     */
    public function clearOutputViewOutputs()
    {
        $this->collOutputViewOutputs = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collOutputViewOutputs collection loaded partially.
     */
    public function resetPartialOutputViewOutputs($v = true)
    {
        $this->collOutputViewOutputsPartial = $v;
    }

    /**
     * Initializes the collOutputViewOutputs collection.
     *
     * By default this just sets the collOutputViewOutputs collection to an empty array (like clearcollOutputViewOutputs());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initOutputViewOutputs($overrideExisting = true)
    {
        if (null !== $this->collOutputViewOutputs && !$overrideExisting) {
            return;
        }
        $this->collOutputViewOutputs = new ObjectCollection();
        $this->collOutputViewOutputs->setModel('\ArduinoCoilDriver\Outputs\OutputViewOutput');
    }

    /**
     * Gets an array of OutputViewOutput objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDriverOutput is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|OutputViewOutput[] List of OutputViewOutput objects
     * @throws PropelException
     */
    public function getOutputViewOutputs(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collOutputViewOutputsPartial && !$this->isNew();
        if (null === $this->collOutputViewOutputs || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collOutputViewOutputs) {
                // return empty collection
                $this->initOutputViewOutputs();
            } else {
                $collOutputViewOutputs = OutputViewOutputQuery::create(null, $criteria)
                    ->filterByDriverOutput($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collOutputViewOutputsPartial && count($collOutputViewOutputs)) {
                        $this->initOutputViewOutputs(false);

                        foreach ($collOutputViewOutputs as $obj) {
                            if (false == $this->collOutputViewOutputs->contains($obj)) {
                                $this->collOutputViewOutputs->append($obj);
                            }
                        }

                        $this->collOutputViewOutputsPartial = true;
                    }

                    return $collOutputViewOutputs;
                }

                if ($partial && $this->collOutputViewOutputs) {
                    foreach ($this->collOutputViewOutputs as $obj) {
                        if ($obj->isNew()) {
                            $collOutputViewOutputs[] = $obj;
                        }
                    }
                }

                $this->collOutputViewOutputs = $collOutputViewOutputs;
                $this->collOutputViewOutputsPartial = false;
            }
        }

        return $this->collOutputViewOutputs;
    }

    /**
     * Sets a collection of OutputViewOutput objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $outputViewOutputs A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildDriverOutput The current object (for fluent API support)
     */
    public function setOutputViewOutputs(Collection $outputViewOutputs, ConnectionInterface $con = null)
    {
        /** @var OutputViewOutput[] $outputViewOutputsToDelete */
        $outputViewOutputsToDelete = $this->getOutputViewOutputs(new Criteria(), $con)->diff($outputViewOutputs);


        $this->outputViewOutputsScheduledForDeletion = $outputViewOutputsToDelete;

        foreach ($outputViewOutputsToDelete as $outputViewOutputRemoved) {
            $outputViewOutputRemoved->setDriverOutput(null);
        }

        $this->collOutputViewOutputs = null;
        foreach ($outputViewOutputs as $outputViewOutput) {
            $this->addOutputViewOutput($outputViewOutput);
        }

        $this->collOutputViewOutputs = $outputViewOutputs;
        $this->collOutputViewOutputsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related BaseOutputViewOutput objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related BaseOutputViewOutput objects.
     * @throws PropelException
     */
    public function countOutputViewOutputs(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collOutputViewOutputsPartial && !$this->isNew();
        if (null === $this->collOutputViewOutputs || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collOutputViewOutputs) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getOutputViewOutputs());
            }

            $query = OutputViewOutputQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDriverOutput($this)
                ->count($con);
        }

        return count($this->collOutputViewOutputs);
    }

    /**
     * Method called to associate a OutputViewOutput object to this object
     * through the OutputViewOutput foreign key attribute.
     *
     * @param  OutputViewOutput $l OutputViewOutput
     * @return $this|\ArduinoCoilDriver\Drivers\DriverOutput The current object (for fluent API support)
     */
    public function addOutputViewOutput(OutputViewOutput $l)
    {
        if ($this->collOutputViewOutputs === null) {
            $this->initOutputViewOutputs();
            $this->collOutputViewOutputsPartial = true;
        }

        if (!$this->collOutputViewOutputs->contains($l)) {
            $this->doAddOutputViewOutput($l);
        }

        return $this;
    }

    /**
     * @param OutputViewOutput $outputViewOutput The OutputViewOutput object to add.
     */
    protected function doAddOutputViewOutput(OutputViewOutput $outputViewOutput)
    {
        $this->collOutputViewOutputs[]= $outputViewOutput;
        $outputViewOutput->setDriverOutput($this);
    }

    /**
     * @param  OutputViewOutput $outputViewOutput The OutputViewOutput object to remove.
     * @return $this|ChildDriverOutput The current object (for fluent API support)
     */
    public function removeOutputViewOutput(OutputViewOutput $outputViewOutput)
    {
        if ($this->getOutputViewOutputs()->contains($outputViewOutput)) {
            $pos = $this->collOutputViewOutputs->search($outputViewOutput);
            $this->collOutputViewOutputs->remove($pos);
            if (null === $this->outputViewOutputsScheduledForDeletion) {
                $this->outputViewOutputsScheduledForDeletion = clone $this->collOutputViewOutputs;
                $this->outputViewOutputsScheduledForDeletion->clear();
            }
            $this->outputViewOutputsScheduledForDeletion[]= clone $outputViewOutput;
            $outputViewOutput->setDriverOutput(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this DriverOutput is new, it will return
     * an empty collection; or if this DriverOutput has previously
     * been saved, it will retrieve related OutputViewOutputs from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in DriverOutput.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|OutputViewOutput[] List of OutputViewOutput objects
     */
    public function getOutputViewOutputsJoinOutputView(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = OutputViewOutputQuery::create(null, $criteria);
        $query->joinWith('OutputView', $joinBehavior);

        return $this->getOutputViewOutputs($query, $con);
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        if (null !== $this->aDriver) {
            $this->aDriver->removeDriverOutput($this);
        }
        $this->id = null;
        $this->driver_id = null;
        $this->name = null;
        $this->mapping = null;
        $this->overlap_value = null;
        $this->central_value = null;
        $this->default_delay = null;
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
            if ($this->collDriverOutputPins) {
                foreach ($this->collDriverOutputPins as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collOutputViewOutputs) {
                foreach ($this->collOutputViewOutputs as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collDriverOutputPins = null;
        $this->collOutputViewOutputs = null;
        $this->aDriver = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(DriverOutputTableMap::DEFAULT_STRING_FORMAT);
    }

    // validate behavior

    /**
     * Configure validators constraints. The Validator object uses this method
     * to perform object validation.
     *
     * @param ClassMetadata $metadata
     */
    static public function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('name', new NotBlank());
        $metadata->addPropertyConstraint('name', new Length(array ('min' => 3,'max' => 32,)));
        $metadata->addPropertyConstraint('mapping', new Range(array ('min' => 1,'max' => 255,)));
        $metadata->addPropertyConstraint('overlap_value', new Range(array ('min' => 0,'max' => 255,)));
        $metadata->addPropertyConstraint('central_value', new Range(array ('min' => 0,'max' => 65536,)));
        $metadata->addPropertyConstraint('default_delay', new Range(array ('min' => 0,'max' => 250,)));
    }

    /**
     * Validates the object and all objects related to this table.
     *
     * @see        getValidationFailures()
     * @param      object $validator A Validator class instance
     * @return     boolean Whether all objects pass validation.
     */
    public function validate(ValidatorInterface $validator = null)
    {
        if (null === $validator) {
            if(class_exists('Symfony\\Component\\Validator\\Validator\\LegacyValidator')){
                $validator = new LegacyValidator(
                            new ExecutionContextFactory(new DefaultTranslator()),
                            new ClassMetaDataFactory(new StaticMethodLoader()),
                            new ConstraintValidatorFactory()
                );
            }else{
                $validator = new Validator(
                            new ClassMetadataFactory(new StaticMethodLoader()),
                            new ConstraintValidatorFactory(),
                            new DefaultTranslator()
                );
            }
        }

        $failureMap = new ConstraintViolationList();

        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            // We call the validate method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            // If validate() method exists, the validate-behavior is configured for related object
            if (method_exists($this->aDriver, 'validate')) {
                if (!$this->aDriver->validate($validator)) {
                    $failureMap->addAll($this->aDriver->getValidationFailures());
                }
            }

            $retval = $validator->validate($this);
            if (count($retval) > 0) {
                $failureMap->addAll($retval);
            }

            if (null !== $this->collDriverOutputPins) {
                foreach ($this->collDriverOutputPins as $referrerFK) {
                    if (method_exists($referrerFK, 'validate')) {
                        if (!$referrerFK->validate($validator)) {
                            $failureMap->addAll($referrerFK->getValidationFailures());
                        }
                    }
                }
            }
            if (null !== $this->collOutputViewOutputs) {
                foreach ($this->collOutputViewOutputs as $referrerFK) {
                    if (method_exists($referrerFK, 'validate')) {
                        if (!$referrerFK->validate($validator)) {
                            $failureMap->addAll($referrerFK->getValidationFailures());
                        }
                    }
                }
            }

            $this->alreadyInValidation = false;
        }

        $this->validationFailures = $failureMap;

        return (Boolean) (!(count($this->validationFailures) > 0));

    }

    /**
     * Gets any ConstraintViolation objects that resulted from last call to validate().
     *
     *
     * @return     object ConstraintViolationList
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
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
