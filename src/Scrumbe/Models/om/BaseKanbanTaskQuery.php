<?php

namespace Scrumbe\Models\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Scrumbe\Models\KanbanTask;
use Scrumbe\Models\KanbanTaskPeer;
use Scrumbe\Models\KanbanTaskQuery;
use Scrumbe\Models\Sprint;
use Scrumbe\Models\Task;

/**
 * @method KanbanTaskQuery orderById($order = Criteria::ASC) Order by the id column
 * @method KanbanTaskQuery orderByTaskId($order = Criteria::ASC) Order by the task_id column
 * @method KanbanTaskQuery orderBySprintId($order = Criteria::ASC) Order by the sprint_id column
 * @method KanbanTaskQuery orderByTaskPosition($order = Criteria::ASC) Order by the task_position column
 *
 * @method KanbanTaskQuery groupById() Group by the id column
 * @method KanbanTaskQuery groupByTaskId() Group by the task_id column
 * @method KanbanTaskQuery groupBySprintId() Group by the sprint_id column
 * @method KanbanTaskQuery groupByTaskPosition() Group by the task_position column
 *
 * @method KanbanTaskQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method KanbanTaskQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method KanbanTaskQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method KanbanTaskQuery leftJoinTask($relationAlias = null) Adds a LEFT JOIN clause to the query using the Task relation
 * @method KanbanTaskQuery rightJoinTask($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Task relation
 * @method KanbanTaskQuery innerJoinTask($relationAlias = null) Adds a INNER JOIN clause to the query using the Task relation
 *
 * @method KanbanTaskQuery leftJoinSprint($relationAlias = null) Adds a LEFT JOIN clause to the query using the Sprint relation
 * @method KanbanTaskQuery rightJoinSprint($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Sprint relation
 * @method KanbanTaskQuery innerJoinSprint($relationAlias = null) Adds a INNER JOIN clause to the query using the Sprint relation
 *
 * @method KanbanTask findOne(PropelPDO $con = null) Return the first KanbanTask matching the query
 * @method KanbanTask findOneOrCreate(PropelPDO $con = null) Return the first KanbanTask matching the query, or a new KanbanTask object populated from the query conditions when no match is found
 *
 * @method KanbanTask findOneByTaskId(int $task_id) Return the first KanbanTask filtered by the task_id column
 * @method KanbanTask findOneBySprintId(int $sprint_id) Return the first KanbanTask filtered by the sprint_id column
 * @method KanbanTask findOneByTaskPosition(int $task_position) Return the first KanbanTask filtered by the task_position column
 *
 * @method array findById(int $id) Return KanbanTask objects filtered by the id column
 * @method array findByTaskId(int $task_id) Return KanbanTask objects filtered by the task_id column
 * @method array findBySprintId(int $sprint_id) Return KanbanTask objects filtered by the sprint_id column
 * @method array findByTaskPosition(int $task_position) Return KanbanTask objects filtered by the task_position column
 */
