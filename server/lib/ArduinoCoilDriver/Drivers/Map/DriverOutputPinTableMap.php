<?php

namespace ArduinoCoilDriver\Drivers\Map;

use ArduinoCoilDriver\Drivers\DriverOutputPin;
use ArduinoCoilDriver\Drivers\DriverOutputPinQuery;
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
 * This class defines the structure of the 'driver_output_pins' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class DriverOutputPinTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'ArduinoCoilDriver.Drivers.Map.DriverOutputPinTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'driver_output_pins';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\ArduinoCoilDriver\\Drivers\\DriverOutputPin';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'ArduinoCoilDriver.Drivers.DriverOutputPin';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 4;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 4;

    /**
     * the column name for the id field
     */
    const COL_ID = 'driver_output_pins.id';

    /**
     * the column name for the driver_output_id field
     */
    const COL_DRIVER_OUTPUT_ID = 'driver_output_pins.driver_output_id';

    /**
     * the column name for the driver_pin_id field
     */
    const COL_DRIVER_PIN_ID = 'driver_output_pins.driver_pin_id';

    /**
     * the column name for the type field
     */
    const COL_TYPE = 'driver_output_pins.type';

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
        self::TYPE_PHPNAME       => array('Id', 'DriverOutputId', 'DriverPinId', 'Type', ),
        self::TYPE_CAMELNAME     => array('id', 'driverOutputId', 'driverPinId', 'type', ),
        self::TYPE_COLNAME       => array(DriverOutputPinTableMap::COL_ID, DriverOutputPinTableMap::COL_DRIVER_OUTPUT_ID, DriverOutputPinTableMap::COL_DRIVER_PIN_ID, DriverOutputPinTableMap::COL_TYPE, ),
        self::TYPE_FIELDNAME     => array('id', 'driver_output_id', 'driver_pin_id', 'type', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'DriverOutputId' => 1, 'DriverPinId' => 2, 'Type' => 3, ),
        self::TYPE_CAMELNAME     => array('id' => 0, 'driverOutputId' => 1, 'driverPinId' => 2, 'type' => 3, ),
        self::TYPE_COLNAME       => array(DriverOutputPinTableMap::COL_ID => 0, DriverOutputPinTableMap::COL_DRIVER_OUTPUT_ID => 1, DriverOutputPinTableMap::COL_DRIVER_PIN_ID => 2, DriverOutputPinTableMap::COL_TYPE => 3, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'driver_output_id' => 1, 'driver_pin_id' => 2, 'type' => 3, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, )
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
        $this->setName('driver_output_pins');
        $this->setPhpName('DriverOutputPin');
        $this->setIdentifierQuoting(false);
        $this->setClassName('\\ArduinoCoilDriver\\Drivers\\DriverOutputPin');
        $this->setPackage('ArduinoCoilDriver.Drivers');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, 10, null);
        $this->addForeignKey('driver_output_id', 'DriverOutputId', 'INTEGER', 'driver_outputs', 'id', true, 10, null);
        $this->addForeignKey('driver_pin_id', 'DriverPinId', 'INTEGER', 'driver_pins', 'id', true, 10, null);
        $this->addColumn('type', 'Type', 'CHAR', true, null, 'coarse');
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('DriverOutput', '\\ArduinoCoilDriver\\Drivers\\DriverOutput', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':driver_output_id',
    1 => ':id',
  ),
), null, null, null, false);
        $this->addRelation('DriverPin', '\\ArduinoCoilDriver\\Drivers\\DriverPin', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':driver_pin_id',
    1 => ':id',
  ),
), null, null, null, false);
    } // buildRelations()

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
        return $withPrefix ? DriverOutputPinTableMap::CLASS_DEFAULT : DriverOutputPinTableMap::OM_CLASS;
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
     * @return array           (DriverOutputPin object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = DriverOutputPinTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = DriverOutputPinTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + DriverOutputPinTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = DriverOutputPinTableMap::OM_CLASS;
            /** @var DriverOutputPin $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            DriverOutputPinTableMap::addInstanceToPool($obj, $key);
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
            $key = DriverOutputPinTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = DriverOutputPinTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var DriverOutputPin $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                DriverOutputPinTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(DriverOutputPinTableMap::COL_ID);
            $criteria->addSelectColumn(DriverOutputPinTableMap::COL_DRIVER_OUTPUT_ID);
            $criteria->addSelectColumn(DriverOutputPinTableMap::COL_DRIVER_PIN_ID);
            $criteria->addSelectColumn(DriverOutputPinTableMap::COL_TYPE);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.driver_output_id');
            $criteria->addSelectColumn($alias . '.driver_pin_id');
            $criteria->addSelectColumn($alias . '.type');
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
        return Propel::getServiceContainer()->getDatabaseMap(DriverOutputPinTableMap::DATABASE_NAME)->getTable(DriverOutputPinTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(DriverOutputPinTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(DriverOutputPinTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new DriverOutputPinTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a DriverOutputPin or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or DriverOutputPin object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(DriverOutputPinTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \ArduinoCoilDriver\Drivers\DriverOutputPin) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(DriverOutputPinTableMap::DATABASE_NAME);
            $criteria->add(DriverOutputPinTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = DriverOutputPinQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            DriverOutputPinTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                DriverOutputPinTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the driver_output_pins table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return DriverOutputPinQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a DriverOutputPin or Criteria object.
     *
     * @param mixed               $criteria Criteria or DriverOutputPin object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(DriverOutputPinTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from DriverOutputPin object
        }

        if ($criteria->containsKey(DriverOutputPinTableMap::COL_ID) && $criteria->keyContainsValue(DriverOutputPinTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.DriverOutputPinTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = DriverOutputPinQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // DriverOutputPinTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
DriverOutputPinTableMap::buildTableMap();
