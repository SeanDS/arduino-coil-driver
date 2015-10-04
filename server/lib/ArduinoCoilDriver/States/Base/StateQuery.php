<?php

namespace ArduinoCoilDriver\States\Base;

use \Exception;
use \PDO;
use ArduinoCoilDriver\Drivers\DriverPinValue;
use ArduinoCoilDriver\States\State as ChildState;
use ArduinoCoilDriver\States\StateQuery as ChildStateQuery;
use ArduinoCoilDriver\States\Map\StateTableMap;
use ArduinoCoilDriver\Users\User;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'states' table.
 *
 *
 *
 * @method     ChildStateQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildStateQuery orderByUserId($order = Criteria::ASC) Order by the user_id column
 * @method     ChildStateQuery orderByTime($order = Criteria::ASC) Order by the time column
 *
 * @method     ChildStateQuery groupById() Group by the id column
 * @method     ChildStateQuery groupByUserId() Group by the user_id column
 * @method     ChildStateQuery groupByTime() Group by the time column
 *
 * @method     ChildStateQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildStateQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildStateQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildStateQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildStateQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildStateQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildStateQuery leftJoinUser($relationAlias = null) Adds a LEFT JOIN clause to the query using the User relation
 * @method     ChildStateQuery rightJoinUser($relationAlias = null) Adds a RIGHT JOIN clause to the query using the User relation
 * @method     ChildStateQuery innerJoinUser($relationAlias = null) Adds a INNER JOIN clause to the query using the User relation
 *
 * @method     ChildStateQuery joinWithUser($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the User relation
 *
 * @method     ChildStateQuery leftJoinWithUser() Adds a LEFT JOIN clause and with to the query using the User relation
 * @method     ChildStateQuery rightJoinWithUser() Adds a RIGHT JOIN clause and with to the query using the User relation
 * @method     ChildStateQuery innerJoinWithUser() Adds a INNER JOIN clause and with to the query using the User relation
 *
 * @method     ChildStateQuery leftJoinDriverPinValue($relationAlias = null) Adds a LEFT JOIN clause to the query using the DriverPinValue relation
 * @method     ChildStateQuery rightJoinDriverPinValue($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DriverPinValue relation
 * @method     ChildStateQuery innerJoinDriverPinValue($relationAlias = null) Adds a INNER JOIN clause to the query using the DriverPinValue relation
 *
 * @method     ChildStateQuery joinWithDriverPinValue($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the DriverPinValue relation
 *
 * @method     ChildStateQuery leftJoinWithDriverPinValue() Adds a LEFT JOIN clause and with to the query using the DriverPinValue relation
 * @method     ChildStateQuery rightJoinWithDriverPinValue() Adds a RIGHT JOIN clause and with to the query using the DriverPinValue relation
 * @method     ChildStateQuery innerJoinWithDriverPinValue() Adds a INNER JOIN clause and with to the query using the DriverPinValue relation
 *
 * @method     ChildStateQuery leftJoinStateBookmark($relationAlias = null) Adds a LEFT JOIN clause to the query using the StateBookmark relation
 * @method     ChildStateQuery rightJoinStateBookmark($relationAlias = null) Adds a RIGHT JOIN clause to the query using the StateBookmark relation
 * @method     ChildStateQuery innerJoinStateBookmark($relationAlias = null) Adds a INNER JOIN clause to the query using the StateBookmark relation
 *
 * @method     ChildStateQuery joinWithStateBookmark($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the StateBookmark relation
 *
 * @method     ChildStateQuery leftJoinWithStateBookmark() Adds a LEFT JOIN clause and with to the query using the StateBookmark relation
 * @method     ChildStateQuery rightJoinWithStateBookmark() Adds a RIGHT JOIN clause and with to the query using the StateBookmark relation
 * @method     ChildStateQuery innerJoinWithStateBookmark() Adds a INNER JOIN clause and with to the query using the StateBookmark relation
 *
 * @method     \ArduinoCoilDriver\Users\UserQuery|\ArduinoCoilDriver\Drivers\DriverPinValueQuery|\ArduinoCoilDriver\States\StateBookmarkQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildState findOne(ConnectionInterface $con = null) Return the first ChildState matching the query
 * @method     ChildState findOneOrCreate(ConnectionInterface $con = null) Return the first ChildState matching the query, or a new ChildState object populated from the query conditions when no match is found
 *
 * @method     ChildState findOneById(int $id) Return the first ChildState filtered by the id column
 * @method     ChildState findOneByUserId(int $user_id) Return the first ChildState filtered by the user_id column
 * @method     ChildState findOneByTime(string $time) Return the first ChildState filtered by the time column *

 * @method     ChildState requirePk($key, ConnectionInterface $con = null) Return the ChildState by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildState requireOne(ConnectionInterface $con = null) Return the first ChildState matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildState requireOneById(int $id) Return the first ChildState filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildState requireOneByUserId(int $user_id) Return the first ChildState filtered by the user_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildState requireOneByTime(string $time) Return the first ChildState filtered by the time column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildState[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildState objects based on current ModelCriteria
 * @method     ChildState[]|ObjectCollection findById(int $id) Return ChildState objects filtered by the id column
 * @method     ChildState[]|ObjectCollection findByUserId(int $user_id) Return ChildState objects filtered by the user_id column
 * @method     ChildState[]|ObjectCollection findByTime(string $time) Return ChildState objects filtered by the time column
 * @method     ChildState[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class StateQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \ArduinoCoilDriver\States\Base\StateQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\ArduinoCoilDriver\\States\\State', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildStateQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildStateQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildStateQuery) {
            return $criteria;
        }
        $query = new ChildStateQuery();
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
     * @return ChildState|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = StateTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(StateTableMap::DATABASE_NAME);
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
     * @return ChildState A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, user_id, time FROM states WHERE id = :p0';
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
            /** @var ChildState $obj */
            $obj = new ChildState();
            $obj->hydrate($row);
            StateTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildState|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildStateQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(StateTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildStateQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(StateTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildStateQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(StateTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(StateTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StateTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the user_id column
     *
     * Example usage:
     * <code>
     * $query->filterByUserId(1234); // WHERE user_id = 1234
     * $query->filterByUserId(array(12, 34)); // WHERE user_id IN (12, 34)
     * $query->filterByUserId(array('min' => 12)); // WHERE user_id > 12
     * </code>
     *
     * @see       filterByUser()
     *
     * @param     mixed $userId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildStateQuery The current query, for fluid interface
     */
    public function filterByUserId($userId = null, $comparison = null)
    {
        if (is_array($userId)) {
            $useMinMax = false;
            if (isset($userId['min'])) {
                $this->addUsingAlias(StateTableMap::COL_USER_ID, $userId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($userId['max'])) {
                $this->addUsingAlias(StateTableMap::COL_USER_ID, $userId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StateTableMap::COL_USER_ID, $userId, $comparison);
    }

    /**
     * Filter the query on the time column
     *
     * Example usage:
     * <code>
     * $query->filterByTime('2011-03-14'); // WHERE time = '2011-03-14'
     * $query->filterByTime('now'); // WHERE time = '2011-03-14'
     * $query->filterByTime(array('max' => 'yesterday')); // WHERE time > '2011-03-13'
     * </code>
     *
     * @param     mixed $time The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildStateQuery The current query, for fluid interface
     */
    public function filterByTime($time = null, $comparison = null)
    {
        if (is_array($time)) {
            $useMinMax = false;
            if (isset($time['min'])) {
                $this->addUsingAlias(StateTableMap::COL_TIME, $time['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($time['max'])) {
                $this->addUsingAlias(StateTableMap::COL_TIME, $time['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StateTableMap::COL_TIME, $time, $comparison);
    }

    /**
     * Filter the query by a related \ArduinoCoilDriver\Users\User object
     *
     * @param \ArduinoCoilDriver\Users\User|ObjectCollection $user The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildStateQuery The current query, for fluid interface
     */
    public function filterByUser($user, $comparison = null)
    {
        if ($user instanceof \ArduinoCoilDriver\Users\User) {
            return $this
                ->addUsingAlias(StateTableMap::COL_USER_ID, $user->getId(), $comparison);
        } elseif ($user instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(StateTableMap::COL_USER_ID, $user->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByUser() only accepts arguments of type \ArduinoCoilDriver\Users\User or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the User relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildStateQuery The current query, for fluid interface
     */
    public function joinUser($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('User');

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
            $this->addJoinObject($join, 'User');
        }

        return $this;
    }

    /**
     * Use the User relation User object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \ArduinoCoilDriver\Users\UserQuery A secondary query class using the current class as primary query
     */
    public function useUserQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinUser($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'User', '\ArduinoCoilDriver\Users\UserQuery');
    }

    /**
     * Filter the query by a related \ArduinoCoilDriver\Drivers\DriverPinValue object
     *
     * @param \ArduinoCoilDriver\Drivers\DriverPinValue|ObjectCollection $driverPinValue the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildStateQuery The current query, for fluid interface
     */
    public function filterByDriverPinValue($driverPinValue, $comparison = null)
    {
        if ($driverPinValue instanceof \ArduinoCoilDriver\Drivers\DriverPinValue) {
            return $this
                ->addUsingAlias(StateTableMap::COL_ID, $driverPinValue->getStateId(), $comparison);
        } elseif ($driverPinValue instanceof ObjectCollection) {
            return $this
                ->useDriverPinValueQuery()
                ->filterByPrimaryKeys($driverPinValue->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByDriverPinValue() only accepts arguments of type \ArduinoCoilDriver\Drivers\DriverPinValue or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DriverPinValue relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildStateQuery The current query, for fluid interface
     */
    public function joinDriverPinValue($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DriverPinValue');

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
            $this->addJoinObject($join, 'DriverPinValue');
        }

        return $this;
    }

    /**
     * Use the DriverPinValue relation DriverPinValue object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \ArduinoCoilDriver\Drivers\DriverPinValueQuery A secondary query class using the current class as primary query
     */
    public function useDriverPinValueQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDriverPinValue($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DriverPinValue', '\ArduinoCoilDriver\Drivers\DriverPinValueQuery');
    }

    /**
     * Filter the query by a related \ArduinoCoilDriver\States\StateBookmark object
     *
     * @param \ArduinoCoilDriver\States\StateBookmark|ObjectCollection $stateBookmark the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildStateQuery The current query, for fluid interface
     */
    public function filterByStateBookmark($stateBookmark, $comparison = null)
    {
        if ($stateBookmark instanceof \ArduinoCoilDriver\States\StateBookmark) {
            return $this
                ->addUsingAlias(StateTableMap::COL_ID, $stateBookmark->getStateId(), $comparison);
        } elseif ($stateBookmark instanceof ObjectCollection) {
            return $this
                ->useStateBookmarkQuery()
                ->filterByPrimaryKeys($stateBookmark->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByStateBookmark() only accepts arguments of type \ArduinoCoilDriver\States\StateBookmark or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the StateBookmark relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildStateQuery The current query, for fluid interface
     */
    public function joinStateBookmark($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('StateBookmark');

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
            $this->addJoinObject($join, 'StateBookmark');
        }

        return $this;
    }

    /**
     * Use the StateBookmark relation StateBookmark object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \ArduinoCoilDriver\States\StateBookmarkQuery A secondary query class using the current class as primary query
     */
    public function useStateBookmarkQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinStateBookmark($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'StateBookmark', '\ArduinoCoilDriver\States\StateBookmarkQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildState $state Object to remove from the list of results
     *
     * @return $this|ChildStateQuery The current query, for fluid interface
     */
    public function prune($state = null)
    {
        if ($state) {
            $this->addUsingAlias(StateTableMap::COL_ID, $state->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the states table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(StateTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            StateTableMap::clearInstancePool();
            StateTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(StateTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(StateTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            StateTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            StateTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // StateQuery
