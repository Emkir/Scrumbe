<?php

namespace Scrumbe\Models\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \DateTime;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelDateTime;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Scrumbe\Models\KanbanTask;
use Scrumbe\Models\KanbanTaskQuery;
use Scrumbe\Models\LinkUserStorySprint;
use Scrumbe\Models\LinkUserStorySprintQuery;
use Scrumbe\Models\Project;
use Scrumbe\Models\ProjectQuery;
use Scrumbe\Models\Sprint;
use Scrumbe\Models\SprintPeer;
use Scrumbe\Models\SprintQuery;

abstract class BaseSprint extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Scrumbe\\Models\\SprintPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        SprintPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the project_id field.
     * @var        int
     */
    protected $project_id;

    /**
     * The value for the name field.
     * @var        string
     */
    protected $name;

    /**
     * The value for the start_date field.
     * @var        string
     */
    protected $start_date;

    /**
     * The value for the end_date field.
     * @var        string
     */
    protected $end_date;

    /**
     * The value for the created_at field.
     * @var        string
     */
    protected $created_at;

    /**
     * The value for the updated_at field.
     * @var        string
     */
    protected $updated_at;

    /**
     * @var        Project
     */
    protected $aProject;

    /**
     * @var        PropelObjectCollection|LinkUserStorySprint[] Collection to store aggregation of LinkUserStorySprint objects.
     */
    protected $collLinkUserStorySprints;
    protected $collLinkUserStorySprintsPartial;

    /**
     * @var        PropelObjectCollection|KanbanTask[] Collection to store aggregation of KanbanTask objects.
     */
    protected $collKanbanTasks;
    protected $collKanbanTasksPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * Flag to prevent endless clearAllReferences($deep=true) loop, if this object is referenced
     * @var        boolean
     */
    protected $alreadyInClearAllReferencesDeep = false;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $linkUserStorySprintsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $kanbanTasksScheduledForDeletion = null;

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [project_id] column value.
     *
     * @return int
     */
    public function getProjectId()
    {

        return $this->project_id;
    }

    /**
     * Get the [name] column value.
     *
     * @return string
     */
    public function getName()
    {

        return $this->name;
    }

    /**
     * Get the [optionally formatted] temporal [start_date] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null, and 0 if column value is 0000-00-00
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getStartDate($format = null)
    {
        if ($this->start_date === null) {
            return null;
        }

        if ($this->start_date === '0000-00-00') {
            // while technically this is not a default value of null,
            // this seems to be closest in meaning.
            return null;
        }

        try {
            $dt = new DateTime($this->start_date);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->start_date, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Get the [optionally formatted] temporal [end_date] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null, and 0 if column value is 0000-00-00
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getEndDate($format = null)
    {
        if ($this->end_date === null) {
            return null;
        }

        if ($this->end_date === '0000-00-00') {
            // while technically this is not a default value of null,
            // this seems to be closest in meaning.
            return null;
        }

        try {
            $dt = new DateTime($this->end_date);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->end_date, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null, and 0 if column value is 0000-00-00 00:00:00
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = null)
    {
        if ($this->created_at === null) {
            return null;
        }

        if ($this->created_at === '0000-00-00 00:00:00') {
            // while technically this is not a default value of null,
            // this seems to be closest in meaning.
            return null;
        }

        try {
            $dt = new DateTime($this->created_at);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->created_at, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Get the [optionally formatted] temporal [updated_at] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null, and 0 if column value is 0000-00-00 00:00:00
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = null)
    {
        if ($this->updated_at === null) {
            return null;
        }

        if ($this->updated_at === '0000-00-00 00:00:00') {
            // while technically this is not a default value of null,
            // this seems to be closest in meaning.
            return null;
        }

        try {
            $dt = new DateTime($this->updated_at);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->updated_at, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return Sprint The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = SprintPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [project_id] column.
     *
     * @param  int $v new value
     * @return Sprint The current object (for fluent API support)
     */
    public function setProjectId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->project_id !== $v) {
            $this->project_id = $v;
            $this->modifiedColumns[] = SprintPeer::PROJECT_ID;
        }

        if ($this->aProject !== null && $this->aProject->getId() !== $v) {
            $this->aProject = null;
        }


        return $this;
    } // setProjectId()

    /**
     * Set the value of [name] column.
     *
     * @param  string $v new value
     * @return Sprint The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = SprintPeer::NAME;
        }


        return $this;
    } // setName()

    /**
     * Sets the value of [start_date] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Sprint The current object (for fluent API support)
     */
    public function setStartDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->start_date !== null || $dt !== null) {
            $currentDateAsString = ($this->start_date !== null && $tmpDt = new DateTime($this->start_date)) ? $tmpDt->format('Y-m-d') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->start_date = $newDateAsString;
                $this->modifiedColumns[] = SprintPeer::START_DATE;
            }
        } // if either are not null


        return $this;
    } // setStartDate()

    /**
     * Sets the value of [end_date] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Sprint The current object (for fluent API support)
     */
    public function setEndDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->end_date !== null || $dt !== null) {
            $currentDateAsString = ($this->end_date !== null && $tmpDt = new DateTime($this->end_date)) ? $tmpDt->format('Y-m-d') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->end_date = $newDateAsString;
                $this->modifiedColumns[] = SprintPeer::END_DATE;
            }
        } // if either are not null


        return $this;
    } // setEndDate()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Sprint The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            $currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->created_at = $newDateAsString;
                $this->modifiedColumns[] = SprintPeer::CREATED_AT;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Sprint The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            $currentDateAsString = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->updated_at = $newDateAsString;
                $this->modifiedColumns[] = SprintPeer::UPDATED_AT;
            }
        } // if either are not null


        return $this;
    } // setUpdatedAt()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return true
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
     * @param int $startcol 0-based offset column which indicates which resultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        try {

            $this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->project_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->name = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->start_date = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->end_date = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->created_at = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->updated_at = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 7; // 7 = SprintPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Sprint object", $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {

        if ($this->aProject !== null && $this->project_id !== $this->aProject->getId()) {
            $this->aProject = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param boolean $deep (optional) Whether to also de-associated any related objects.
     * @param PropelPDO $con (optional) The PropelPDO connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getConnection(SprintPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = SprintPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aProject = null;
            $this->collLinkUserStorySprints = null;

            $this->collKanbanTasks = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param PropelPDO $con
     * @return void
     * @throws PropelException
     * @throws Exception
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(SprintPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = SprintQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @throws Exception
     * @see        doSave()
     */
    public function save(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(SprintPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(SprintPeer::CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(SprintPeer::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(SprintPeer::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                SprintPeer::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see        save()
     */
    protected function doSave(PropelPDO $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aProject !== null) {
                if ($this->aProject->isModified() || $this->aProject->isNew()) {
                    $affectedRows += $this->aProject->save($con);
                }
                $this->setProject($this->aProject);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->linkUserStorySprintsScheduledForDeletion !== null) {
                if (!$this->linkUserStorySprintsScheduledForDeletion->isEmpty()) {
                    LinkUserStorySprintQuery::create()
                        ->filterByPrimaryKeys($this->linkUserStorySprintsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->linkUserStorySprintsScheduledForDeletion = null;
                }
            }

            if ($this->collLinkUserStorySprints !== null) {
                foreach ($this->collLinkUserStorySprints as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->kanbanTasksScheduledForDeletion !== null) {
                if (!$this->kanbanTasksScheduledForDeletion->isEmpty()) {
                    KanbanTaskQuery::create()
                        ->filterByPrimaryKeys($this->kanbanTasksScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->kanbanTasksScheduledForDeletion = null;
                }
            }

            if ($this->collKanbanTasks !== null) {
                foreach ($this->collKanbanTasks as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param PropelPDO $con
     *
     * @throws PropelException
     * @see        doSave()
     */
    protected function doInsert(PropelPDO $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[] = SprintPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . SprintPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(SprintPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(SprintPeer::PROJECT_ID)) {
            $modifiedColumns[':p' . $index++]  = '`project_id`';
        }
        if ($this->isColumnModified(SprintPeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = '`name`';
        }
        if ($this->isColumnModified(SprintPeer::START_DATE)) {
            $modifiedColumns[':p' . $index++]  = '`start_date`';
        }
        if ($this->isColumnModified(SprintPeer::END_DATE)) {
            $modifiedColumns[':p' . $index++]  = '`end_date`';
        }
        if ($this->isColumnModified(SprintPeer::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`created_at`';
        }
        if ($this->isColumnModified(SprintPeer::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`updated_at`';
        }

        $sql = sprintf(
            'INSERT INTO `sprint` (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '`id`':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case '`project_id`':
                        $stmt->bindValue($identifier, $this->project_id, PDO::PARAM_INT);
                        break;
                    case '`name`':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case '`start_date`':
                        $stmt->bindValue($identifier, $this->start_date, PDO::PARAM_STR);
                        break;
                    case '`end_date`':
                        $stmt->bindValue($identifier, $this->end_date, PDO::PARAM_STR);
                        break;
                    case '`created_at`':
                        $stmt->bindValue($identifier, $this->created_at, PDO::PARAM_STR);
                        break;
                    case '`updated_at`':
                        $stmt->bindValue($identifier, $this->updated_at, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param PropelPDO $con
     *
     * @see        doSave()
     */
    protected function doUpdate(PropelPDO $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();
        BasePeer::doUpdate($selectCriteria, $valuesCriteria, $con);
    }

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param mixed $columns Column name or an array of column names.
     * @return boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();

            return true;
        }

        $this->validationFailures = $res;

        return false;
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggregated array of ValidationFailed objects will be returned.
     *
     * @param array $columns Array of column names to validate.
     * @return mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objects otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            // We call the validate method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aProject !== null) {
                if (!$this->aProject->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aProject->getValidationFailures());
                }
            }


            if (($retval = SprintPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collLinkUserStorySprints !== null) {
                    foreach ($this->collLinkUserStorySprints as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collKanbanTasks !== null) {
                    foreach ($this->collKanbanTasks as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }


            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param string $name name
     * @param string $type The type of fieldname the $name is of:
     *               one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *               BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *               Defaults to BasePeer::TYPE_PHPNAME
     * @return mixed Value of field.
     */
    public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = SprintPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getProjectId();
                break;
            case 2:
                return $this->getName();
                break;
            case 3:
                return $this->getStartDate();
                break;
            case 4:
                return $this->getEndDate();
                break;
            case 5:
                return $this->getCreatedAt();
                break;
            case 6:
                return $this->getUpdatedAt();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     *                    BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                    Defaults to BasePeer::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to true.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['Sprint'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Sprint'][$this->getPrimaryKey()] = true;
        $keys = SprintPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getProjectId(),
            $keys[2] => $this->getName(),
            $keys[3] => $this->getStartDate(),
            $keys[4] => $this->getEndDate(),
            $keys[5] => $this->getCreatedAt(),
            $keys[6] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aProject) {
                $result['Project'] = $this->aProject->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collLinkUserStorySprints) {
                $result['LinkUserStorySprints'] = $this->collLinkUserStorySprints->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collKanbanTasks) {
                $result['KanbanTasks'] = $this->collKanbanTasks->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param string $name peer name
     * @param mixed $value field value
     * @param string $type The type of fieldname the $name is of:
     *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                     Defaults to BasePeer::TYPE_PHPNAME
     * @return void
     */
    public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = SprintPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

        $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @param mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setProjectId($value);
                break;
            case 2:
                $this->setName($value);
                break;
            case 3:
                $this->setStartDate($value);
                break;
            case 4:
                $this->setEndDate($value);
                break;
            case 5:
                $this->setCreatedAt($value);
                break;
            case 6:
                $this->setUpdatedAt($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     * The default key type is the column's BasePeer::TYPE_PHPNAME
     *
     * @param array  $arr     An array to populate the object from.
     * @param string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = SprintPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setProjectId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setName($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setStartDate($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setEndDate($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setCreatedAt($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setUpdatedAt($arr[$keys[6]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(SprintPeer::DATABASE_NAME);

        if ($this->isColumnModified(SprintPeer::ID)) $criteria->add(SprintPeer::ID, $this->id);
        if ($this->isColumnModified(SprintPeer::PROJECT_ID)) $criteria->add(SprintPeer::PROJECT_ID, $this->project_id);
        if ($this->isColumnModified(SprintPeer::NAME)) $criteria->add(SprintPeer::NAME, $this->name);
        if ($this->isColumnModified(SprintPeer::START_DATE)) $criteria->add(SprintPeer::START_DATE, $this->start_date);
        if ($this->isColumnModified(SprintPeer::END_DATE)) $criteria->add(SprintPeer::END_DATE, $this->end_date);
        if ($this->isColumnModified(SprintPeer::CREATED_AT)) $criteria->add(SprintPeer::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(SprintPeer::UPDATED_AT)) $criteria->add(SprintPeer::UPDATED_AT, $this->updated_at);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(SprintPeer::DATABASE_NAME);
        $criteria->add(SprintPeer::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of Sprint (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setProjectId($this->getProjectId());
        $copyObj->setName($this->getName());
        $copyObj->setStartDate($this->getStartDate());
        $copyObj->setEndDate($this->getEndDate());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getLinkUserStorySprints() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addLinkUserStorySprint($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getKanbanTasks() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addKanbanTask($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return Sprint Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return SprintPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new SprintPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Project object.
     *
     * @param                  Project $v
     * @return Sprint The current object (for fluent API support)
     * @throws PropelException
     */
    public function setProject(Project $v = null)
    {
        if ($v === null) {
            $this->setProjectId(NULL);
        } else {
            $this->setProjectId($v->getId());
        }

        $this->aProject = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Project object, it will not be re-added.
        if ($v !== null) {
            $v->addSprint($this);
        }


        return $this;
    }


    /**
     * Get the associated Project object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Project The associated Project object.
     * @throws PropelException
     */
    public function getProject(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aProject === null && ($this->project_id !== null) && $doQuery) {
            $this->aProject = ProjectQuery::create()->findPk($this->project_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aProject->addSprints($this);
             */
        }

        return $this->aProject;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('LinkUserStorySprint' == $relationName) {
            $this->initLinkUserStorySprints();
        }
        if ('KanbanTask' == $relationName) {
            $this->initKanbanTasks();
        }
    }

    /**
     * Clears out the collLinkUserStorySprints collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Sprint The current object (for fluent API support)
     * @see        addLinkUserStorySprints()
     */
    public function clearLinkUserStorySprints()
    {
        $this->collLinkUserStorySprints = null; // important to set this to null since that means it is uninitialized
        $this->collLinkUserStorySprintsPartial = null;

        return $this;
    }

    /**
     * reset is the collLinkUserStorySprints collection loaded partially
     *
     * @return void
     */
    public function resetPartialLinkUserStorySprints($v = true)
    {
        $this->collLinkUserStorySprintsPartial = $v;
    }

    /**
     * Initializes the collLinkUserStorySprints collection.
     *
     * By default this just sets the collLinkUserStorySprints collection to an empty array (like clearcollLinkUserStorySprints());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initLinkUserStorySprints($overrideExisting = true)
    {
        if (null !== $this->collLinkUserStorySprints && !$overrideExisting) {
            return;
        }
        $this->collLinkUserStorySprints = new PropelObjectCollection();
        $this->collLinkUserStorySprints->setModel('LinkUserStorySprint');
    }

    /**
     * Gets an array of LinkUserStorySprint objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Sprint is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|LinkUserStorySprint[] List of LinkUserStorySprint objects
     * @throws PropelException
     */
    public function getLinkUserStorySprints($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collLinkUserStorySprintsPartial && !$this->isNew();
        if (null === $this->collLinkUserStorySprints || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collLinkUserStorySprints) {
                // return empty collection
                $this->initLinkUserStorySprints();
            } else {
                $collLinkUserStorySprints = LinkUserStorySprintQuery::create(null, $criteria)
                    ->filterBySprint($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collLinkUserStorySprintsPartial && count($collLinkUserStorySprints)) {
                      $this->initLinkUserStorySprints(false);

                      foreach ($collLinkUserStorySprints as $obj) {
                        if (false == $this->collLinkUserStorySprints->contains($obj)) {
                          $this->collLinkUserStorySprints->append($obj);
                        }
                      }

                      $this->collLinkUserStorySprintsPartial = true;
                    }

                    $collLinkUserStorySprints->getInternalIterator()->rewind();

                    return $collLinkUserStorySprints;
                }

                if ($partial && $this->collLinkUserStorySprints) {
                    foreach ($this->collLinkUserStorySprints as $obj) {
                        if ($obj->isNew()) {
                            $collLinkUserStorySprints[] = $obj;
                        }
                    }
                }

                $this->collLinkUserStorySprints = $collLinkUserStorySprints;
                $this->collLinkUserStorySprintsPartial = false;
            }
        }

        return $this->collLinkUserStorySprints;
    }

    /**
     * Sets a collection of LinkUserStorySprint objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $linkUserStorySprints A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Sprint The current object (for fluent API support)
     */
    public function setLinkUserStorySprints(PropelCollection $linkUserStorySprints, PropelPDO $con = null)
    {
        $linkUserStorySprintsToDelete = $this->getLinkUserStorySprints(new Criteria(), $con)->diff($linkUserStorySprints);


        $this->linkUserStorySprintsScheduledForDeletion = $linkUserStorySprintsToDelete;

        foreach ($linkUserStorySprintsToDelete as $linkUserStorySprintRemoved) {
            $linkUserStorySprintRemoved->setSprint(null);
        }

        $this->collLinkUserStorySprints = null;
        foreach ($linkUserStorySprints as $linkUserStorySprint) {
            $this->addLinkUserStorySprint($linkUserStorySprint);
        }

        $this->collLinkUserStorySprints = $linkUserStorySprints;
        $this->collLinkUserStorySprintsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related LinkUserStorySprint objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related LinkUserStorySprint objects.
     * @throws PropelException
     */
    public function countLinkUserStorySprints(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collLinkUserStorySprintsPartial && !$this->isNew();
        if (null === $this->collLinkUserStorySprints || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collLinkUserStorySprints) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getLinkUserStorySprints());
            }
            $query = LinkUserStorySprintQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterBySprint($this)
                ->count($con);
        }

        return count($this->collLinkUserStorySprints);
    }

    /**
     * Method called to associate a LinkUserStorySprint object to this object
     * through the LinkUserStorySprint foreign key attribute.
     *
     * @param    LinkUserStorySprint $l LinkUserStorySprint
     * @return Sprint The current object (for fluent API support)
     */
    public function addLinkUserStorySprint(LinkUserStorySprint $l)
    {
        if ($this->collLinkUserStorySprints === null) {
            $this->initLinkUserStorySprints();
            $this->collLinkUserStorySprintsPartial = true;
        }

        if (!in_array($l, $this->collLinkUserStorySprints->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddLinkUserStorySprint($l);

            if ($this->linkUserStorySprintsScheduledForDeletion and $this->linkUserStorySprintsScheduledForDeletion->contains($l)) {
                $this->linkUserStorySprintsScheduledForDeletion->remove($this->linkUserStorySprintsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	LinkUserStorySprint $linkUserStorySprint The linkUserStorySprint object to add.
     */
    protected function doAddLinkUserStorySprint($linkUserStorySprint)
    {
        $this->collLinkUserStorySprints[]= $linkUserStorySprint;
        $linkUserStorySprint->setSprint($this);
    }

    /**
     * @param	LinkUserStorySprint $linkUserStorySprint The linkUserStorySprint object to remove.
     * @return Sprint The current object (for fluent API support)
     */
    public function removeLinkUserStorySprint($linkUserStorySprint)
    {
        if ($this->getLinkUserStorySprints()->contains($linkUserStorySprint)) {
            $this->collLinkUserStorySprints->remove($this->collLinkUserStorySprints->search($linkUserStorySprint));
            if (null === $this->linkUserStorySprintsScheduledForDeletion) {
                $this->linkUserStorySprintsScheduledForDeletion = clone $this->collLinkUserStorySprints;
                $this->linkUserStorySprintsScheduledForDeletion->clear();
            }
            $this->linkUserStorySprintsScheduledForDeletion[]= $linkUserStorySprint;
            $linkUserStorySprint->setSprint(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Sprint is new, it will return
     * an empty collection; or if this Sprint has previously
     * been saved, it will retrieve related LinkUserStorySprints from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Sprint.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|LinkUserStorySprint[] List of LinkUserStorySprint objects
     */
    public function getLinkUserStorySprintsJoinUserStory($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = LinkUserStorySprintQuery::create(null, $criteria);
        $query->joinWith('UserStory', $join_behavior);

        return $this->getLinkUserStorySprints($query, $con);
    }

    /**
     * Clears out the collKanbanTasks collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Sprint The current object (for fluent API support)
     * @see        addKanbanTasks()
     */
    public function clearKanbanTasks()
    {
        $this->collKanbanTasks = null; // important to set this to null since that means it is uninitialized
        $this->collKanbanTasksPartial = null;

        return $this;
    }

    /**
     * reset is the collKanbanTasks collection loaded partially
     *
     * @return void
     */
    public function resetPartialKanbanTasks($v = true)
    {
        $this->collKanbanTasksPartial = $v;
    }

    /**
     * Initializes the collKanbanTasks collection.
     *
     * By default this just sets the collKanbanTasks collection to an empty array (like clearcollKanbanTasks());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initKanbanTasks($overrideExisting = true)
    {
        if (null !== $this->collKanbanTasks && !$overrideExisting) {
            return;
        }
        $this->collKanbanTasks = new PropelObjectCollection();
        $this->collKanbanTasks->setModel('KanbanTask');
    }

    /**
     * Gets an array of KanbanTask objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Sprint is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|KanbanTask[] List of KanbanTask objects
     * @throws PropelException
     */
    public function getKanbanTasks($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collKanbanTasksPartial && !$this->isNew();
        if (null === $this->collKanbanTasks || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collKanbanTasks) {
                // return empty collection
                $this->initKanbanTasks();
            } else {
                $collKanbanTasks = KanbanTaskQuery::create(null, $criteria)
                    ->filterBySprint($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collKanbanTasksPartial && count($collKanbanTasks)) {
                      $this->initKanbanTasks(false);

                      foreach ($collKanbanTasks as $obj) {
                        if (false == $this->collKanbanTasks->contains($obj)) {
                          $this->collKanbanTasks->append($obj);
                        }
                      }

                      $this->collKanbanTasksPartial = true;
                    }

                    $collKanbanTasks->getInternalIterator()->rewind();

                    return $collKanbanTasks;
                }

                if ($partial && $this->collKanbanTasks) {
                    foreach ($this->collKanbanTasks as $obj) {
                        if ($obj->isNew()) {
                            $collKanbanTasks[] = $obj;
                        }
                    }
                }

                $this->collKanbanTasks = $collKanbanTasks;
                $this->collKanbanTasksPartial = false;
            }
        }

        return $this->collKanbanTasks;
    }

    /**
     * Sets a collection of KanbanTask objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $kanbanTasks A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Sprint The current object (for fluent API support)
     */
    public function setKanbanTasks(PropelCollection $kanbanTasks, PropelPDO $con = null)
    {
        $kanbanTasksToDelete = $this->getKanbanTasks(new Criteria(), $con)->diff($kanbanTasks);


        $this->kanbanTasksScheduledForDeletion = $kanbanTasksToDelete;

        foreach ($kanbanTasksToDelete as $kanbanTaskRemoved) {
            $kanbanTaskRemoved->setSprint(null);
        }

        $this->collKanbanTasks = null;
        foreach ($kanbanTasks as $kanbanTask) {
            $this->addKanbanTask($kanbanTask);
        }

        $this->collKanbanTasks = $kanbanTasks;
        $this->collKanbanTasksPartial = false;

        return $this;
    }

    /**
     * Returns the number of related KanbanTask objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related KanbanTask objects.
     * @throws PropelException
     */
    public function countKanbanTasks(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collKanbanTasksPartial && !$this->isNew();
        if (null === $this->collKanbanTasks || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collKanbanTasks) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getKanbanTasks());
            }
            $query = KanbanTaskQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterBySprint($this)
                ->count($con);
        }

        return count($this->collKanbanTasks);
    }

    /**
     * Method called to associate a KanbanTask object to this object
     * through the KanbanTask foreign key attribute.
     *
     * @param    KanbanTask $l KanbanTask
     * @return Sprint The current object (for fluent API support)
     */
    public function addKanbanTask(KanbanTask $l)
    {
        if ($this->collKanbanTasks === null) {
            $this->initKanbanTasks();
            $this->collKanbanTasksPartial = true;
        }

        if (!in_array($l, $this->collKanbanTasks->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddKanbanTask($l);

            if ($this->kanbanTasksScheduledForDeletion and $this->kanbanTasksScheduledForDeletion->contains($l)) {
                $this->kanbanTasksScheduledForDeletion->remove($this->kanbanTasksScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	KanbanTask $kanbanTask The kanbanTask object to add.
     */
    protected function doAddKanbanTask($kanbanTask)
    {
        $this->collKanbanTasks[]= $kanbanTask;
        $kanbanTask->setSprint($this);
    }

    /**
     * @param	KanbanTask $kanbanTask The kanbanTask object to remove.
     * @return Sprint The current object (for fluent API support)
     */
    public function removeKanbanTask($kanbanTask)
    {
        if ($this->getKanbanTasks()->contains($kanbanTask)) {
            $this->collKanbanTasks->remove($this->collKanbanTasks->search($kanbanTask));
            if (null === $this->kanbanTasksScheduledForDeletion) {
                $this->kanbanTasksScheduledForDeletion = clone $this->collKanbanTasks;
                $this->kanbanTasksScheduledForDeletion->clear();
            }
            $this->kanbanTasksScheduledForDeletion[]= $kanbanTask;
            $kanbanTask->setSprint(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Sprint is new, it will return
     * an empty collection; or if this Sprint has previously
     * been saved, it will retrieve related KanbanTasks from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Sprint.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|KanbanTask[] List of KanbanTask objects
     */
    public function getKanbanTasksJoinTask($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = KanbanTaskQuery::create(null, $criteria);
        $query->joinWith('Task', $join_behavior);

        return $this->getKanbanTasks($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->project_id = null;
        $this->name = null;
        $this->start_date = null;
        $this->end_date = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep && !$this->alreadyInClearAllReferencesDeep) {
            $this->alreadyInClearAllReferencesDeep = true;
            if ($this->collLinkUserStorySprints) {
                foreach ($this->collLinkUserStorySprints as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collKanbanTasks) {
                foreach ($this->collKanbanTasks as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aProject instanceof Persistent) {
              $this->aProject->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collLinkUserStorySprints instanceof PropelCollection) {
            $this->collLinkUserStorySprints->clearIterator();
        }
        $this->collLinkUserStorySprints = null;
        if ($this->collKanbanTasks instanceof PropelCollection) {
            $this->collKanbanTasks->clearIterator();
        }
        $this->collKanbanTasks = null;
        $this->aProject = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(SprintPeer::DEFAULT_STRING_FORMAT);
    }

    /**
     * return true is the object is in saving state
     *
     * @return boolean
     */
    public function isAlreadyInSave()
    {
        return $this->alreadyInSave;
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     Sprint The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[] = SprintPeer::UPDATED_AT;

        return $this;
    }

}
