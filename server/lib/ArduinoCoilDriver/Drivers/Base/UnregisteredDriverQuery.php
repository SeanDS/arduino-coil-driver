<?php

namespace ArduinoCoilDriver\Drivers\Base;

use \Exception;
use \PDO;
use ArduinoCoilDriver\Drivers\UnregisteredDriver as ChildUnregisteredDriver;
use ArduinoCoilDriver\Drivers\UnregisteredDriverQuery as ChildUnregisteredDriverQuery;
use ArduinoCoilDriver\Drivers\Map\UnregisteredDriverTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'drivers_unregistered' table.
 *
 *
 *
 * @method     ChildUnregisteredDriverQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildUnregisteredDriverQuery orderByMac($order = Criteria::ASC) Order by the mac column
 * @method     ChildUnregisteredDriverQuery orderByIp($order = Criteria::ASC) Order by the ip column
 * @method     ChildUnregisteredDriverQuery orderByLastCheckIn($order = Criteria::ASC) Order by the last_check_in column
 *
 * @method     ChildUnregisteredDriverQuery groupById() Group by the id column
 * @method     ChildUnregisteredDriverQuery groupByMac() Group by the mac column
 * @method     ChildUnregisteredDriverQuery groupByIp() Group by the ip column
 * @method     ChildUnregisteredDriverQuery groupByLastCheckIn() Group by the last_check_in column
 *
 * @method     ChildUnregisteredDriverQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildUnregisteredDriverQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildUnregisteredDriverQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildUnregisteredDriverQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildUnregisteredDriverQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildUnregisteredDriverQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildUnregisteredDriver findOne(ConnectionInterface $con = null) Return the first ChildUnregisteredDriver matching the query
 * @method     ChildUnregisteredDriver findOneOrCreate(ConnectionInterface $con = null) Return the first ChildUnregisteredDriver matching the query, or a new ChildUnregisteredDriver object populated from the query conditions when no match is found
 *
 * @method     ChildUnregisteredDriver findOneById(int $id) Return the first ChildUnregisteredDriver filtered by the id column
 * @method     ChildUnregisteredDriver findOneByMac(string $mac) Return the first ChildUnregisteredDriver filtered by the mac column
 * @method     ChildUnregisteredDriver findOneByIp(string $ip) Return the first ChildUnregisteredDriver filtered by the ip column
 * @method     ChildUnregisteredDriver findOneByLastCheckIn(string $last_check_in) Return the first ChildUnregisteredDriver filtered by the last_check_in column *

 * @method     ChildUnregisteredDriver requirePk($key, ConnectionInterface $con = null) Return the ChildUnregisteredDriver by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUnregisteredDriver requireOne(ConnectionInterface $con = null) Return the first ChildUnregisteredDriver matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildUnregisteredDriver requireOneById(int $id) Return the first ChildUnregisteredDriver filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUnregisteredDriver requireOneByMac(string $mac) Return the first ChildUnregisteredDriver filtered by the mac column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUnregisteredDriver requireOneByIp(string $ip) Return the first ChildUnregisteredDriver filtered by the ip column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildUnregisteredDriver requireOneByLastCheckIn(string $last_check_in) Return the first ChildUnregisteredDriver filtered by the last_check_in column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildUnregisteredDriver[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildUnregisteredDriver objects based on current ModelCriteria
 * @method     ChildUnregisteredDriver[]|ObjectCollection findById(int $id) Return ChildUnregisteredDriver objects filtered by the id column
 * @method     ChildUnregisteredDriver[]|ObjectCollection findByMac(string $mac) Return ChildUnregisteredDriver objects filtered by the mac column
 * @method     ChildUnregisteredDriver[]|ObjectCollection findByIp(string $ip) Return ChildUnregisteredDriver objects filtered by the ip column
 * @method     ChildUnregisteredDriver[]|ObjectCollection findByLastCheckIn(string $last_check_in) Return ChildUnregisteredDriver objects filtered by the last_check_in column
 * @method     ChildUnregisteredDriver[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class UnregisteredDriverQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \ArduinoCoilDriver\Drivers\Base\UnregisteredDriverQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\ArduinoCoilDriver\\Drivers\\UnregisteredDriver', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildUnregisteredDriverQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildUnregisteredDriverQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildUnregisteredDriverQuery) {
            return $criteria;
        }
        $query = new ChildUnregisteredDriverQuery();
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
     * @return ChildUnregisteredDriver|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = UnregisteredDriverTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(UnregisteredDriverTableMap::DATABASE_NAME);
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
     * @return ChildUnregisteredDriver A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, mac, ip, last_check_in FROM drivers_unregistered WHERE id = :p0';
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
            /** @var ChildUnregisteredDriver $obj */
            $obj = new ChildUnregisteredDriver();
            $obj->hydrate($row);
            UnregisteredDriverTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildUnregisteredDriver|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildUnregisteredDriverQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(UnregisteredDriverTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildUnregisteredDriverQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(UnregisteredDriverTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildUnregisteredDriverQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(UnregisteredDriverTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(UnregisteredDriverTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UnregisteredDriverTableMap::COL_ID, $id, $comparison);
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
     * @return $this|ChildUnregisteredDriverQuery The current query, for fluid interface
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

        return $this->addUsingAlias(UnregisteredDriverTableMap::COL_MAC, $mac, $comparison);
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
     * @return $this|ChildUnregisteredDriverQuery The current query, for fluid interface
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

        return $this->addUsingAlias(UnregisteredDriverTableMap::COL_IP, $ip, $comparison);
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
     * @return $this|ChildUnregisteredDriverQuery The current query, for fluid interface
     */
    public function filterByLastCheckIn($lastCheckIn = null, $comparison = null)
    {
        if (is_array($lastCheckIn)) {
            $useMinMax = false;
            if (isset($lastCheckIn['min'])) {
                $this->addUsingAlias(UnregisteredDriverTableMap::COL_LAST_CHECK_IN, $lastCheckIn['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($lastCheckIn['max'])) {
                $this->addUsingAlias(UnregisteredDriverTableMap::COL_LAST_CHECK_IN, $lastCheckIn['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UnregisteredDriverTableMap::COL_LAST_CHECK_IN, $lastCheckIn, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildUnregisteredDriver $unregisteredDriver Object to remove from the list of results
     *
     * @return $this|ChildUnregisteredDriverQuery The current query, for fluid interface
     */
    public function prune($unregisteredDriver = null)
    {
        if ($unregisteredDriver) {
            $this->addUsingAlias(UnregisteredDriverTableMap::COL_ID, $unregisteredDriver->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the drivers_unregistered table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(UnregisteredDriverTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            UnregisteredDriverTableMap::clearInstancePool();
            UnregisteredDriverTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(UnregisteredDriverTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(UnregisteredDriverTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            UnregisteredDriverTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            UnregisteredDriverTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // UnregisteredDriverQuery
