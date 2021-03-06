<?php

namespace ArduinoCoilDriver\Drivers\Map;

use ArduinoCoilDriver\Drivers\DriverOutput;
use ArduinoCoilDriver\Drivers\DriverOutputQuery;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;


/**
 * This class defines the structure of the 'driver_outputs' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class DriverOutputTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'ArduinoCoilDriver.Drivers.Map.DriverOutputTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'driver_outputs';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\ArduinoCoilDriver\\Drivers\\DriverOutput';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'ArduinoCoilDriver.Drivers.DriverOutput';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 7;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 7;

    /**
     * the column name for the id field
     */
    const COL_ID = 'driver_outputs.id';

    /**
     * the column name for the driver_id field
     */
    const COL_DRIVER_ID = 'driver_outputs.driver_id';

    /**
     * the column name for the name field
     */
    const COL_NAME = 'driver_outputs.name';

    /**
     * the column name for the mapping field
     */
    const COL_MAPPING = 'driver_outputs.mapping';

    /**
     * the column name for the overlap_value field
     */
    const COL_OVERLAP_VALUE = 'driver_outputs.overlap_value';

    /**
     * the column name for the central_value field
     */
    const COL_CENTRAL_VALUE = 'driver_outputs.central_value';

    /**
     * the column name for the default_delay field
     */
    const COL_DEFAULT_DELAY = 'driver_outputs.default_delay';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('Id', 'DriverId', 'Name', 'Mapping', 'OverlapValue', 'CentralValue', 'DefaultDelay', ),
        self::TYPE_CAMELNAME     => array('id', 'driverId', 'name', 'mapping', 'overlapValue', 'centralValue', 'defaultDelay', ),
        self::TYPE_COLNAME       => array(DriverOutputTableMap::COL_ID, DriverOutputTableMap::COL_DRIVER_ID, DriverOutputTableMap::COL_NAME, DriverOutputTableMap::COL_MAPPING, DriverOutputTableMap::COL_OVERLAP_VALUE, DriverOutputTableMap::COL_CENTRAL_VALUE, DriverOutputTableMap::COL_DEFAULT_DELAY, ),
        self::TYPE_FIELDNAME     => array('id', 'driver_id', 'name', 'mapping', 'overlap_value', 'central_value', 'default_delay', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'DriverId' => 1, 'Name' => 2, 'Mapping' => 3, 'OverlapValue' => 4, 'CentralValue' => 5, 'DefaultDelay' => 6, ),
        self::TYPE_CAMELNAME     => array('id' => 0, 'driverId' => 1, 'name' => 2, 'mapping' => 3, 'overlapValue' => 4, 'centralValue' => 5, 'defaultDelay' => 6, ),
        self::TYPE_COLNAME       => array(DriverOutputTableMap::COL_ID => 0, DriverOutputTableMap::COL_DRIVER_ID => 1, DriverOutputTableMap::COL_NAME => 2, DriverOutputTableMap::COL_MAPPING => 3, DriverOutputTableMap::COL_OVERLAP_VALUE => 4, DriverOutputTableMap::COL_CENTRAL_VALUE => 5, DriverOutputTableMap::COL_DEFAULT_DELAY => 6, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'driver_id' => 1, 'name' => 2, 'mapping' => 3, 'overlap_value' => 4, 'central_value' => 5, 'default_delay' => 6, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, )
    );

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('driver_outputs');
        $this->setPhpName('DriverOutput');
        $this->setIdentifierQuoting(false);
        $this->setClassName('\\ArduinoCoilDriver\\Drivers\\DriverOutput');
        $this->setPackage('ArduinoCoilDriver.Drivers');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, 10, null);
        $this->addForeignKey('driver_id', 'DriverId', 'INTEGER', 'drivers', 'id', true, 10, null);
        $this->addColumn('name', 'Name', 'VARCHAR', true, 32, null);
        $this->addColumn('mapping', 'Mapping', 'INTEGER', true, 10, null);
        $this->addColumn('overlap_value', 'OverlapValue', 'INTEGER', true, 10, null);
        $this->addColumn('central_value', 'CentralValue', 'INTEGER', true, 10, null);
        $this->addColumn('default_delay', 'DefaultDelay', 'INTEGER', true, 10, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Driver', '\\ArduinoCoilDriver\\Drivers\\Driver', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':driver_id',
    1 => ':id',
  ),
), null, null, null, false);
        $this->addRelation('DriverOutputPin', '\\ArduinoCoilDriver\\Drivers\\DriverOutputPin', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':driver_output_id',
    1 => ':id',
  ),
), null, null, 'DriverOutputPins', false);
        $this->addRelation('OutputViewOutput', '\\ArduinoCoilDriver\\Outputs\\OutputViewOutput', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':driver_output_id',
    1 => ':id',
  ),
), null, null, 'OutputViewOutputs', false);
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'validate' => array('rule1' => array ('column' => 'name','validator' => 'NotBlank',), 'rule2' => array ('column' => 'name','validator' => 'Length','options' => array ('min' => 3,'max' => 32,),), 'rule3' => array ('column' => 'mapping','validator' => 'Range','options' => array ('min' => 1,'max' => 255,),), 'rule4' => array ('column' => 'overlap_value','validator' => 'Range','options' => array ('min' => 0,'max' => 255,),), 'rule5' => array ('column' => 'central_value','validator' => 'Range','options' => array ('min' => 0,'max' => 65536,),), 'rule6' => array ('column' => 'default_delay','validator' => 'Range','options' => array ('min' => 0,'max' => 250,),), ),
        );
    } // getBehaviors()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return string The primary key hash of the row
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        return (int) $row[
            $indexType == TableMap::TYPE_NUM
                ? 0 + $offset
                : self::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)
        ];
    }

    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param boolean $withPrefix Whether or not to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? DriverOutputTableMap::CLASS_DEFAULT : DriverOutputTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array  $row       row returned by DataFetcher->fetch().
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     * @return array           (DriverOutput object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = DriverOutputTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = DriverOutputTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + DriverOutputTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = DriverOutputTableMap::OM_CLASS;
            /** @var DriverOutput $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            DriverOutputTableMap::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = DriverOutputTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = DriverOutputTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var DriverOutput $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                DriverOutputTableMap::addInstanceToPool($obj, $key);
            } // if key exists
        }

        return $results;
    }
    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria object containing the columns to add.
     * @param string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(DriverOutputTableMap::COL_ID);
            $criteria->addSelectColumn(DriverOutputTableMap::COL_DRIVER_ID);
            $criteria->addSelectColumn(DriverOutputTableMap::COL_NAME);
            $criteria->addSelectColumn(DriverOutputTableMap::COL_MAPPING);
            $criteria->addSelectColumn(DriverOutputTableMap::COL_OVERLAP_VALUE);
            $criteria->addSelectColumn(DriverOutputTableMap::COL_CENTRAL_VALUE);
            $criteria->addSelectColumn(DriverOutputTableMap::COL_DEFAULT_DELAY);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.driver_id');
            $criteria->addSelectColumn($alias . '.name');
            $criteria->addSelectColumn($alias . '.mapping');
            $criteria->addSelectColumn($alias . '.overlap_value');
            $criteria->addSelectColumn($alias . '.central_value');
            $criteria->addSelectColumn($alias . '.default_delay');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(DriverOutputTableMap::DATABASE_NAME)->getTable(DriverOutputTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(DriverOutputTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(DriverOutputTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new DriverOutputTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a DriverOutput or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or DriverOutput object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param  ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(DriverOutputTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \ArduinoCoilDriver\Drivers\DriverOutput) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(DriverOutputTableMap::DATABASE_NAME);
            $criteria->add(DriverOutputTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = DriverOutputQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            DriverOutputTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                DriverOutputTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the driver_outputs table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return DriverOutputQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a DriverOutput or Criteria object.
     *
     * @param mixed               $criteria Criteria or DriverOutput object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(DriverOutputTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from DriverOutput object
        }

        if ($criteria->containsKey(DriverOutputTableMap::COL_ID) && $criteria->keyContainsValue(DriverOutputTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.DriverOutputTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = DriverOutputQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // DriverOutputTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
DriverOutputTableMap::buildTableMap();
