<?php

namespace ArduinoCoilDriver\Drivers\Base;

use \Exception;
use \PDO;
use ArduinoCoilDriver\Drivers\DriverOutput as ChildDriverOutput;
use ArduinoCoilDriver\Drivers\DriverOutputQuery as ChildDriverOutputQuery;
use ArduinoCoilDriver\Drivers\Map\DriverOutputTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'driver_outputs' table.
 *
 *
 *
 * @method     ChildDriverOutputQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildDriverOutputQuery orderByDriverId($order = Criteria::ASC) Order by the driver_id column
 * @method     ChildDriverOutputQuery orderByName($order = Criteria::ASC) Order by the name column
 *
 * @method     ChildDriverOutputQuery groupById() Group by the id column
 * @method     ChildDriverOutputQuery groupByDriverId() Group by the driver_id column
 * @method     ChildDriverOutputQuery groupByName() Group by the name column
 *
 * @method     ChildDriverOutputQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildDriverOutputQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildDriverOutputQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildDriverOutputQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildDriverOutputQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildDriverOutputQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildDriverOutputQuery leftJoinDriver($relationAlias = null) Adds a LEFT JOIN clause to the query using the Driver relation
 * @method     ChildDriverOutputQuery rightJoinDriver($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Driver relation
 * @method     ChildDriverOutputQuery innerJoinDriver($relationAlias = null) Adds a INNER JOIN clause to the query using the Driver relation
 *
 * @method     ChildDriverOutputQuery joinWithDriver($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Driver relation
 *
 * @method     ChildDriverOutputQuery leftJoinWithDriver() Adds a LEFT JOIN clause and with to the query using the Driver relation
 * @method     ChildDriverOutputQuery rightJoinWithDriver() Adds a RIGHT JOIN clause and with to the query using the Driver relation
 * @method     ChildDriverOutputQuery innerJoinWithDriver() Adds a INNER JOIN clause and with to the query using the Driver relation
 *
 * @method     ChildDriverOutputQuery leftJoinDriverOutputPin($relationAlias = null) Adds a LEFT JOIN clause to the query using the DriverOutputPin relation
 * @method     ChildDriverOutputQuery rightJoinDriverOutputPin($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DriverOutputPin relation
 * @method     ChildDriverOutputQuery innerJoinDriverOutputPin($relationAlias = null) Adds a INNER JOIN clause to the query using the DriverOutputPin relation
 *
 * @method     ChildDriverOutputQuery joinWithDriverOutputPin($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the DriverOutputPin relation
 *
 * @method     ChildDriverOutputQuery leftJoinWithDriverOutputPin() Adds a LEFT JOIN clause and with to the query using the DriverOutputPin relation
 * @method     ChildDriverOutputQuery rightJoinWithDriverOutputPin() Adds a RIGHT JOIN clause and with to the query using the DriverOutputPin relation
 * @method     ChildDriverOutputQuery innerJoinWithDriverOutputPin() Adds a INNER JOIN clause and with to the query using the DriverOutputPin relation
 *
 * @method     \ArduinoCoilDriver\Drivers\DriverQuery|\ArduinoCoilDriver\Drivers\DriverOutputPinQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildDriverOutput findOne(ConnectionInterface $con = null) Return the first ChildDriverOutput matching the query
 * @method     ChildDriverOutput findOneOrCreate(ConnectionInterface $con = null) Return the first ChildDriverOutput matching the query, or a new ChildDriverOutput object populated from the query conditions when no match is found
 *
 * @method     ChildDriverOutput findOneById(int $id) Return the first ChildDriverOutput filtered by the id column
 * @method     ChildDriverOutput findOneByDriverId(int $driver_id) Return the first ChildDriverOutput filtered by the driver_id column
 * @method     ChildDriverOutput findOneByName(string $name) Return the first ChildDriverOutput filtered by the name column *

 * @method     ChildDriverOutput requirePk($key, ConnectionInterface $con = null) Return the ChildDriverOutput by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildDriverOutput requireOne(ConnectionInterface $con = null) Return the first ChildDriverOutput matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildDriverOutput requireOneById(int $id) Return the first ChildDriverOutput filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildDriverOutput requireOneByDriverId(int $driver_id) Return the first ChildDriverOutput filtered by the driver_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildDriverOutput requireOneByName(string $name) Return the first ChildDriverOutput filtered by the name column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildDriverOutput[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildDriverOutput objects based on current ModelCriteria
 * @method     ChildDriverOutput[]|ObjectCollection findById(int $id) Return ChildDriverOutput objects filtered by the id column
 * @method     ChildDriverOutput[]|ObjectCollection findByDriverId(int $driver_id) Return ChildDriverOutput objects filtered by the driver_id column
 * @method     ChildDriverOutput[]|ObjectCollection findByName(string $name) Return ChildDriverOutput objects filtered by the name column
 * @method     ChildDriverOutput[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class DriverOutputQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \ArduinoCoilDriver\Drivers\Base\DriverOutputQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\ArduinoCoilDriver\\Drivers\\DriverOutput', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildDriverOutputQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildDriverOutputQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildDriverOutputQuery) {
            return $criteria;
        }
        $query = new ChildDriverOutputQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildDriverOutput|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = DriverOutputTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(DriverOutputTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildDriverOutput A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, driver_id, name FROM driver_outputs WHERE id = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildDriverOutput $obj */
            $obj = new ChildDriverOutput();
            $obj->hydrate($row);
            DriverOutputTableMap::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildDriverOutput|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, ConnectionInterface $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return $this|ChildDriverOutputQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(DriverOutputTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildDriverOutputQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(DriverOutputTableMap::COL_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildDriverOutputQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(DriverOutputTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(DriverOutputTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DriverOutputTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the driver_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDriverId(1234); // WHERE driver_id = 1234
     * $query->filterByDriverId(array(12, 34)); // WHERE driver_id IN (12, 34)
     * $query->filterByDriverId(array('min' => 12)); // WHERE driver_id > 12
     * </code>
     *
     * @see       filterByDriver()
     *
     * @param     mixed $driverId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildDriverOutputQuery The current query, for fluid interface
     */
    public function filterByDriverId($driverId = null, $comparison = null)
    {
        if (is_array($driverId)) {
            $useMinMax = false;
            if (isset($driverId['min'])) {
                $this->addUsingAlias(DriverOutputTableMap::COL_DRIVER_ID, $driverId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($driverId['max'])) {
                $this->addUsingAlias(DriverOutputTableMap::COL_DRIVER_ID, $driverId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DriverOutputTableMap::COL_DRIVER_ID, $driverId, $comparison);
    }

    /**
     * Filter the query on the name column
     *
     * Example usage:
     * <code>
     * $query->filterByName('fooValue');   // WHERE name = 'fooValue'
     * $query->filterByName('%fooValue%'); // WHERE name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $name The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildDriverOutputQuery The current query, for fluid interface
     */
    public function filterByName($name = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($name)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $name)) {
                $name = str_replace('*', '%', $name);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(DriverOutputTableMap::COL_NAME, $name, $comparison);
    }

    /**
     * Filter the query by a related \ArduinoCoilDriver\Drivers\Driver object
     *
     * @param \ArduinoCoilDriver\Drivers\Driver|ObjectCollection $driver The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildDriverOutputQuery The current query, for fluid interface
     */
    public function filterByDriver($driver, $comparison = null)
    {
        if ($driver instanceof \ArduinoCoilDriver\Drivers\Driver) {
            return $this
                ->addUsingAlias(DriverOutputTableMap::COL_DRIVER_ID, $driver->getId(), $comparison);
        } elseif ($driver instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(DriverOutputTableMap::COL_DRIVER_ID, $driver->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByDriver() only accepts arguments of type \ArduinoCoilDriver\Drivers\Driver or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Driver relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildDriverOutputQuery The current query, for fluid interface
     */
    public function joinDriver($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Driver');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Driver');
        }

        return $this;
    }

    /**
     * Use the Driver relation Driver object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \ArduinoCoilDriver\Drivers\DriverQuery A secondary query class using the current class as primary query
     */
    public function useDriverQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDriver($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Driver', '\ArduinoCoilDriver\Drivers\DriverQuery');
    }

    /**
     * Filter the query by a related \ArduinoCoilDriver\Drivers\DriverOutputPin object
     *
     * @param \ArduinoCoilDriver\Drivers\DriverOutputPin|ObjectCollection $driverOutputPin the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDriverOutputQuery The current query, for fluid interface
     */
    public function filterByDriverOutputPin($driverOutputPin, $comparison = null)
    {
        if ($driverOutputPin instanceof \ArduinoCoilDriver\Drivers\DriverOutputPin) {
            return $this
                ->addUsingAlias(DriverOutputTableMap::COL_ID, $driverOutputPin->getDriverOutputId(), $comparison);
        } elseif ($driverOutputPin instanceof ObjectCollection) {
            return $this
                ->useDriverOutputPinQuery()
                ->filterByPrimaryKeys($driverOutputPin->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByDriverOutputPin() only accepts arguments of type \ArduinoCoilDriver\Drivers\DriverOutputPin or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DriverOutputPin relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildDriverOutputQuery The current query, for fluid interface
     */
    public function joinDriverOutputPin($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DriverOutputPin');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'DriverOutputPin');
        }

        return $this;
    }

    /**
     * Use the DriverOutputPin relation DriverOutputPin object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \ArduinoCoilDriver\Drivers\DriverOutputPinQuery A secondary query class using the current class as primary query
     */
    public function useDriverOutputPinQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDriverOutputPin($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DriverOutputPin', '\ArduinoCoilDriver\Drivers\DriverOutputPinQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildDriverOutput $driverOutput Object to remove from the list of results
     *
     * @return $this|ChildDriverOutputQuery The current query, for fluid interface
     */
    public function prune($driverOutput = null)
    {
        if ($driverOutput) {
            $this->addUsingAlias(DriverOutputTableMap::COL_ID, $driverOutput->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the driver_outputs table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(DriverOutputTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            DriverOutputTableMap::clearInstancePool();
            DriverOutputTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(DriverOutputTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(DriverOutputTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            DriverOutputTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            DriverOutputTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // DriverOutputQuery
