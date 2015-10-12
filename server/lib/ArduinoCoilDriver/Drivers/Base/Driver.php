<?php

namespace ArduinoCoilDriver\Drivers\Base;

use \DateTime;
use \Exception;
use \PDO;
use ArduinoCoilDriver\Drivers\Driver as ChildDriver;
use ArduinoCoilDriver\Drivers\DriverOutput as ChildDriverOutput;
use ArduinoCoilDriver\Drivers\DriverOutputQuery as ChildDriverOutputQuery;
use ArduinoCoilDriver\Drivers\DriverPin as ChildDriverPin;
use ArduinoCoilDriver\Drivers\DriverPinQuery as ChildDriverPinQuery;
use ArduinoCoilDriver\Drivers\DriverQuery as ChildDriverQuery;
use ArduinoCoilDriver\Drivers\Map\DriverTableMap;
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
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\DefaultTranslator;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextFactory;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\Validator\LegacyValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Base class that represents a row from the 'drivers' table.
 *
 *
 *
* @package    propel.generator.ArduinoCoilDriver.Drivers.Base
*/
abstract class Driver implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\ArduinoCoilDriver\\Drivers\\Map\\DriverTableMap';


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
     * The value for the name field.
     *
     * @var        string
     */
    protected $name;

    /**
     * The value for the mac field.
     *
     * @var        string
     */
    protected $mac;

    /**
     * The value for the ip field.
     *
     * @var        string
     */
    protected $ip;

    /**
     * The value for the added field.
     *
     * @var        \DateTime
     */
    protected $added;

    /**
     * The value for the last_check_in field.
     *
     * @var        \DateTime
     */
    protected $last_check_in;

    /**
     * The value for the coil_contact field.
     *
     * @var        boolean
     */
    protected $coil_contact;

    /**
     * @var        ObjectCollection|ChildDriverPin[] Collection to store aggregation of ChildDriverPin objects.
     */
    protected $collDriverPins;
    protected $collDriverPinsPartial;

    /**
     * @var        ObjectCollection|ChildDriverOutput[] Collection to store aggregation of ChildDriverOutput objects.
     */
    protected $collDriverOutputs;
    protected $collDriverOutputsPartial;

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
     * @var ObjectCollection|ChildDriverPin[]
     */
    protected $driverPinsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildDriverOutput[]
     */
    protected $driverOutputsScheduledForDeletion = null;

    /**
     * Initializes internal state of ArduinoCoilDriver\Drivers\Base\Driver object.
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
     * Compares this with another <code>Driver</code> instance.  If
     * <code>obj</code> is an instance of <code>Driver</code>, delegates to
     * <code>equals(Driver)</code>.  Otherwise, returns <code>false</code>.
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
     * @return $this|Driver The current object, for fluid interface
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
     * Get the [name] column value.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the [mac] column value.
     *
     * @return string
     */
    public function getMac()
    {
        return $this->mac;
    }

    /**
     * Get the [ip] column value.
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Get the [optionally formatted] temporal [added] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getAdded($format = NULL)
    {
        if ($format === null) {
            return $this->added;
        } else {
            return $this->added instanceof \DateTime ? $this->added->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [last_check_in] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getLastCheckIn($format = NULL)
    {
        if ($format === null) {
            return $this->last_check_in;
        } else {
            return $this->last_check_in instanceof \DateTime ? $this->last_check_in->format($format) : null;
        }
    }

    /**
     * Get the [coil_contact] column value.
     *
     * @return boolean
     */
    public function getCoilContact()
    {
        return $this->coil_contact;
    }

    /**
     * Get the [coil_contact] column value.
     *
     * @return boolean
     */
    public function isCoilContact()
    {
        return $this->getCoilContact();
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return $this|\ArduinoCoilDriver\Drivers\Driver The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[DriverTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [name] column.
     *
     * @param string $v new value
     * @return $this|\ArduinoCoilDriver\Drivers\Driver The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[DriverTableMap::COL_NAME] = true;
        }

        return $this;
    } // setName()

    /**
     * Set the value of [mac] column.
     *
     * @param string $v new value
     * @return $this|\ArduinoCoilDriver\Drivers\Driver The current object (for fluent API support)
     */
    public function setMac($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->mac !== $v) {
            $this->mac = $v;
            $this->modifiedColumns[DriverTableMap::COL_MAC] = true;
        }

        return $this;
    } // setMac()

    /**
     * Set the value of [ip] column.
     *
     * @param string $v new value
     * @return $this|\ArduinoCoilDriver\Drivers\Driver The current object (for fluent API support)
     */
    public function setIp($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->ip !== $v) {
            $this->ip = $v;
            $this->modifiedColumns[DriverTableMap::COL_IP] = true;
        }

        return $this;
    } // setIp()

    /**
     * Sets the value of [added] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\ArduinoCoilDriver\Drivers\Driver The current object (for fluent API support)
     */
    public function setAdded($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->added !== null || $dt !== null) {
            if ($this->added === null || $dt === null || $dt->format("Y-m-d H:i:s") !== $this->added->format("Y-m-d H:i:s")) {
                $this->added = $dt === null ? null : clone $dt;
                $this->modifiedColumns[DriverTableMap::COL_ADDED] = true;
            }
        } // if either are not null

        return $this;
    } // setAdded()

    /**
     * Sets the value of [last_check_in] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\ArduinoCoilDriver\Drivers\Driver The current object (for fluent API support)
     */
    public function setLastCheckIn($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->last_check_in !== null || $dt !== null) {
            if ($this->last_check_in === null || $dt === null || $dt->format("Y-m-d H:i:s") !== $this->last_check_in->format("Y-m-d H:i:s")) {
                $this->last_check_in = $dt === null ? null : clone $dt;
                $this->modifiedColumns[DriverTableMap::COL_LAST_CHECK_IN] = true;
            }
        } // if either are not null

        return $this;
    } // setLastCheckIn()

    /**
     * Sets the value of the [coil_contact] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param  boolean|integer|string $v The new value
     * @return $this|\ArduinoCoilDriver\Drivers\Driver The current object (for fluent API support)
     */
    public function setCoilContact($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->coil_contact !== $v) {
            $this->coil_contact = $v;
            $this->modifiedColumns[DriverTableMap::COL_COIL_CONTACT] = true;
        }

        return $this;
    } // setCoilContact()

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

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : DriverTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : DriverTableMap::translateFieldName('Name', TableMap::TYPE_PHPNAME, $indexType)];
            $this->name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : DriverTableMap::translateFieldName('Mac', TableMap::TYPE_PHPNAME, $indexType)];
            $this->mac = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : DriverTableMap::translateFieldName('Ip', TableMap::TYPE_PHPNAME, $indexType)];
            $this->ip = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : DriverTableMap::translateFieldName('Added', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->added = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : DriverTableMap::translateFieldName('LastCheckIn', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->last_check_in = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : DriverTableMap::translateFieldName('CoilContact', TableMap::TYPE_PHPNAME, $indexType)];
            $this->coil_contact = (null !== $col) ? (boolean) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 7; // 7 = DriverTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\ArduinoCoilDriver\\Drivers\\Driver'), 0, $e);
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
            $con = Propel::getServiceContainer()->getReadConnection(DriverTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildDriverQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collDriverPins = null;

            $this->collDriverOutputs = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Driver::setDeleted()
     * @see Driver::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(DriverTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildDriverQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(DriverTableMap::DATABASE_NAME);
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
                DriverTableMap::addInstanceToPool($this);
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

            if ($this->driverPinsScheduledForDeletion !== null) {
                if (!$this->driverPinsScheduledForDeletion->isEmpty()) {
                    \ArduinoCoilDriver\Drivers\DriverPinQuery::create()
                        ->filterByPrimaryKeys($this->driverPinsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->driverPinsScheduledForDeletion = null;
                }
            }

            if ($this->collDriverPins !== null) {
                foreach ($this->collDriverPins as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->driverOutputsScheduledForDeletion !== null) {
                if (!$this->driverOutputsScheduledForDeletion->isEmpty()) {
                    \ArduinoCoilDriver\Drivers\DriverOutputQuery::create()
                        ->filterByPrimaryKeys($this->driverOutputsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->driverOutputsScheduledForDeletion = null;
                }
            }

            if ($this->collDriverOutputs !== null) {
                foreach ($this->collDriverOutputs as $referrerFK) {
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

        $this->modifiedColumns[DriverTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . DriverTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(DriverTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(DriverTableMap::COL_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'name';
        }
        if ($this->isColumnModified(DriverTableMap::COL_MAC)) {
            $modifiedColumns[':p' . $index++]  = 'mac';
        }
        if ($this->isColumnModified(DriverTableMap::COL_IP)) {
            $modifiedColumns[':p' . $index++]  = 'ip';
        }
        if ($this->isColumnModified(DriverTableMap::COL_ADDED)) {
            $modifiedColumns[':p' . $index++]  = 'added';
        }
        if ($this->isColumnModified(DriverTableMap::COL_LAST_CHECK_IN)) {
            $modifiedColumns[':p' . $index++]  = 'last_check_in';
        }
        if ($this->isColumnModified(DriverTableMap::COL_COIL_CONTACT)) {
            $modifiedColumns[':p' . $index++]  = 'coil_contact';
        }

        $sql = sprintf(
            'INSERT INTO drivers (%s) VALUES (%s)',
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
                    case 'name':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case 'mac':
                        $stmt->bindValue($identifier, $this->mac, PDO::PARAM_STR);
                        break;
                    case 'ip':
                        $stmt->bindValue($identifier, $this->ip, PDO::PARAM_STR);
                        break;
                    case 'added':
                        $stmt->bindValue($identifier, $this->added ? $this->added->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'last_check_in':
                        $stmt->bindValue($identifier, $this->last_check_in ? $this->last_check_in->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'coil_contact':
                        $stmt->bindValue($identifier, (int) $this->coil_contact, PDO::PARAM_INT);
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
        $pos = DriverTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getName();
                break;
            case 2:
                return $this->getMac();
                break;
            case 3:
                return $this->getIp();
                break;
            case 4:
                return $this->getAdded();
                break;
            case 5:
                return $this->getLastCheckIn();
                break;
            case 6:
                return $this->getCoilContact();
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

        if (isset($alreadyDumpedObjects['Driver'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Driver'][$this->hashCode()] = true;
        $keys = DriverTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getName(),
            $keys[2] => $this->getMac(),
            $keys[3] => $this->getIp(),
            $keys[4] => $this->getAdded(),
            $keys[5] => $this->getLastCheckIn(),
            $keys[6] => $this->getCoilContact(),
        );

        $utc = new \DateTimeZone('utc');
        if ($result[$keys[4]] instanceof \DateTime) {
            // When changing timezone we don't want to change existing instances
            $dateTime = clone $result[$keys[4]];
            $result[$keys[4]] = $dateTime->setTimezone($utc)->format('Y-m-d\TH:i:s\Z');
        }

        if ($result[$keys[5]] instanceof \DateTime) {
            // When changing timezone we don't want to change existing instances
            $dateTime = clone $result[$keys[5]];
            $result[$keys[5]] = $dateTime->setTimezone($utc)->format('Y-m-d\TH:i:s\Z');
        }

        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collDriverPins) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'driverPins';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'driver_pinss';
                        break;
                    default:
                        $key = 'DriverPins';
                }

                $result[$key] = $this->collDriverPins->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collDriverOutputs) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'driverOutputs';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'driver_outputss';
                        break;
                    default:
                        $key = 'DriverOutputs';
                }

                $result[$key] = $this->collDriverOutputs->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
     * @return $this|\ArduinoCoilDriver\Drivers\Driver
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = DriverTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\ArduinoCoilDriver\Drivers\Driver
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setName($value);
                break;
            case 2:
                $this->setMac($value);
                break;
            case 3:
                $this->setIp($value);
                break;
            case 4:
                $this->setAdded($value);
                break;
            case 5:
                $this->setLastCheckIn($value);
                break;
            case 6:
                $this->setCoilContact($value);
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
        $keys = DriverTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setName($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setMac($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setIp($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setAdded($arr[$keys[4]]);
        }
        if (array_key_exists($keys[5], $arr)) {
            $this->setLastCheckIn($arr[$keys[5]]);
        }
        if (array_key_exists($keys[6], $arr)) {
            $this->setCoilContact($arr[$keys[6]]);
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
     * @return $this|\ArduinoCoilDriver\Drivers\Driver The current object, for fluid interface
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
        $criteria = new Criteria(DriverTableMap::DATABASE_NAME);

        if ($this->isColumnModified(DriverTableMap::COL_ID)) {
            $criteria->add(DriverTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(DriverTableMap::COL_NAME)) {
            $criteria->add(DriverTableMap::COL_NAME, $this->name);
        }
        if ($this->isColumnModified(DriverTableMap::COL_MAC)) {
            $criteria->add(DriverTableMap::COL_MAC, $this->mac);
        }
        if ($this->isColumnModified(DriverTableMap::COL_IP)) {
            $criteria->add(DriverTableMap::COL_IP, $this->ip);
        }
        if ($this->isColumnModified(DriverTableMap::COL_ADDED)) {
            $criteria->add(DriverTableMap::COL_ADDED, $this->added);
        }
        if ($this->isColumnModified(DriverTableMap::COL_LAST_CHECK_IN)) {
            $criteria->add(DriverTableMap::COL_LAST_CHECK_IN, $this->last_check_in);
        }
        if ($this->isColumnModified(DriverTableMap::COL_COIL_CONTACT)) {
            $criteria->add(DriverTableMap::COL_COIL_CONTACT, $this->coil_contact);
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
        $criteria = ChildDriverQuery::create();
        $criteria->add(DriverTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \ArduinoCoilDriver\Drivers\Driver (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setName($this->getName());
        $copyObj->setMac($this->getMac());
        $copyObj->setIp($this->getIp());
        $copyObj->setAdded($this->getAdded());
        $copyObj->setLastCheckIn($this->getLastCheckIn());
        $copyObj->setCoilContact($this->getCoilContact());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getDriverPins() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDriverPin($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getDriverOutputs() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDriverOutput($relObj->copy($deepCopy));
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
     * @return \ArduinoCoilDriver\Drivers\Driver Clone of current object.
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
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('DriverPin' == $relationName) {
            return $this->initDriverPins();
        }
        if ('DriverOutput' == $relationName) {
            return $this->initDriverOutputs();
        }
    }

    /**
     * Clears out the collDriverPins collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDriverPins()
     */
    public function clearDriverPins()
    {
        $this->collDriverPins = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDriverPins collection loaded partially.
     */
    public function resetPartialDriverPins($v = true)
    {
        $this->collDriverPinsPartial = $v;
    }

    /**
     * Initializes the collDriverPins collection.
     *
     * By default this just sets the collDriverPins collection to an empty array (like clearcollDriverPins());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDriverPins($overrideExisting = true)
    {
        if (null !== $this->collDriverPins && !$overrideExisting) {
            return;
        }
        $this->collDriverPins = new ObjectCollection();
        $this->collDriverPins->setModel('\ArduinoCoilDriver\Drivers\DriverPin');
    }

    /**
     * Gets an array of ChildDriverPin objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDriver is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildDriverPin[] List of ChildDriverPin objects
     * @throws PropelException
     */
    public function getDriverPins(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDriverPinsPartial && !$this->isNew();
        if (null === $this->collDriverPins || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDriverPins) {
                // return empty collection
                $this->initDriverPins();
            } else {
                $collDriverPins = ChildDriverPinQuery::create(null, $criteria)
                    ->filterByDriver($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDriverPinsPartial && count($collDriverPins)) {
                        $this->initDriverPins(false);

                        foreach ($collDriverPins as $obj) {
                            if (false == $this->collDriverPins->contains($obj)) {
                                $this->collDriverPins->append($obj);
                            }
                        }

                        $this->collDriverPinsPartial = true;
                    }

                    return $collDriverPins;
                }

                if ($partial && $this->collDriverPins) {
                    foreach ($this->collDriverPins as $obj) {
                        if ($obj->isNew()) {
                            $collDriverPins[] = $obj;
                        }
                    }
                }

                $this->collDriverPins = $collDriverPins;
                $this->collDriverPinsPartial = false;
            }
        }

        return $this->collDriverPins;
    }

    /**
     * Sets a collection of ChildDriverPin objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $driverPins A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildDriver The current object (for fluent API support)
     */
    public function setDriverPins(Collection $driverPins, ConnectionInterface $con = null)
    {
        /** @var ChildDriverPin[] $driverPinsToDelete */
        $driverPinsToDelete = $this->getDriverPins(new Criteria(), $con)->diff($driverPins);


        $this->driverPinsScheduledForDeletion = $driverPinsToDelete;

        foreach ($driverPinsToDelete as $driverPinRemoved) {
            $driverPinRemoved->setDriver(null);
        }

        $this->collDriverPins = null;
        foreach ($driverPins as $driverPin) {
            $this->addDriverPin($driverPin);
        }

        $this->collDriverPins = $driverPins;
        $this->collDriverPinsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DriverPin objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DriverPin objects.
     * @throws PropelException
     */
    public function countDriverPins(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDriverPinsPartial && !$this->isNew();
        if (null === $this->collDriverPins || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDriverPins) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDriverPins());
            }

            $query = ChildDriverPinQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDriver($this)
                ->count($con);
        }

        return count($this->collDriverPins);
    }

    /**
     * Method called to associate a ChildDriverPin object to this object
     * through the ChildDriverPin foreign key attribute.
     *
     * @param  ChildDriverPin $l ChildDriverPin
     * @return $this|\ArduinoCoilDriver\Drivers\Driver The current object (for fluent API support)
     */
    public function addDriverPin(ChildDriverPin $l)
    {
        if ($this->collDriverPins === null) {
            $this->initDriverPins();
            $this->collDriverPinsPartial = true;
        }

        if (!$this->collDriverPins->contains($l)) {
            $this->doAddDriverPin($l);
        }

        return $this;
    }

    /**
     * @param ChildDriverPin $driverPin The ChildDriverPin object to add.
     */
    protected function doAddDriverPin(ChildDriverPin $driverPin)
    {
        $this->collDriverPins[]= $driverPin;
        $driverPin->setDriver($this);
    }

    /**
     * @param  ChildDriverPin $driverPin The ChildDriverPin object to remove.
     * @return $this|ChildDriver The current object (for fluent API support)
     */
    public function removeDriverPin(ChildDriverPin $driverPin)
    {
        if ($this->getDriverPins()->contains($driverPin)) {
            $pos = $this->collDriverPins->search($driverPin);
            $this->collDriverPins->remove($pos);
            if (null === $this->driverPinsScheduledForDeletion) {
                $this->driverPinsScheduledForDeletion = clone $this->collDriverPins;
                $this->driverPinsScheduledForDeletion->clear();
            }
            $this->driverPinsScheduledForDeletion[]= clone $driverPin;
            $driverPin->setDriver(null);
        }

        return $this;
    }

    /**
     * Clears out the collDriverOutputs collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDriverOutputs()
     */
    public function clearDriverOutputs()
    {
        $this->collDriverOutputs = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDriverOutputs collection loaded partially.
     */
    public function resetPartialDriverOutputs($v = true)
    {
        $this->collDriverOutputsPartial = $v;
    }

    /**
     * Initializes the collDriverOutputs collection.
     *
     * By default this just sets the collDriverOutputs collection to an empty array (like clearcollDriverOutputs());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDriverOutputs($overrideExisting = true)
    {
        if (null !== $this->collDriverOutputs && !$overrideExisting) {
            return;
        }
        $this->collDriverOutputs = new ObjectCollection();
        $this->collDriverOutputs->setModel('\ArduinoCoilDriver\Drivers\DriverOutput');
    }

    /**
     * Gets an array of ChildDriverOutput objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDriver is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildDriverOutput[] List of ChildDriverOutput objects
     * @throws PropelException
     */
    public function getDriverOutputs(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDriverOutputsPartial && !$this->isNew();
        if (null === $this->collDriverOutputs || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDriverOutputs) {
                // return empty collection
                $this->initDriverOutputs();
            } else {
                $collDriverOutputs = ChildDriverOutputQuery::create(null, $criteria)
                    ->filterByDriver($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDriverOutputsPartial && count($collDriverOutputs)) {
                        $this->initDriverOutputs(false);

                        foreach ($collDriverOutputs as $obj) {
                            if (false == $this->collDriverOutputs->contains($obj)) {
                                $this->collDriverOutputs->append($obj);
                            }
                        }

                        $this->collDriverOutputsPartial = true;
                    }

                    return $collDriverOutputs;
                }

                if ($partial && $this->collDriverOutputs) {
                    foreach ($this->collDriverOutputs as $obj) {
                        if ($obj->isNew()) {
                            $collDriverOutputs[] = $obj;
                        }
                    }
                }

                $this->collDriverOutputs = $collDriverOutputs;
                $this->collDriverOutputsPartial = false;
            }
        }

        return $this->collDriverOutputs;
    }

    /**
     * Sets a collection of ChildDriverOutput objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $driverOutputs A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildDriver The current object (for fluent API support)
     */
    public function setDriverOutputs(Collection $driverOutputs, ConnectionInterface $con = null)
    {
        /** @var ChildDriverOutput[] $driverOutputsToDelete */
        $driverOutputsToDelete = $this->getDriverOutputs(new Criteria(), $con)->diff($driverOutputs);


        $this->driverOutputsScheduledForDeletion = $driverOutputsToDelete;

        foreach ($driverOutputsToDelete as $driverOutputRemoved) {
            $driverOutputRemoved->setDriver(null);
        }

        $this->collDriverOutputs = null;
        foreach ($driverOutputs as $driverOutput) {
            $this->addDriverOutput($driverOutput);
        }

        $this->collDriverOutputs = $driverOutputs;
        $this->collDriverOutputsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DriverOutput objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DriverOutput objects.
     * @throws PropelException
     */
    public function countDriverOutputs(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDriverOutputsPartial && !$this->isNew();
        if (null === $this->collDriverOutputs || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDriverOutputs) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDriverOutputs());
            }

            $query = ChildDriverOutputQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDriver($this)
                ->count($con);
        }

        return count($this->collDriverOutputs);
    }

    /**
     * Method called to associate a ChildDriverOutput object to this object
     * through the ChildDriverOutput foreign key attribute.
     *
     * @param  ChildDriverOutput $l ChildDriverOutput
     * @return $this|\ArduinoCoilDriver\Drivers\Driver The current object (for fluent API support)
     */
    public function addDriverOutput(ChildDriverOutput $l)
    {
        if ($this->collDriverOutputs === null) {
            $this->initDriverOutputs();
            $this->collDriverOutputsPartial = true;
        }

        if (!$this->collDriverOutputs->contains($l)) {
            $this->doAddDriverOutput($l);
        }

        return $this;
    }

    /**
     * @param ChildDriverOutput $driverOutput The ChildDriverOutput object to add.
     */
    protected function doAddDriverOutput(ChildDriverOutput $driverOutput)
    {
        $this->collDriverOutputs[]= $driverOutput;
        $driverOutput->setDriver($this);
    }

    /**
     * @param  ChildDriverOutput $driverOutput The ChildDriverOutput object to remove.
     * @return $this|ChildDriver The current object (for fluent API support)
     */
    public function removeDriverOutput(ChildDriverOutput $driverOutput)
    {
        if ($this->getDriverOutputs()->contains($driverOutput)) {
            $pos = $this->collDriverOutputs->search($driverOutput);
            $this->collDriverOutputs->remove($pos);
            if (null === $this->driverOutputsScheduledForDeletion) {
                $this->driverOutputsScheduledForDeletion = clone $this->collDriverOutputs;
                $this->driverOutputsScheduledForDeletion->clear();
            }
            $this->driverOutputsScheduledForDeletion[]= clone $driverOutput;
            $driverOutput->setDriver(null);
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
        $this->id = null;
        $this->name = null;
        $this->mac = null;
        $this->ip = null;
        $this->added = null;
        $this->last_check_in = null;
        $this->coil_contact = null;
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
            if ($this->collDriverPins) {
                foreach ($this->collDriverPins as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collDriverOutputs) {
                foreach ($this->collDriverOutputs as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collDriverPins = null;
        $this->collDriverOutputs = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(DriverTableMap::DEFAULT_STRING_FORMAT);
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
        $metadata->addPropertyConstraint('mac', new Length(array ('min' => 17,'max' => 17,)));
        $metadata->addPropertyConstraint('ip', new Length(array ('min' => 7,'max' => 15,)));
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


            $retval = $validator->validate($this);
            if (count($retval) > 0) {
                $failureMap->addAll($retval);
            }

            if (null !== $this->collDriverPins) {
                foreach ($this->collDriverPins as $referrerFK) {
                    if (method_exists($referrerFK, 'validate')) {
                        if (!$referrerFK->validate($validator)) {
                            $failureMap->addAll($referrerFK->getValidationFailures());
                        }
                    }
                }
            }
            if (null !== $this->collDriverOutputs) {
                foreach ($this->collDriverOutputs as $referrerFK) {
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
