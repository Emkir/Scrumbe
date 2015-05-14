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
use Scrumbe\Models\LinkUserStorySprint;
use Scrumbe\Models\LinkUserStorySprintPeer;
use Scrumbe\Models\LinkUserStorySprintQuery;
use Scrumbe\Models\Sprint;
use Scrumbe\Models\UserStory;

/**
 * @method LinkUserStorySprintQuery orderById($order = Criteria::ASC) Order by the id column
 * @method LinkUserStorySprintQuery orderByUserStoryId($order = Criteria::ASC) Order by the user_story_id column
 * @method LinkUserStorySprintQuery orderBySprintId($order = Criteria::ASC) Order by the sprint_id column
 * @method LinkUserStorySprintQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method LinkUserStorySprintQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method LinkUserStorySprintQuery groupById() Group by the id column
 * @method LinkUserStorySprintQuery groupByUserStoryId() Group by the user_story_id column
 * @method LinkUserStorySprintQuery groupBySprintId() Group by the sprint_id column
 * @method LinkUserStorySprintQuery groupByCreatedAt() Group by the created_at column
 * @method LinkUserStorySprintQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method LinkUserStorySprintQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method LinkUserStorySprintQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method LinkUserStorySprintQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method LinkUserStorySprintQuery leftJoinUserStory($relationAlias = null) Adds a LEFT JOIN clause to the query using the UserStory relation
 * @method LinkUserStorySprintQuery rightJoinUserStory($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UserStory relation
 * @method LinkUserStorySprintQuery innerJoinUserStory($relationAlias = null) Adds a INNER JOIN clause to the query using the UserStory relation
 *
 * @method LinkUserStorySprintQuery leftJoinSprint($relationAlias = null) Adds a LEFT JOIN clause to the query using the Sprint relation
 * @method LinkUserStorySprintQuery rightJoinSprint($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Sprint relation
 * @method LinkUserStorySprintQuery innerJoinSprint($relationAlias = null) Adds a INNER JOIN clause to the query using the Sprint relation
 *
 * @method LinkUserStorySprint findOne(PropelPDO $con = null) Return the first LinkUserStorySprint matching the query
 * @method LinkUserStorySprint findOneOrCreate(PropelPDO $con = null) Return the first LinkUserStorySprint matching the query, or a new LinkUserStorySprint object populated from the query conditions when no match is found
 *
 * @method LinkUserStorySprint findOneByUserStoryId(int $user_story_id) Return the first LinkUserStorySprint filtered by the user_story_id column
 * @method LinkUserStorySprint findOneBySprintId(int $sprint_id) Return the first LinkUserStorySprint filtered by the sprint_id column
 * @method LinkUserStorySprint findOneByCreatedAt(string $created_at) Return the first LinkUserStorySprint filtered by the created_at column
 * @method LinkUserStorySprint findOneByUpdatedAt(string $updated_at) Return the first LinkUserStorySprint filtered by the updated_at column
 *
 * @method array findById(int $id) Return LinkUserStorySprint objects filtered by the id column
 * @method array findByUserStoryId(int $user_story_id) Return LinkUserStorySprint objects filtered by the user_story_id column
 * @method array findBySprintId(int $sprint_id) Return LinkUserStorySprint objects filtered by the sprint_id column
 * @method array findByCreatedAt(string $created_at) Return LinkUserStorySprint objects filtered by the created_at column
 * @method array findByUpdatedAt(string $updated_at) Return LinkUserStorySprint objects filtered by the updated_at column
 */
abstract class BaseLinkUserStorySprintQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseLinkUserStorySprintQuery object.
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
            $modelName = 'Scrumbe\\Models\\LinkUserStorySprint';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new LinkUserStorySprintQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   LinkUserStorySprintQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return LinkUserStorySprintQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof LinkUserStorySprintQuery) {
            return $criteria;
        }
        $query = new LinkUserStorySprintQuery(null, null, $modelAlias);

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
     * @return   LinkUserStorySprint|LinkUserStorySprint[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = LinkUserStorySprintPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(LinkUserStorySprintPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 LinkUserStorySprint A model object, or null if the key is not found
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
     * @return                 LinkUserStorySprint A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `user_story_id`, `sprint_id`, `created_at`, `updated_at` FROM `link_user_story_sprint` WHERE `id` = :p0';
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
            $obj = new LinkUserStorySprint();
            $obj->hydrate($row);
            LinkUserStorySprintPeer::addInstanceToPool($obj, (string) $key);
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
     * @return LinkUserStorySprint|LinkUserStorySprint[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|LinkUserStorySprint[]|mixed the list of results, formatted by the current formatter
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
     * @return LinkUserStorySprintQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(LinkUserStorySprintPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return LinkUserStorySprintQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(LinkUserStorySprintPeer::ID, $keys, Criteria::IN);
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
     * @return LinkUserStorySprintQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(LinkUserStorySprintPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(LinkUserStorySprintPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LinkUserStorySprintPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the user_story_id column
     *
     * Example usage:
     * <code>
     * $query->filterByUserStoryId(1234); // WHERE user_story_id = 1234
     * $query->filterByUserStoryId(array(12, 34)); // WHERE user_story_id IN (12, 34)
     * $query->filterByUserStoryId(array('min' => 12)); // WHERE user_story_id >= 12
     * $query->filterByUserStoryId(array('max' => 12)); // WHERE user_story_id <= 12
     * </code>
     *
     * @see       filterByUserStory()
     *
     * @param     mixed $userStoryId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LinkUserStorySprintQuery The current query, for fluid interface
     */
    public function filterByUserStoryId($userStoryId = null, $comparison = null)
    {
        if (is_array($userStoryId)) {
            $useMinMax = false;
            if (isset($userStoryId['min'])) {
                $this->addUsingAlias(LinkUserStorySprintPeer::USER_STORY_ID, $userStoryId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($userStoryId['max'])) {
                $this->addUsingAlias(LinkUserStorySprintPeer::USER_STORY_ID, $userStoryId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LinkUserStorySprintPeer::USER_STORY_ID, $userStoryId, $comparison);
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
     * @return LinkUserStorySprintQuery The current query, for fluid interface
     */
    public function filterBySprintId($sprintId = null, $comparison = null)
    {
        if (is_array($sprintId)) {
            $useMinMax = false;
            if (isset($sprintId['min'])) {
                $this->addUsingAlias(LinkUserStorySprintPeer::SPRINT_ID, $sprintId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sprintId['max'])) {
                $this->addUsingAlias(LinkUserStorySprintPeer::SPRINT_ID, $sprintId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LinkUserStorySprintPeer::SPRINT_ID, $sprintId, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at < '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LinkUserStorySprintQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(LinkUserStorySprintPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(LinkUserStorySprintPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LinkUserStorySprintPeer::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at < '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LinkUserStorySprintQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(LinkUserStorySprintPeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(LinkUserStorySprintPeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LinkUserStorySprintPeer::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related UserStory object
     *
     * @param   UserStory|PropelObjectCollection $userStory The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 LinkUserStorySprintQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByUserStory($userStory, $comparison = null)
    {
        if ($userStory instanceof UserStory) {
            return $this
                ->addUsingAlias(LinkUserStorySprintPeer::USER_STORY_ID, $userStory->getId(), $comparison);
        } elseif ($userStory instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(LinkUserStorySprintPeer::USER_STORY_ID, $userStory->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByUserStory() only accepts arguments of type UserStory or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the UserStory relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return LinkUserStorySprintQuery The current query, for fluid interface
     */
    public function joinUserStory($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('UserStory');

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
            $this->addJoinObject($join, 'UserStory');
        }

        return $this;
    }

    /**
     * Use the UserStory relation UserStory object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Scrumbe\Models\UserStoryQuery A secondary query class using the current class as primary query
     */
    public function useUserStoryQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinUserStory($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'UserStory', '\Scrumbe\Models\UserStoryQuery');
    }

    /**
     * Filter the query by a related Sprint object
     *
     * @param   Sprint|PropelObjectCollection $sprint The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 LinkUserStorySprintQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterBySprint($sprint, $comparison = null)
    {
        if ($sprint instanceof Sprint) {
            return $this
                ->addUsingAlias(LinkUserStorySprintPeer::SPRINT_ID, $sprint->getId(), $comparison);
        } elseif ($sprint instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(LinkUserStorySprintPeer::SPRINT_ID, $sprint->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return LinkUserStorySprintQuery The current query, for fluid interface
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
     * @param   LinkUserStorySprint $linkUserStorySprint Object to remove from the list of results
     *
     * @return LinkUserStorySprintQuery The current query, for fluid interface
     */
    public function prune($linkUserStorySprint = null)
    {
        if ($linkUserStorySprint) {
            $this->addUsingAlias(LinkUserStorySprintPeer::ID, $linkUserStorySprint->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     LinkUserStorySprintQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(LinkUserStorySprintPeer::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     LinkUserStorySprintQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(LinkUserStorySprintPeer::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     LinkUserStorySprintQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(LinkUserStorySprintPeer::UPDATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     LinkUserStorySprintQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(LinkUserStorySprintPeer::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date desc
     *
     * @return     LinkUserStorySprintQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(LinkUserStorySprintPeer::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     LinkUserStorySprintQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(LinkUserStorySprintPeer::CREATED_AT);
    }
}
