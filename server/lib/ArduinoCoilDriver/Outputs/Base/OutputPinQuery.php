<?php

namespace ArduinoCoilDriver\Outputs\Base;

use \Exception;
use \PDO;
use ArduinoCoilDriver\Drivers\DriverPin;
use ArduinoCoilDriver\Outputs\OutputPin as ChildOutputPin;
use ArduinoCoilDriver\Outputs\OutputPinQuery as ChildOutputPinQuery;
use ArduinoCoilDriver\Outputs\Map\OutputPinTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'output_pins' table.
 *
 *
 *
 * @method     ChildOutputPinQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildOutputPinQuery orderByOutputId($order = Criteria::ASC) Order by the output_id column
 * @method     ChildOutputPinQuery orderByDriverPinId($order = Criteria::ASC) Order by the driver_pin_id column
 *
 * @method     ChildOutputPinQuery groupById() Group by the id column
 * @method     ChildOutputPinQuery groupByOutputId() Group by the output_id column
 * @method     ChildOutputPinQuery groupByDriverPinId() Group by the driver_pin_id column
 *
 * @method     ChildOutputPinQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildOutputPinQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildOutputPinQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildOutputPinQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildOutputPinQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildOutputPinQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildOutputPinQuery leftJoinOutput($relationAlias = null) Adds a LEFT JOIN clause to the query using the Output relation
 * @method     ChildOutputPinQuery rightJoinOutput($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Output relation
 * @method     ChildOutputPinQuery innerJoinOutput($relationAlias = null) Adds a INNER JOIN clause to the query using the Output relation
 *
 * @method     ChildOutputPinQuery joinWithOutput($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Output relation
 *
 * @method     ChildOutputPinQuery leftJoinWithOutput() Adds a LEFT JOIN clause and with to the query using the Output relation
 * @method     ChildOutputPinQuery rightJoinWithOutput() Adds a RIGHT JOIN clause and with to the query using the Output relation
 * @method     ChildOutputPinQuery innerJoinWithOutput() Adds a INNER JOIN clause and with to the query using the Output relation
 *
 * @method     ChildOutputPinQuery leftJoinDriverPin($relationAlias = null) Adds a LEFT JOIN clause to the query using the DriverPin relation
 * @method     ChildOutputPinQuery rightJoinDriverPin($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DriverPin relation
 * @method     ChildOutputPinQuery innerJoinDriverPin($relationAlias = null) Adds a INNER JOIN clause to the query using the DriverPin relation
 *
 * @method     ChildOutputPinQuery joinWithDriverPin($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the DriverPin relation
 *
 * @method     ChildOutputPinQuery leftJoinWithDriverPin() Adds a LEFT JOIN clause and with to the query using the DriverPin relation
 * @method     ChildOutputPinQuery rightJoinWithDriverPin() Adds a RIGHT JOIN clause and with to the query using the DriverPin relation
 * @method     ChildOutputPinQuery innerJoinWithDriverPin() Adds a INNER JOIN clause and with to the query using the DriverPin relation
 *
 * @method     \ArduinoCoilDriver\Outputs\OutputQuery|\ArduinoCoilDriver\Drivers\DriverPinQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildOutputPin findOne(ConnectionInterface $con = null) Return the first ChildOutputPin matching the query
 * @method     ChildOutputPin findOneOrCreate(ConnectionInterface $con = null) Return the first ChildOutputPin matching the query, or a new ChildOutputPin object populated from the query conditions when no match is found
 *
 * @method     ChildOutputPin findOneById(int $id) Return the first ChildOutputPin filtered by the id column
 * @method     ChildOutputPin findOneByOutputId(int $output_id) Return the first ChildOutputPin filtered by the output_id column
 * @method     ChildOutputPin findOneByDriverPinId(int $driver_pin_id) Return the first ChildOutputPin filtered by the driver_pin_id column *

 * @method     ChildOutputPin requirePk($key, ConnectionInterface $con = null) Return the ChildOutputPin by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildOutputPin requireOne(ConnectionInterface $con = null) Return the first ChildOutputPin matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildOutputPin requireOneById(int $id) Return the first ChildOutputPin filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildOutputPin requireOneByOutputId(int $output_id) Return the first ChildOutputPin filtered by the output_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildOutputPin requireOneByDriverPinId(int $driver_pin_id) Return the first ChildOutputPin filtered by the driver_pin_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildOutputPin[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildOutputPin objects based on current ModelCriteria
 * @method     ChildOutputPin[]|ObjectCollection findById(int $id) Return ChildOutputPin objects filtered by the id column
 * @method     ChildOutputPin[]|ObjectCollection findByOutputId(int $output_id) Return ChildOutputPin objects filtered by the output_id column
 * @method     ChildOutputPin[]|ObjectCollection findByDriverPinId(int $driver_pin_id) Return ChildOutputPin objects filtered by the driver_pin_id column
 * @method     ChildOutputPin[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class OutputPinQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \ArduinoCoilDriver\Outputs\Base\OutputPinQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\ArduinoCoilDriver\\Outputs\\OutputPin', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildOutputPinQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildOutputPinQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildOutputPinQuery) {
            return $criteria;
        }
        $query = new ChildOutputPinQuery();
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
     * @return ChildOutputPin|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = OutputPinTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(OutputPinTableMap::DATABASE_NAME);
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
     * @return ChildOutputPin A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, output_id, driver_pin_id FROM output_pins WHERE id = :p0';
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
            /** @var ChildOutputPin $obj */
            $obj = new ChildOutputPin();
            $obj->hydrate($row);
            OutputPinTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildOutputPin|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildOutputPinQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(OutputPinTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildOutputPinQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(OutputPinTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildOutputPinQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(OutputPinTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(OutputPinTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OutputPinTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the output_id column
     *
     * Example usage:
     * <code>
     * $query->filterByOutputId(1234); // WHERE output_id = 1234
     * $query->filterByOutputId(array(12, 34)); // WHERE output_id IN (12, 34)
     * $query->filterByOutputId(array('min' => 12)); // WHERE output_id > 12
     * </code>
     *
     * @see       filterByOutput()
     *
     * @param     mixed $outputId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildOutputPinQuery The current query, for fluid interface
     */
    public function filterByOutputId($outputId = null, $comparison = null)
    {
        if (is_array($outputId)) {
            $useMinMax = false;
            if (isset($outputId['min'])) {
                $this->addUsingAlias(OutputPinTableMap::COL_OUTPUT_ID, $outputId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($outputId['max'])) {
                $this->addUsingAlias(OutputPinTableMap::COL_OUTPUT_ID, $outputId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OutputPinTableMap::COL_OUTPUT_ID, $outputId, $comparison);
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
     * @return $this|ChildOutputPinQuery The current query, for fluid interface
     */
    public function filterByDriverPinId($driverPinId = null, $comparison = null)
    {
        if (is_array($driverPinId)) {
            $useMinMax = false;
            if (isset($driverPinId['min'])) {
                $this->addUsingAlias(OutputPinTableMap::COL_DRIVER_PIN_ID, $driverPinId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($driverPinId['max'])) {
                $this->addUsingAlias(OutputPinTableMap::COL_DRIVER_PIN_ID, $driverPinId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OutputPinTableMap::COL_DRIVER_PIN_ID, $driverPinId, $comparison);
    }

    /**
     * Filter the query by a related \ArduinoCoilDriver\Outputs\Output object
     *
     * @param \ArduinoCoilDriver\Outputs\Output|ObjectCollection $output The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildOutputPinQuery The current query, for fluid interface
     */
    public function filterByOutput($output, $comparison = null)
    {
        if ($output instanceof \ArduinoCoilDriver\Outputs\Output) {
            return $this
                ->addUsingAlias(OutputPinTableMap::COL_OUTPUT_ID, $output->getId(), $comparison);
        } elseif ($output instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(OutputPinTableMap::COL_OUTPUT_ID, $output->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByOutput() only accepts arguments of type \ArduinoCoilDriver\Outputs\Output or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Output relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildOutputPinQuery The current query, for fluid interface
     */
    public function joinOutput($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Output');

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
            $this->addJoinObject($join, 'Output');
        }

        return $this;
    }

    /**
     * Use the Output relation Output object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \ArduinoCoilDriver\Outputs\OutputQuery A secondary query class using the current class as primary query
     */
    public function useOutputQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOutput($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Output', '\ArduinoCoilDriver\Outputs\OutputQuery');
    }

    /**
     * Filter the query by a related \ArduinoCoilDriver\Drivers\DriverPin object
     *
     * @param \ArduinoCoilDriver\Drivers\DriverPin|ObjectCollection $driverPin The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildOutputPinQuery The current query, for fluid interface
     */
    public function filterByDriverPin($driverPin, $comparison = null)
    {
        if ($driverPin instanceof \ArduinoCoilDriver\Drivers\DriverPin) {
            return $this
                ->addUsingAlias(OutputPinTableMap::COL_DRIVER_PIN_ID, $driverPin->getId(), $comparison);
        } elseif ($driverPin instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(OutputPinTableMap::COL_DRIVER_PIN_ID, $driverPin->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return $this|ChildOutputPinQuery The current query, for fluid interface
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
     * Exclude object from result
     *
     * @param   ChildOutputPin $outputPin Object to remove from the list of results
     *
     * @return $this|ChildOutputPinQuery The current query, for fluid interface
     */
    public function prune($outputPin = null)
    {
        if ($outputPin) {
            $this->addUsingAlias(OutputPinTableMap::COL_ID, $outputPin->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the output_pins table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(OutputPinTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            OutputPinTableMap::clearInstancePool();
            OutputPinTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(OutputPinTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(OutputPinTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            OutputPinTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            OutputPinTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // OutputPinQuery