abstract class BaseKanbanTaskQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseKanbanTaskQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = null, $modelName = null, $modelAlias = null)
    {
        if (null === $dbName) {
            $dbName = 'default';
        }
        if (null === $modelName) {
            $modelName = 'Scrumbe\\Models\\KanbanTask';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new KanbanTaskQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   KanbanTaskQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return KanbanTaskQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof KanbanTaskQuery) {
            return $criteria;
        }
        $query = new KanbanTaskQuery(null, null, $modelAlias);

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
     * @param     PropelPDO $con an optional connection object
     *
     * @return   KanbanTask|KanbanTask[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = KanbanTaskPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(KanbanTaskPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * Alias of findPk to use instance pooling
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 KanbanTask A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneById($key, $con = null)
     {
        return $this->findPk($key, $con);
     }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 KanbanTask A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `task_id`, `sprint_id`, `task_position` FROM `kanban_task` WHERE `id` = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new KanbanTask();
            $obj->hydrate($row);
            KanbanTaskPeer::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return KanbanTask|KanbanTask[]|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|KanbanTask[]|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($stmt);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return KanbanTaskQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(KanbanTaskPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return KanbanTaskQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(KanbanTaskPeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id >= 12
     * $query->filterById(array('max' => 12)); // WHERE id <= 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return KanbanTaskQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(KanbanTaskPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(KanbanTaskPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(KanbanTaskPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the task_id column
     *
     * Example usage:
     * <code>
     * $query->filterByTaskId(1234); // WHERE task_id = 1234
     * $query->filterByTaskId(array(12, 34)); // WHERE task_id IN (12, 34)
     * $query->filterByTaskId(array('min' => 12)); // WHERE task_id >= 12
     * $query->filterByTaskId(array('max' => 12)); // WHERE task_id <= 12
     * </code>
     *
     * @see       filterByTask()
     *
     * @param     mixed $taskId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return KanbanTaskQuery The current query, for fluid interface
     */
    public function filterByTaskId($taskId = null, $comparison = null)
    {
        if (is_array($taskId)) {
            $useMinMax = false;
            if (isset($taskId['min'])) {
                $this->addUsingAlias(KanbanTaskPeer::TASK_ID, $taskId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($taskId['max'])) {
                $this->addUsingAlias(KanbanTaskPeer::TASK_ID, $taskId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(KanbanTaskPeer::TASK_ID, $taskId, $comparison);
    }

    /**
     * Filter the query on the sprint_id column
     *
     * Example usage:
     * <code>
     * $query->filterBySprintId(1234); // WHERE sprint_id = 1234
     * $query->filterBySprintId(array(12, 34)); // WHERE sprint_id IN (12, 34)
     * $query->filterBySprintId(array('min' => 12)); // WHERE sprint_id >= 12
     * $query->filterBySprintId(array('max' => 12)); // WHERE sprint_id <= 12
     * </code>
     *
     * @see       filterBySprint()
     *
     * @param     mixed $sprintId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return KanbanTaskQuery The current query, for fluid interface
     */
    public function filterBySprintId($sprintId = null, $comparison = null)
    {
        if (is_array($sprintId)) {
            $useMinMax = false;
            if (isset($sprintId['min'])) {
                $this->addUsingAlias(KanbanTaskPeer::SPRINT_ID, $sprintId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sprintId['max'])) {
                $this->addUsingAlias(KanbanTaskPeer::SPRINT_ID, $sprintId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(KanbanTaskPeer::SPRINT_ID, $sprintId, $comparison);
    }

    /**
     * Filter the query on the task_position column
     *
     * Example usage:
     * <code>
     * $query->filterByTaskPosition(1234); // WHERE task_position = 1234
     * $query->filterByTaskPosition(array(12, 34)); // WHERE task_position IN (12, 34)
     * $query->filterByTaskPosition(array('min' => 12)); // WHERE task_position >= 12
     * $query->filterByTaskPosition(array('max' => 12)); // WHERE task_position <= 12
     * </code>
     *
     * @param     mixed $taskPosition The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return KanbanTaskQuery The current query, for fluid interface
     */
    public function filterByTaskPosition($taskPosition = null, $comparison = null)
    {
        if (is_array($taskPosition)) {
            $useMinMax = false;
            if (isset($taskPosition['min'])) {
                $this->addUsingAlias(KanbanTaskPeer::TASK_POSITION, $taskPosition['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($taskPosition['max'])) {
                $this->addUsingAlias(KanbanTaskPeer::TASK_POSITION, $taskPosition['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(KanbanTaskPeer::TASK_POSITION, $taskPosition, $comparison);
    }

    /**
     * Filter the query by a related Task object
     *
     * @param   Task|PropelObjectCollection $task The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 KanbanTaskQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByTask($task, $comparison = null)
    {
        if ($task instanceof Task) {
            return $this
                ->addUsingAlias(KanbanTaskPeer::TASK_ID, $task->getId(), $comparison);
        } elseif ($task instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(KanbanTaskPeer::TASK_ID, $task->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByTask() only accepts arguments of type Task or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Task relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return KanbanTaskQuery The current query, for fluid interface
     */
    public function joinTask($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Task');

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
            $this->addJoinObject($join, 'Task');
        }

        return $this;
    }

    /**
     * Use the Task relation Task object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Scrumbe\Models\TaskQuery A secondary query class using the current class as primary query
     */
    public function useTaskQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinTask($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Task', '\Scrumbe\Models\TaskQuery');
    }

    /**
     * Filter the query by a related Sprint object
     *
     * @param   Sprint|PropelObjectCollection $sprint The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 KanbanTaskQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterBySprint($sprint, $comparison = null)
    {
        if ($sprint instanceof Sprint) {
            return $this
                ->addUsingAlias(KanbanTaskPeer::SPRINT_ID, $sprint->getId(), $comparison);
        } elseif ($sprint instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(KanbanTaskPeer::SPRINT_ID, $sprint->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterBySprint() only accepts arguments of type Sprint or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Sprint relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return KanbanTaskQuery The current query, for fluid interface
     */
    public function joinSprint($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Sprint');

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
            $this->addJoinObject($join, 'Sprint');
        }

        return $this;
    }

    /**
     * Use the Sprint relation Sprint object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Scrumbe\Models\SprintQuery A secondary query class using the current class as primary query
     */
    public function useSprintQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinSprint($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Sprint', '\Scrumbe\Models\SprintQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   KanbanTask $kanbanTask Object to remove from the list of results
     *
     * @return KanbanTaskQuery The current query, for fluid interface
     */
    public function prune($kanbanTask = null)
    {
        if ($kanbanTask) {
            $this->addUsingAlias(KanbanTaskPeer::ID, $kanbanTask->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
