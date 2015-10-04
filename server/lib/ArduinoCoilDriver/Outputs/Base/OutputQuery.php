<?php

namespace ArduinoCoilDriver\Outputs\Base;

use \Exception;
use \PDO;
use ArduinoCoilDriver\Outputs\Output as ChildOutput;
use ArduinoCoilDriver\Outputs\OutputQuery as ChildOutputQuery;
use ArduinoCoilDriver\Outputs\Map\OutputTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'outputs' table.
 *
 *
 *
 * @method     ChildOutputQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildOutputQuery orderByName($order = Criteria::ASC) Order by the name column
 *
 * @method     ChildOutputQuery groupById() Group by the id column
 * @method     ChildOutputQuery groupByName() Group by the name column
 *
 * @method     ChildOutputQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildOutputQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildOutputQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildOutputQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildOutputQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildOutputQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildOutputQuery leftJoinOutputPin($relationAlias = null) Adds a LEFT JOIN clause to the query using the OutputPin relation
 * @method     ChildOutputQuery rightJoinOutputPin($relationAlias = null) Adds a RIGHT JOIN clause to the query using the OutputPin relation
 * @method     ChildOutputQuery innerJoinOutputPin($relationAlias = null) Adds a INNER JOIN clause to the query using the OutputPin relation
 *
 * @method     ChildOutputQuery joinWithOutputPin($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the OutputPin relation
 *
 * @method     ChildOutputQuery leftJoinWithOutputPin() Adds a LEFT JOIN clause and with to the query using the OutputPin relation
 * @method     ChildOutputQuery rightJoinWithOutputPin() Adds a RIGHT JOIN clause and with to the query using the OutputPin relation
 * @method     ChildOutputQuery innerJoinWithOutputPin() Adds a INNER JOIN clause and with to the query using the OutputPin relation
 *
 * @method     \ArduinoCoilDriver\Outputs\OutputPinQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildOutput findOne(ConnectionInterface $con = null) Return the first ChildOutput matching the query
 * @method     ChildOutput findOneOrCreate(ConnectionInterface $con = null) Return the first ChildOutput matching the query, or a new ChildOutput object populated from the query conditions when no match is found
 *
 * @method     ChildOutput findOneById(int $id) Return the first ChildOutput filtered by the id column
 * @method     ChildOutput findOneByName(string $name) Return the first ChildOutput filtered by the name column *

 * @method     ChildOutput requirePk($key, ConnectionInterface $con = null) Return the ChildOutput by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildOutput requireOne(ConnectionInterface $con = null) Return the first ChildOutput matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildOutput requireOneById(int $id) Return the first ChildOutput filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildOutput requireOneByName(string $name) Return the first ChildOutput filtered by the name column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildOutput[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildOutput objects based on current ModelCriteria
 * @method     ChildOutput[]|ObjectCollection findById(int $id) Return ChildOutput objects filtered by the id column
 * @method     ChildOutput[]|ObjectCollection findByName(string $name) Return ChildOutput objects filtered by the name column
 * @method     ChildOutput[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class OutputQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \ArduinoCoilDriver\Outputs\Base\OutputQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\ArduinoCoilDriver\\Outputs\\Output', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildOutputQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildOutputQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildOutputQuery) {
            return $criteria;
        }
        $query = new ChildOutputQuery();
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
     * @return ChildOutput|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = OutputTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(OutputTableMap::DATABASE_NAME);
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
     * @return ChildOutput A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, name FROM outputs WHERE id = :p0';
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
            /** @var ChildOutput $obj */
            $obj = new ChildOutput();
            $obj->hydrate($row);
            OutputTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildOutput|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildOutputQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(OutputTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildOutputQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(OutputTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildOutputQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(OutputTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(OutputTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OutputTableMap::COL_ID, $id, $comparison);
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
     * @return $this|ChildOutputQuery The current query, for fluid interface
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

        return $this->addUsingAlias(OutputTableMap::COL_NAME, $name, $comparison);
    }

    /**
     * Filter the query by a related \ArduinoCoilDriver\Outputs\OutputPin object
     *
     * @param \ArduinoCoilDriver\Outputs\OutputPin|ObjectCollection $outputPin the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOutputQuery The current query, for fluid interface
     */
    public function filterByOutputPin($outputPin, $comparison = null)
    {
        if ($outputPin instanceof \ArduinoCoilDriver\Outputs\OutputPin) {
            return $this
                ->addUsingAlias(OutputTableMap::COL_ID, $outputPin->getOutputId(), $comparison);
        } elseif ($outputPin instanceof ObjectCollection) {
            return $this
                ->useOutputPinQuery()
                ->filterByPrimaryKeys($outputPin->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByOutputPin() only accepts arguments of type \ArduinoCoilDriver\Outputs\OutputPin or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the OutputPin relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildOutputQuery The current query, for fluid interface
     */
    public function joinOutputPin($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('OutputPin');

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
            $this->addJoinObject($join, 'OutputPin');
        }

        return $this;
    }

    /**
     * Use the OutputPin relation OutputPin object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \ArduinoCoilDriver\Outputs\OutputPinQuery A secondary query class using the current class as primary query
     */
    public function useOutputPinQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOutputPin($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'OutputPin', '\ArduinoCoilDriver\Outputs\OutputPinQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildOutput $output Object to remove from the list of results
     *
     * @return $this|ChildOutputQuery The current query, for fluid interface
     */
    public function prune($output = null)
    {
        if ($output) {
            $this->addUsingAlias(OutputTableMap::COL_ID, $output->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the outputs table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(OutputTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            OutputTableMap::clearInstancePool();
            OutputTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(OutputTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(OutputTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            OutputTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            OutputTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // OutputQuery
