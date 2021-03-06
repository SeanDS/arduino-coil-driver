<?php

namespace ArduinoCoilDriver\Drivers\Map;

use ArduinoCoilDriver\Drivers\Driver;
use ArduinoCoilDriver\Drivers\DriverQuery;
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
 * This class defines the structure of the 'drivers' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class DriverTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'ArduinoCoilDriver.Drivers.Map.DriverTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'drivers';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\ArduinoCoilDriver\\Drivers\\Driver';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'ArduinoCoilDriver.Drivers.Driver';

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
    const COL_ID = 'drivers.id';

    /**
     * the column name for the name field
     */
    const COL_NAME = 'drivers.name';

    /**
     * the column name for the mac field
     */
    const COL_MAC = 'drivers.mac';

    /**
     * the column name for the ip field
     */
    const COL_IP = 'drivers.ip';

    /**
     * the column name for the added field
     */
    const COL_ADDED = 'drivers.added';

    /**
     * the column name for the last_check_in field
     */
    const COL_LAST_CHECK_IN = 'drivers.last_check_in';

    /**
     * the column name for the coil_contact field
     */
    const COL_COIL_CONTACT = 'drivers.coil_contact';

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
        self::TYPE_PHPNAME       => array('Id', 'Name', 'Mac', 'Ip', 'Added', 'LastCheckIn', 'CoilContact', ),
        self::TYPE_CAMELNAME     => array('id', 'name', 'mac', 'ip', 'added', 'lastCheckIn', 'coilContact', ),
        self::TYPE_COLNAME       => array(DriverTableMap::COL_ID, DriverTableMap::COL_NAME, DriverTableMap::COL_MAC, DriverTableMap::COL_IP, DriverTableMap::COL_ADDED, DriverTableMap::COL_LAST_CHECK_IN, DriverTableMap::COL_COIL_CONTACT, ),
        self::TYPE_FIELDNAME     => array('id', 'name', 'mac', 'ip', 'added', 'last_check_in', 'coil_contact', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Name' => 1, 'Mac' => 2, 'Ip' => 3, 'Added' => 4, 'LastCheckIn' => 5, 'CoilContact' => 6, ),
        self::TYPE_CAMELNAME     => array('id' => 0, 'name' => 1, 'mac' => 2, 'ip' => 3, 'added' => 4, 'lastCheckIn' => 5, 'coilContact' => 6, ),
        self::TYPE_COLNAME       => array(DriverTableMap::COL_ID => 0, DriverTableMap::COL_NAME => 1, DriverTableMap::COL_MAC => 2, DriverTableMap::COL_IP => 3, DriverTableMap::COL_ADDED => 4, DriverTableMap::COL_LAST_CHECK_IN => 5, DriverTableMap::COL_COIL_CONTACT => 6, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'name' => 1, 'mac' => 2, 'ip' => 3, 'added' => 4, 'last_check_in' => 5, 'coil_contact' => 6, ),
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
        $this->setName('drivers');
        $this->setPhpName('Driver');
        $this->setIdentifierQuoting(false);
        $this->setClassName('\\ArduinoCoilDriver\\Drivers\\Driver');
        $this->setPackage('ArduinoCoilDriver.Drivers');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, 10, null);
        $this->addColumn('name', 'Name', 'VARCHAR', true, 32, null);
        $this->addColumn('mac', 'Mac', 'CHAR', true, 17, null);
        $this->addColumn('ip', 'Ip', 'VARCHAR', true, 15, null);
        $this->addColumn('added', 'Added', 'TIMESTAMP', true, null, null);
        $this->addColumn('last_check_in', 'LastCheckIn', 'TIMESTAMP', true, null, null);
        $this->addColumn('coil_contact', 'CoilContact', 'BOOLEAN', true, 1, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('DriverPin', '\\ArduinoCoilDriver\\Drivers\\DriverPin', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':driver_id',
    1 => ':id',
  ),
), null, null, 'DriverPins', false);
        $this->addRelation('DriverOutput', '\\ArduinoCoilDriver\\Drivers\\DriverOutput', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':driver_id',
    1 => ':id',
  ),
), null, null, 'DriverOutputs', false);
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
            'validate' => array('rule1' => array ('column' => 'name','validator' => 'NotBlank',), 'rule2' => array ('column' => 'name','validator' => 'Length','options' => array ('min' => 3,'max' => 32,),), 'rule3' => array ('column' => 'mac','validator' => 'Length','options' => array ('min' => 17,'max' => 17,),), 'rule4' => array ('column' => 'ip','validator' => 'Length','options' => array ('min' => 7,'max' => 15,),), ),
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
        return $withPrefix ? DriverTableMap::CLASS_DEFAULT : DriverTableMap::OM_CLASS;
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
     * @return array           (Driver object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = DriverTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = DriverTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + DriverTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = DriverTableMap::OM_CLASS;
            /** @var Driver $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            DriverTableMap::addInstanceToPool($obj, $key);
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
            $key = DriverTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = DriverTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var Driver $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                DriverTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(DriverTableMap::COL_ID);
            $criteria->addSelectColumn(DriverTableMap::COL_NAME);
            $criteria->addSelectColumn(DriverTableMap::COL_MAC);
            $criteria->addSelectColumn(DriverTableMap::COL_IP);
            $criteria->addSelectColumn(DriverTableMap::COL_ADDED);
            $criteria->addSelectColumn(DriverTableMap::COL_LAST_CHECK_IN);
            $criteria->addSelectColumn(DriverTableMap::COL_COIL_CONTACT);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.name');
            $criteria->addSelectColumn($alias . '.mac');
            $criteria->addSelectColumn($alias . '.ip');
            $criteria->addSelectColumn($alias . '.added');
            $criteria->addSelectColumn($alias . '.last_check_in');
            $criteria->addSelectColumn($alias . '.coil_contact');
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
        return Propel::getServiceContainer()->getDatabaseMap(DriverTableMap::DATABASE_NAME)->getTable(DriverTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(DriverTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(DriverTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new DriverTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a Driver or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Driver object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(DriverTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \ArduinoCoilDriver\Drivers\Driver) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(DriverTableMap::DATABASE_NAME);
            $criteria->add(DriverTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = DriverQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            DriverTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                DriverTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the drivers table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return DriverQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Driver or Criteria object.
     *
     * @param mixed               $criteria Criteria or Driver object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(DriverTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Driver object
        }

        if ($criteria->containsKey(DriverTableMap::COL_ID) && $criteria->keyContainsValue(DriverTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.DriverTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = DriverQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // DriverTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
DriverTableMap::buildTableMap();
