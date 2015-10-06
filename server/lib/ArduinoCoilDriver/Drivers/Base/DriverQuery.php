<?php

namespace ArduinoCoilDriver\Drivers\Base;

use \Exception;
use \PDO;
use ArduinoCoilDriver\Drivers\Driver as ChildDriver;
use ArduinoCoilDriver\Drivers\DriverQuery as ChildDriverQuery;
use ArduinoCoilDriver\Drivers\Map\DriverTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'drivers' table.
 *
 *
 *
 * @method     ChildDriverQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildDriverQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildDriverQuery orderByMac($order = Criteria::ASC) Order by the mac column
 * @method     ChildDriverQuery orderByIp($order = Criteria::ASC) Order by the ip column
 * @method     ChildDriverQuery orderByAdded($order = Criteria::ASC) Order by the added column
 * @method     ChildDriverQuery orderByLastCheckIn($order = Criteria::ASC) Order by the last_check_in column
 * @method     ChildDriverQuery orderByCoilContact($order = Criteria::ASC) Order by the coil_contact column
 *
 * @method     ChildDriverQuery groupById() Group by the id column
 * @method     ChildDriverQuery groupByName() Group by the name column
 * @method     ChildDriverQuery groupByMac() Group by the mac column
 * @method     ChildDriverQuery groupByIp() Group by the ip column
 * @method     ChildDriverQuery groupByAdded() Group by the added column
 * @method     ChildDriverQuery groupByLastCheckIn() Group by the last_check_in column
 * @method     ChildDriverQuery groupByCoilContact() Group by the coil_contact column
 *
 * @method     ChildDriverQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildDriverQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildDriverQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildDriverQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildDriverQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildDriverQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildDriverQuery leftJoinDriverPin($relationAlias = null) Adds a LEFT JOIN clause to the query using the DriverPin relation
 * @method     ChildDriverQuery rightJoinDriverPin($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DriverPin relation
 * @method     ChildDriverQuery innerJoinDriverPin($relationAlias = null) Adds a INNER JOIN clause to the query using the DriverPin relation
 *
 * @method     ChildDriverQuery joinWithDriverPin($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the DriverPin relation
 *
 * @method     ChildDriverQuery leftJoinWithDriverPin() Adds a LEFT JOIN clause and with to the query using the DriverPin relation
 * @method     ChildDriverQuery rightJoinWithDriverPin() Adds a RIGHT JOIN clause and with to the query using the DriverPin relation
 * @method     ChildDriverQuery innerJoinWithDriverPin() Adds a INNER JOIN clause and with to the query using the DriverPin relation
 *
 * @method     ChildDriverQuery leftJoinDriverOutput($relationAlias = null) Adds a LEFT JOIN clause to the query using the DriverOutput relation
 * @method     ChildDriverQuery rightJoinDriverOutput($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DriverOutput relation
 * @method     ChildDriverQuery innerJoinDriverOutput($relationAlias = null) Adds a INNER JOIN clause to the query using the DriverOutput relation
 *
 * @method     ChildDriverQuery joinWithDriverOutput($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the DriverOutput relation
 *
 * @method     ChildDriverQuery leftJoinWithDriverOutput() Adds a LEFT JOIN clause and with to the query using the DriverOutput relation
 * @method     ChildDriverQuery rightJoinWithDriverOutput() Adds a RIGHT JOIN clause and with to the query using the DriverOutput relation
 * @method     ChildDriverQuery innerJoinWithDriverOutput() Adds a INNER JOIN clause and with to the query using the DriverOutput relation
 *
 * @method     \ArduinoCoilDriver\Drivers\DriverPinQuery|\ArduinoCoilDriver\Drivers\DriverOutputQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildDriver findOne(ConnectionInterface $con = null) Return the first ChildDriver matching the query
 * @method     ChildDriver findOneOrCreate(ConnectionInterface $con = null) Return the first ChildDriver matching the query, or a new ChildDriver object populated from the query conditions when no match is found
 *
 * @method     ChildDriver findOneById(int $id) Return the first ChildDriver filtered by the id column
 * @method     ChildDriver findOneByName(string $name) Return the first ChildDriver filtered by the name column
 * @method     ChildDriver findOneByMac(string $mac) Return the first ChildDriver filtered by the mac column
 * @method     ChildDriver findOneByIp(string $ip) Return the first ChildDriver filtered by the ip column
 * @method     ChildDriver findOneByAdded(string $added) Return the first ChildDriver filtered by the added column
 * @method     ChildDriver findOneByLastCheckIn(string $last_check_in) Return the first ChildDriver filtered by the last_check_in column
 * @method     ChildDriver findOneByCoilContact(boolean $coil_contact) Return the first ChildDriver filtered by the coil_contact column *

 * @method     ChildDriver requirePk($key, ConnectionInterface $con = null) Return the ChildDriver by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildDriver requireOne(ConnectionInterface $con = null) Return the first ChildDriver matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildDriver requireOneById(int $id) Return the first ChildDriver filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildDriver requireOneByName(string $name) Return the first ChildDriver filtered by the name column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildDriver requireOneByMac(string $mac) Return the first ChildDriver filtered by the mac column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildDriver requireOneByIp(string $ip) Return the first ChildDriver filtered by the ip column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildDriver requireOneByAdded(string $added) Return the first ChildDriver filtered by the added column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildDriver requireOneByLastCheckIn(string $last_check_in) Return the first ChildDriver filtered by the last_check_in column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildDriver requireOneByCoilContact(boolean $coil_contact) Return the first ChildDriver filtered by the coil_contact column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildDriver[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildDriver objects based on current ModelCriteria
 * @method     ChildDriver[]|ObjectCollection findById(int $id) Return ChildDriver objects filtered by the id column
 * @method     ChildDriver[]|ObjectCollection findByName(string $name) Return ChildDriver objects filtered by the name column
 * @method     ChildDriver[]|ObjectCollection findByMac(string $mac) Return ChildDriver objects filtered by the mac column
 * @method     ChildDriver[]|ObjectCollection findByIp(string $ip) Return ChildDriver objects filtered by the ip column
 * @method     ChildDriver[]|ObjectCollection findByAdded(string $added) Return ChildDriver objects filtered by the added column
 * @method     ChildDriver[]|ObjectCollection findByLastCheckIn(string $last_check_in) Return ChildDriver objects filtered by the last_check_in column
 * @method     ChildDriver[]|ObjectCollection findByCoilContact(boolean $coil_contact) Return ChildDriver objects filtered by the coil_contact column
 * @method     ChildDriver[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class DriverQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \ArduinoCoilDriver\Drivers\Base\DriverQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\ArduinoCoilDriver\\Drivers\\Driver', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildDriverQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildDriverQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildDriverQuery) {
            return $criteria;
        }
        $query = new ChildDriverQuery();
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
     * @return ChildDriver|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = DriverTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(DriverTableMap::DATABASE_NAME);
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
     * @return ChildDriver A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, name, mac, ip, added, last_check_in, coil_contact FROM drivers WHERE id = :p0';
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
            /** @var ChildDriver $obj */
            $obj = new ChildDriver();
            $obj->hydrate($row);
            DriverTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildDriver|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildDriverQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(DriverTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildDriverQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(DriverTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildDriverQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(DriverTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(DriverTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DriverTableMap::COL_ID, $id, $comparison);
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
     * @return $this|ChildDriverQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DriverTableMap::COL_NAME, $name, $comparison);
    }

    /**
     * Filter the query on the mac column
     *
     * Example usage:
     * <code>
     * $query->filterByMac('fooValue');   // WHERE mac = 'fooValue'
     * $query->filterByMac('%fooValue%'); // WHERE mac LIKE '%fooValue%'
     * </code>
     *
     * @param     string $mac The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildDriverQuery The current query, for fluid interface
     */
    public function filterByMac($mac = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($mac)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $mac)) {
                $mac = str_replace('*', '%', $mac);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(DriverTableMap::COL_MAC, $mac, $comparison);
    }

    /**
     * Filter the query on the ip column
     *
     * Example usage:
     * <code>
     * $query->filterByIp('fooValue');   // WHERE ip = 'fooValue'
     * $query->filterByIp('%fooValue%'); // WHERE ip LIKE '%fooValue%'
     * </code>
     *
     * @param     string $ip The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildDriverQuery The current query, for fluid interface
     */
    public function filterByIp($ip = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($ip)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $ip)) {
                $ip = str_replace('*', '%', $ip);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(DriverTableMap::COL_IP, $ip, $comparison);
    }

    /**
     * Filter the query on the added column
     *
     * Example usage:
     * <code>
     * $query->filterByAdded('2011-03-14'); // WHERE added = '2011-03-14'
     * $query->filterByAdded('now'); // WHERE added = '2011-03-14'
     * $query->filterByAdded(array('max' => 'yesterday')); // WHERE added > '2011-03-13'
     * </code>
     *
     * @param     mixed $added The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildDriverQuery The current query, for fluid interface
     */
    public function filterByAdded($added = null, $comparison = null)
    {
        if (is_array($added)) {
            $useMinMax = false;
            if (isset($added['min'])) {
                $this->addUsingAlias(DriverTableMap::COL_ADDED, $added['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($added['max'])) {
                $this->addUsingAlias(DriverTableMap::COL_ADDED, $added['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DriverTableMap::COL_ADDED, $added, $comparison);
    }

    /**
     * Filter the query on the last_check_in column
     *
     * Example usage:
     * <code>
     * $query->filterByLastCheckIn('2011-03-14'); // WHERE last_check_in = '2011-03-14'
     * $query->filterByLastCheckIn('now'); // WHERE last_check_in = '2011-03-14'
     * $query->filterByLastCheckIn(array('max' => 'yesterday')); // WHERE last_check_in > '2011-03-13'
     * </code>
     *
     * @param     mixed $lastCheckIn The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildDriverQuery The current query, for fluid interface
     */
    public function filterByLastCheckIn($lastCheckIn = null, $comparison = null)
    {
        if (is_array($lastCheckIn)) {
            $useMinMax = false;
            if (isset($lastCheckIn['min'])) {
                $this->addUsingAlias(DriverTableMap::COL_LAST_CHECK_IN, $lastCheckIn['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($lastCheckIn['max'])) {
                $this->addUsingAlias(DriverTableMap::COL_LAST_CHECK_IN, $lastCheckIn['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DriverTableMap::COL_LAST_CHECK_IN, $lastCheckIn, $comparison);
    }

    /**
     * Filter the query on the coil_contact column
     *
     * Example usage:
     * <code>
     * $query->filterByCoilContact(true); // WHERE coil_contact = true
     * $query->filterByCoilContact('yes'); // WHERE coil_contact = true
     * </code>
     *
     * @param     boolean|string $coilContact The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildDriverQuery The current query, for fluid interface
     */
    public function filterByCoilContact($coilContact = null, $comparison = null)
    {
        if (is_string($coilContact)) {
            $coilContact = in_array(strtolower($coilContact), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(DriverTableMap::COL_COIL_CONTACT, $coilContact, $comparison);
    }

    /**
     * Filter the query by a related \ArduinoCoilDriver\Drivers\DriverPin object
     *
     * @param \ArduinoCoilDriver\Drivers\DriverPin|ObjectCollection $driverPin the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDriverQuery The current query, for fluid interface
     */
    public function filterByDriverPin($driverPin, $comparison = null)
    {
        if ($driverPin instanceof \ArduinoCoilDriver\Drivers\DriverPin) {
            return $this
                ->addUsingAlias(DriverTableMap::COL_ID, $driverPin->getDriverId(), $comparison);
        } elseif ($driverPin instanceof ObjectCollection) {
            return $this
                ->useDriverPinQuery()
                ->filterByPrimaryKeys($driverPin->getPrimaryKeys())
                ->endUse();
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
     * @return $this|ChildDriverQuery The current query, for fluid interface
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
     * Filter the query by a related \ArduinoCoilDriver\Drivers\DriverOutput object
     *
     * @param \ArduinoCoilDriver\Drivers\DriverOutput|ObjectCollection $driverOutput the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDriverQuery The current query, for fluid interface
     */
    public function filterByDriverOutput($driverOutput, $comparison = null)
    {
        if ($driverOutput instanceof \ArduinoCoilDriver\Drivers\DriverOutput) {
            return $this
                ->addUsingAlias(DriverTableMap::COL_ID, $driverOutput->getDriverId(), $comparison);
        } elseif ($driverOutput instanceof ObjectCollection) {
            return $this
                ->useDriverOutputQuery()
                ->filterByPrimaryKeys($driverOutput->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByDriverOutput() only accepts arguments of type \ArduinoCoilDriver\Drivers\DriverOutput or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DriverOutput relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildDriverQuery The current query, for fluid interface
     */
    public function joinDriverOutput($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DriverOutput');

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
            $this->addJoinObject($join, 'DriverOutput');
        }

        return $this;
    }

    /**
     * Use the DriverOutput relation DriverOutput object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \ArduinoCoilDriver\Drivers\DriverOutputQuery A secondary query class using the current class as primary query
     */
    public function useDriverOutputQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDriverOutput($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DriverOutput', '\ArduinoCoilDriver\Drivers\DriverOutputQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildDriver $driver Object to remove from the list of results
     *
     * @return $this|ChildDriverQuery The current query, for fluid interface
     */
    public function prune($driver = null)
    {
        if ($driver) {
            $this->addUsingAlias(DriverTableMap::COL_ID, $driver->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the drivers table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(DriverTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            DriverTableMap::clearInstancePool();
            DriverTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(DriverTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(DriverTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            DriverTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            DriverTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // DriverQuery
