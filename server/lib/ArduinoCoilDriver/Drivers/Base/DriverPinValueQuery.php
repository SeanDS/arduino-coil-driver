<?php

namespace ArduinoCoilDriver\Drivers\Base;

use \Exception;
use \PDO;
use ArduinoCoilDriver\Drivers\DriverPinValue as ChildDriverPinValue;
use ArduinoCoilDriver\Drivers\DriverPinValueQuery as ChildDriverPinValueQuery;
use ArduinoCoilDriver\Drivers\Map\DriverPinValueTableMap;
use ArduinoCoilDriver\States\State;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'driver_pin_values' table.
 *
 *
 *
 * @method     ChildDriverPinValueQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildDriverPinValueQuery orderByDriverPinId($order = Criteria::ASC) Order by the driver_pin_id column
 * @method     ChildDriverPinValueQuery orderByStateId($order = Criteria::ASC) Order by the state_id column
 * @method     ChildDriverPinValueQuery orderByValue($order = Criteria::ASC) Order by the value column
 *
 * @method     ChildDriverPinValueQuery groupById() Group by the id column
 * @method     ChildDriverPinValueQuery groupByDriverPinId() Group by the driver_pin_id column
 * @method     ChildDriverPinValueQuery groupByStateId() Group by the state_id column
 * @method     ChildDriverPinValueQuery groupByValue() Group by the value column
 *
 * @method     ChildDriverPinValueQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildDriverPinValueQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildDriverPinValueQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildDriverPinValueQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildDriverPinValueQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildDriverPinValueQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildDriverPinValueQuery leftJoinDriverPin($relationAlias = null) Adds a LEFT JOIN clause to the query using the DriverPin relation
 * @method     ChildDriverPinValueQuery rightJoinDriverPin($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DriverPin relation
 * @method     ChildDriverPinValueQuery innerJoinDriverPin($relationAlias = null) Adds a INNER JOIN clause to the query using the DriverPin relation
 *
 * @method     ChildDriverPinValueQuery joinWithDriverPin($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the DriverPin relation
 *
 * @method     ChildDriverPinValueQuery leftJoinWithDriverPin() Adds a LEFT JOIN clause and with to the query using the DriverPin relation
 * @method     ChildDriverPinValueQuery rightJoinWithDriverPin() Adds a RIGHT JOIN clause and with to the query using the DriverPin relation
 * @method     ChildDriverPinValueQuery innerJoinWithDriverPin() Adds a INNER JOIN clause and with to the query using the DriverPin relation
 *
 * @method     ChildDriverPinValueQuery leftJoinState($relationAlias = null) Adds a LEFT JOIN clause to the query using the State relation
 * @method     ChildDriverPinValueQuery rightJoinState($relationAlias = null) Adds a RIGHT JOIN clause to the query using the State relation
 * @method     ChildDriverPinValueQuery innerJoinState($relationAlias = null) Adds a INNER JOIN clause to the query using the State relation
 *
 * @method     ChildDriverPinValueQuery joinWithState($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the State relation
 *
 * @method     ChildDriverPinValueQuery leftJoinWithState() Adds a LEFT JOIN clause and with to the query using the State relation
 * @method     ChildDriverPinValueQuery rightJoinWithState() Adds a RIGHT JOIN clause and with to the query using the State relation
 * @method     ChildDriverPinValueQuery innerJoinWithState() Adds a INNER JOIN clause and with to the query using the State relation
 *
 * @method     \ArduinoCoilDriver\Drivers\DriverPinQuery|\ArduinoCoilDriver\States\StateQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildDriverPinValue findOne(ConnectionInterface $con = null) Return the first ChildDriverPinValue matching the query
 * @method     ChildDriverPinValue findOneOrCreate(ConnectionInterface $con = null) Return the first ChildDriverPinValue matching the query, or a new ChildDriverPinValue object populated from the query conditions when no match is found
 *
 * @method     ChildDriverPinValue findOneById(int $id) Return the first ChildDriverPinValue filtered by the id column
 * @method     ChildDriverPinValue findOneByDriverPinId(int $driver_pin_id) Return the first ChildDriverPinValue filtered by the driver_pin_id column
 * @method     ChildDriverPinValue findOneByStateId(int $state_id) Return the first ChildDriverPinValue filtered by the state_id column
 * @method     ChildDriverPinValue findOneByValue(int $value) Return the first ChildDriverPinValue filtered by the value column *

 * @method     ChildDriverPinValue requirePk($key, ConnectionInterface $con = null) Return the ChildDriverPinValue by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildDriverPinValue requireOne(ConnectionInterface $con = null) Return the first ChildDriverPinValue matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildDriverPinValue requireOneById(int $id) Return the first ChildDriverPinValue filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildDriverPinValue requireOneByDriverPinId(int $driver_pin_id) Return the first ChildDriverPinValue filtered by the driver_pin_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildDriverPinValue requireOneByStateId(int $state_id) Return the first ChildDriverPinValue filtered by the state_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildDriverPinValue requireOneByValue(int $value) Return the first ChildDriverPinValue filtered by the value column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildDriverPinValue[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildDriverPinValue objects based on current ModelCriteria
 * @method     ChildDriverPinValue[]|ObjectCollection findById(int $id) Return ChildDriverPinValue objects filtered by the id column
 * @method     ChildDriverPinValue[]|ObjectCollection findByDriverPinId(int $driver_pin_id) Return ChildDriverPinValue objects filtered by the driver_pin_id column
 * @method     ChildDriverPinValue[]|ObjectCollection findByStateId(int $state_id) Return ChildDriverPinValue objects filtered by the state_id column
 * @method     ChildDriverPinValue[]|ObjectCollection findByValue(int $value) Return ChildDriverPinValue objects filtered by the value column
 * @method     ChildDriverPinValue[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class DriverPinValueQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \ArduinoCoilDriver\Drivers\Base\DriverPinValueQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\ArduinoCoilDriver\\Drivers\\DriverPinValue', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildDriverPinValueQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildDriverPinValueQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildDriverPinValueQuery) {
            return $criteria;
        }
        $query = new ChildDriverPinValueQuery();
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
     * @return ChildDriverPinValue|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = DriverPinValueTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(DriverPinValueTableMap::DATABASE_NAME);
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
     * @return ChildDriverPinValue A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, driver_pin_id, state_id, value FROM driver_pin_values WHERE id = :p0';
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
            /** @var ChildDriverPinValue $obj */
            $obj = new ChildDriverPinValue();
            $obj->hydrate($row);
            DriverPinValueTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildDriverPinValue|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildDriverPinValueQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(DriverPinValueTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildDriverPinValueQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(DriverPinValueTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildDriverPinValueQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(DriverPinValueTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(DriverPinValueTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DriverPinValueTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the driver_pin_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDriverPinId(1234); // WHERE driver_pin_id = 1234
     * $query->filterByDriverPinId(array(12, 34)); // WHERE driver_pin_id IN (12, 34)
     * $query->filterByDriverPinId(array('min' => 12)); // WHERE driver_pin_id > 12
     * </code>
     *
     * @see       filterByDriverPin()
     *
     * @param     mixed $driverPinId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildDriverPinValueQuery The current query, for fluid interface
     */
    public function filterByDriverPinId($driverPinId = null, $comparison = null)
    {
        if (is_array($driverPinId)) {
            $useMinMax = false;
            if (isset($driverPinId['min'])) {
                $this->addUsingAlias(DriverPinValueTableMap::COL_DRIVER_PIN_ID, $driverPinId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($driverPinId['max'])) {
                $this->addUsingAlias(DriverPinValueTableMap::COL_DRIVER_PIN_ID, $driverPinId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DriverPinValueTableMap::COL_DRIVER_PIN_ID, $driverPinId, $comparison);
    }

    /**
     * Filter the query on the state_id column
     *
     * Example usage:
     * <code>
     * $query->filterByStateId(1234); // WHERE state_id = 1234
     * $query->filterByStateId(array(12, 34)); // WHERE state_id IN (12, 34)
     * $query->filterByStateId(array('min' => 12)); // WHERE state_id > 12
     * </code>
     *
     * @see       filterByState()
     *
     * @param     mixed $stateId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildDriverPinValueQuery The current query, for fluid interface
     */
    public function filterByStateId($stateId = null, $comparison = null)
    {
        if (is_array($stateId)) {
            $useMinMax = false;
            if (isset($stateId['min'])) {
                $this->addUsingAlias(DriverPinValueTableMap::COL_STATE_ID, $stateId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($stateId['max'])) {
                $this->addUsingAlias(DriverPinValueTableMap::COL_STATE_ID, $stateId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DriverPinValueTableMap::COL_STATE_ID, $stateId, $comparison);
    }

    /**
     * Filter the query on the value column
     *
     * Example usage:
     * <code>
     * $query->filterByValue(1234); // WHERE value = 1234
     * $query->filterByValue(array(12, 34)); // WHERE value IN (12, 34)
     * $query->filterByValue(array('min' => 12)); // WHERE value > 12
     * </code>
     *
     * @param     mixed $value The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildDriverPinValueQuery The current query, for fluid interface
     */
    public function filterByValue($value = null, $comparison = null)
    {
        if (is_array($value)) {
            $useMinMax = false;
            if (isset($value['min'])) {
                $this->addUsingAlias(DriverPinValueTableMap::COL_VALUE, $value['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($value['max'])) {
                $this->addUsingAlias(DriverPinValueTableMap::COL_VALUE, $value['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DriverPinValueTableMap::COL_VALUE, $value, $comparison);
    }

    /**
     * Filter the query by a related \ArduinoCoilDriver\Drivers\DriverPin object
     *
     * @param \ArduinoCoilDriver\Drivers\DriverPin|ObjectCollection $driverPin The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildDriverPinValueQuery The current query, for fluid interface
     */
    public function filterByDriverPin($driverPin, $comparison = null)
    {
        if ($driverPin instanceof \ArduinoCoilDriver\Drivers\DriverPin) {
            return $this
                ->addUsingAlias(DriverPinValueTableMap::COL_DRIVER_PIN_ID, $driverPin->getId(), $comparison);
        } elseif ($driverPin instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(DriverPinValueTableMap::COL_DRIVER_PIN_ID, $driverPin->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByDriverPin() only accepts arguments of type \ArduinoCoilDriver\Drivers\DriverPin or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DriverPin relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildDriverPinValueQuery The current query, for fluid interface
     */
    public function joinDriverPin($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DriverPin');

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
            $this->addJoinObject($join, 'DriverPin');
        }

        return $this;
    }

    /**
     * Use the DriverPin relation DriverPin object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \ArduinoCoilDriver\Drivers\DriverPinQuery A secondary query class using the current class as primary query
     */
    public function useDriverPinQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDriverPin($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DriverPin', '\ArduinoCoilDriver\Drivers\DriverPinQuery');
    }

    /**
     * Filter the query by a related \ArduinoCoilDriver\States\State object
     *
     * @param \ArduinoCoilDriver\States\State|ObjectCollection $state The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildDriverPinValueQuery The current query, for fluid interface
     */
    public function filterByState($state, $comparison = null)
    {
        if ($state instanceof \ArduinoCoilDriver\States\State) {
            return $this
                ->addUsingAlias(DriverPinValueTableMap::COL_STATE_ID, $state->getId(), $comparison);
        } elseif ($state instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(DriverPinValueTableMap::COL_STATE_ID, $state->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByState() only accepts arguments of type \ArduinoCoilDriver\States\State or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the State relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildDriverPinValueQuery The current query, for fluid interface
     */
    public function joinState($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('State');

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
            $this->addJoinObject($join, 'State');
        }

        return $this;
    }

    /**
     * Use the State relation State object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \ArduinoCoilDriver\States\StateQuery A secondary query class using the current class as primary query
     */
    public function useStateQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinState($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'State', '\ArduinoCoilDriver\States\StateQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildDriverPinValue $driverPinValue Object to remove from the list of results
     *
     * @return $this|ChildDriverPinValueQuery The current query, for fluid interface
     */
    public function prune($driverPinValue = null)
    {
        if ($driverPinValue) {
            $this->addUsingAlias(DriverPinValueTableMap::COL_ID, $driverPinValue->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the driver_pin_values table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(DriverPinValueTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            DriverPinValueTableMap::clearInstancePool();
            DriverPinValueTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(DriverPinValueTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(DriverPinValueTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            DriverPinValueTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            DriverPinValueTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // DriverPinValueQuery
