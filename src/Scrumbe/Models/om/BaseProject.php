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
use Scrumbe\Models\LinkProjectUser;
use Scrumbe\Models\LinkProjectUserQuery;
use Scrumbe\Models\Project;
use Scrumbe\Models\ProjectPeer;
use Scrumbe\Models\ProjectQuery;
use Scrumbe\Models\UserStory;
use Scrumbe\Models\UserStoryQuery;

abstract class BaseProject extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Scrumbe\\Models\\ProjectPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        ProjectPeer
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
     * The value for the user_id field.
     * @var        int
     */
    protected $user_id;

    /**
     * The value for the name field.
     * @var        string
     */
    protected $name;

    /**
     * The value for the url_name field.
     * @var        string
     */
    protected $url_name;

    /**
     * The value for the description field.
     * @var        string
     */
    protected $description;

    /**
     * The value for the cover_project field.
     * Note: this column has a database default value of: '/assets/img/back-home.jpg'
     * @var        string
     */
    protected $cover_project;

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
     * @var        PropelObjectCollection|UserStory[] Collection to store aggregation of UserStory objects.
     */
    protected $collUserStories;
    protected $collUserStoriesPartial;

    /**
     * @var        PropelObjectCollection|LinkProjectUser[] Collection to store aggregation of LinkProjectUser objects.
     */
    protected $collLinkProjectUsers;
    protected $collLinkProjectUsersPartial;

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
    protected $userStoriesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $linkProjectUsersScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->cover_project = '/assets/img/back-home.jpg';
    }

    /**
     * Initializes internal state of BaseProject object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

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
     * Get the [user_id] column value.
     *
     * @return int
     */
    public function getUserId()
    {

        return $this->user_id;
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
     * Get the [url_name] column value.
     *
     * @return string
     */
    public function getUrlName()
    {

        return $this->url_name;
    }

    /**
     * Get the [description] column value.
     *
     * @return string
     */
    public function getDescription()
    {

        return $this->description;
    }

    /**
     * Get the [cover_project] column value.
     *
     * @return string
     */
    public function getCoverProject()
    {

        return $this->cover_project;
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
     * @return Project The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = ProjectPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [user_id] column.
     *
     * @param  int $v new value
     * @return Project The current object (for fluent API support)
     */
    public function setUserId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->user_id !== $v) {
            $this->user_id = $v;
            $this->modifiedColumns[] = ProjectPeer::USER_ID;
        }


        return $this;
    } // setUserId()

    /**
     * Set the value of [name] column.
     *
     * @param  string $v new value
     * @return Project The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = ProjectPeer::NAME;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [url_name] column.
     *
     * @param  string $v new value
     * @return Project The current object (for fluent API support)
     */
    public function setUrlName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->url_name !== $v) {
            $this->url_name = $v;
            $this->modifiedColumns[] = ProjectPeer::URL_NAME;
        }


        return $this;
    } // setUrlName()

    /**
     * Set the value of [description] column.
     *
     * @param  string $v new value
     * @return Project The current object (for fluent API support)
     */
    public function setDescription($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = $v;
            $this->modifiedColumns[] = ProjectPeer::DESCRIPTION;
        }


        return $this;
    } // setDescription()

    /**
     * Set the value of [cover_project] column.
     *
     * @param  string $v new value
     * @return Project The current object (for fluent API support)
     */
    public function setCoverProject($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->cover_project !== $v) {
            $this->cover_project = $v;
            $this->modifiedColumns[] = ProjectPeer::COVER_PROJECT;
        }


        return $this;
    } // setCoverProject()

    /**
     * Sets the value of [start_date] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Project The current object (for fluent API support)
     */
    public function setStartDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->start_date !== null || $dt !== null) {
            $currentDateAsString = ($this->start_date !== null && $tmpDt = new DateTime($this->start_date)) ? $tmpDt->format('Y-m-d') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->start_date = $newDateAsString;
                $this->modifiedColumns[] = ProjectPeer::START_DATE;
            }
        } // if either are not null


        return $this;
    } // setStartDate()

    /**
     * Sets the value of [end_date] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Project The current object (for fluent API support)
     */
    public function setEndDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->end_date !== null || $dt !== null) {
            $currentDateAsString = ($this->end_date !== null && $tmpDt = new DateTime($this->end_date)) ? $tmpDt->format('Y-m-d') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->end_date = $newDateAsString;
                $this->modifiedColumns[] = ProjectPeer::END_DATE;
            }
        } // if either are not null


        return $this;
    } // setEndDate()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Project The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            $currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->created_at = $newDateAsString;
                $this->modifiedColumns[] = ProjectPeer::CREATED_AT;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Project The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            $currentDateAsString = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->updated_at = $newDateAsString;
                $this->modifiedColumns[] = ProjectPeer::UPDATED_AT;
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
            if ($this->cover_project !== '/assets/img/back-home.jpg') {
                return false;
            }

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
            $this->user_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->name = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->url_name = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->description = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->cover_project = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->start_date = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->end_date = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->created_at = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->updated_at = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 10; // 10 = ProjectPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Project object", $e);
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
            $con = Propel::getConnection(ProjectPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = ProjectPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collUserStories = null;

            $this->collLinkProjectUsers = null;

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
            $con = Propel::getConnection(ProjectPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ProjectQuery::create()
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
            $con = Propel::getConnection(ProjectPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(ProjectPeer::CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(ProjectPeer::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(ProjectPeer::UPDATED_AT)) {
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
                ProjectPeer::addInstanceToPool($this);
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

            if ($this->userStoriesScheduledForDeletion !== null) {
                if (!$this->userStoriesScheduledForDeletion->isEmpty()) {
                    foreach ($this->userStoriesScheduledForDeletion as $userStory) {
                        // need to save related object because we set the relation to null
                        $userStory->save($con);
                    }
                    $this->userStoriesScheduledForDeletion = null;
                }
            }

            if ($this->collUserStories !== null) {
                foreach ($this->collUserStories as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->linkProjectUsersScheduledForDeletion !== null) {
                if (!$this->linkProjectUsersScheduledForDeletion->isEmpty()) {
                    foreach ($this->linkProjectUsersScheduledForDeletion as $linkProjectUser) {
                        // need to save related object because we set the relation to null
                        $linkProjectUser->save($con);
                    }
                    $this->linkProjectUsersScheduledForDeletion = null;
                }
            }

            if ($this->collLinkProjectUsers !== null) {
                foreach ($this->collLinkProjectUsers as $referrerFK) {
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

        $this->modifiedColumns[] = ProjectPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . ProjectPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ProjectPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(ProjectPeer::USER_ID)) {
            $modifiedColumns[':p' . $index++]  = '`user_id`';
        }
        if ($this->isColumnModified(ProjectPeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = '`name`';
        }
        if ($this->isColumnModified(ProjectPeer::URL_NAME)) {
            $modifiedColumns[':p' . $index++]  = '`url_name`';
        }
        if ($this->isColumnModified(ProjectPeer::DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = '`description`';
        }
        if ($this->isColumnModified(ProjectPeer::COVER_PROJECT)) {
            $modifiedColumns[':p' . $index++]  = '`cover_project`';
        }
        if ($this->isColumnModified(ProjectPeer::START_DATE)) {
            $modifiedColumns[':p' . $index++]  = '`start_date`';
        }
        if ($this->isColumnModified(ProjectPeer::END_DATE)) {
            $modifiedColumns[':p' . $index++]  = '`end_date`';
        }
        if ($this->isColumnModified(ProjectPeer::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`created_at`';
        }
        if ($this->isColumnModified(ProjectPeer::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`updated_at`';
        }

        $sql = sprintf(
            'INSERT INTO `project` (%s) VALUES (%s)',
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
                    case '`user_id`':
                        $stmt->bindValue($identifier, $this->user_id, PDO::PARAM_INT);
                        break;
                    case '`name`':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case '`url_name`':
                        $stmt->bindValue($identifier, $this->url_name, PDO::PARAM_STR);
                        break;
                    case '`description`':
                        $stmt->bindValue($identifier, $this->description, PDO::PARAM_STR);
                        break;
                    case '`cover_project`':
                        $stmt->bindValue($identifier, $this->cover_project, PDO::PARAM_STR);
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


            if (($retval = ProjectPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collUserStories !== null) {
                    foreach ($this->collUserStories as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collLinkProjectUsers !== null) {
                    foreach ($this->collLinkProjectUsers as $referrerFK) {
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
        $pos = ProjectPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getUserId();
                break;
            case 2:
                return $this->getName();
                break;
            case 3:
                return $this->getUrlName();
                break;
            case 4:
                return $this->getDescription();
                break;
            case 5:
                return $this->getCoverProject();
                break;
            case 6:
                return $this->getStartDate();
                break;
            case 7:
                return $this->getEndDate();
                break;
            case 8:
                return $this->getCreatedAt();
                break;
            case 9:
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
        if (isset($alreadyDumpedObjects['Project'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Project'][$this->getPrimaryKey()] = true;
        $keys = ProjectPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getUserId(),
            $keys[2] => $this->getName(),
            $keys[3] => $this->getUrlName(),
            $keys[4] => $this->getDescription(),
            $keys[5] => $this->getCoverProject(),
            $keys[6] => $this->getStartDate(),
            $keys[7] => $this->getEndDate(),
            $keys[8] => $this->getCreatedAt(),
            $keys[9] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collUserStories) {
                $result['UserStories'] = $this->collUserStories->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collLinkProjectUsers) {
                $result['LinkProjectUsers'] = $this->collLinkProjectUsers->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = ProjectPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setUserId($value);
                break;
            case 2:
                $this->setName($value);
                break;
            case 3:
                $this->setUrlName($value);
                break;
            case 4:
                $this->setDescription($value);
                break;
            case 5:
                $this->setCoverProject($value);
                break;
            case 6:
                $this->setStartDate($value);
                break;
            case 7:
                $this->setEndDate($value);
                break;
            case 8:
                $this->setCreatedAt($value);
                break;
            case 9:
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
        $keys = ProjectPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setUserId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setName($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setUrlName($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setDescription($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setCoverProject($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setStartDate($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setEndDate($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setCreatedAt($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setUpdatedAt($arr[$keys[9]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ProjectPeer::DATABASE_NAME);

        if ($this->isColumnModified(ProjectPeer::ID)) $criteria->add(ProjectPeer::ID, $this->id);
        if ($this->isColumnModified(ProjectPeer::USER_ID)) $criteria->add(ProjectPeer::USER_ID, $this->user_id);
        if ($this->isColumnModified(ProjectPeer::NAME)) $criteria->add(ProjectPeer::NAME, $this->name);
        if ($this->isColumnModified(ProjectPeer::URL_NAME)) $criteria->add(ProjectPeer::URL_NAME, $this->url_name);
        if ($this->isColumnModified(ProjectPeer::DESCRIPTION)) $criteria->add(ProjectPeer::DESCRIPTION, $this->description);
        if ($this->isColumnModified(ProjectPeer::COVER_PROJECT)) $criteria->add(ProjectPeer::COVER_PROJECT, $this->cover_project);
        if ($this->isColumnModified(ProjectPeer::START_DATE)) $criteria->add(ProjectPeer::START_DATE, $this->start_date);
        if ($this->isColumnModified(ProjectPeer::END_DATE)) $criteria->add(ProjectPeer::END_DATE, $this->end_date);
        if ($this->isColumnModified(ProjectPeer::CREATED_AT)) $criteria->add(ProjectPeer::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(ProjectPeer::UPDATED_AT)) $criteria->add(ProjectPeer::UPDATED_AT, $this->updated_at);

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
        $criteria = new Criteria(ProjectPeer::DATABASE_NAME);
        $criteria->add(ProjectPeer::ID, $this->id);

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
     * @param object $copyObj An object of Project (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setUserId($this->getUserId());
        $copyObj->setName($this->getName());
        $copyObj->setUrlName($this->getUrlName());
        $copyObj->setDescription($this->getDescription());
        $copyObj->setCoverProject($this->getCoverProject());
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

            foreach ($this->getUserStories() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addUserStory($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getLinkProjectUsers() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addLinkProjectUser($relObj->copy($deepCopy));
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
     * @return Project Clone of current object.
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
     * @return ProjectPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new ProjectPeer();
        }

        return self::$peer;
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
        if ('UserStory' == $relationName) {
            $this->initUserStories();
        }
        if ('LinkProjectUser' == $relationName) {
            $this->initLinkProjectUsers();
        }
    }

    /**
     * Clears out the collUserStories collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Project The current object (for fluent API support)
     * @see        addUserStories()
     */
    public function clearUserStories()
    {
        $this->collUserStories = null; // important to set this to null since that means it is uninitialized
        $this->collUserStoriesPartial = null;

        return $this;
    }

    /**
     * reset is the collUserStories collection loaded partially
     *
     * @return void
     */
    public function resetPartialUserStories($v = true)
    {
        $this->collUserStoriesPartial = $v;
    }

    /**
     * Initializes the collUserStories collection.
     *
     * By default this just sets the collUserStories collection to an empty array (like clearcollUserStories());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initUserStories($overrideExisting = true)
    {
        if (null !== $this->collUserStories && !$overrideExisting) {
            return;
        }
        $this->collUserStories = new PropelObjectCollection();
        $this->collUserStories->setModel('UserStory');
    }

    /**
     * Gets an array of UserStory objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Project is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|UserStory[] List of UserStory objects
     * @throws PropelException
     */
    public function getUserStories($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collUserStoriesPartial && !$this->isNew();
        if (null === $this->collUserStories || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collUserStories) {
                // return empty collection
                $this->initUserStories();
            } else {
                $collUserStories = UserStoryQuery::create(null, $criteria)
                    ->filterByProject($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collUserStoriesPartial && count($collUserStories)) {
                      $this->initUserStories(false);

                      foreach ($collUserStories as $obj) {
                        if (false == $this->collUserStories->contains($obj)) {
                          $this->collUserStories->append($obj);
                        }
                      }

                      $this->collUserStoriesPartial = true;
                    }

                    $collUserStories->getInternalIterator()->rewind();

                    return $collUserStories;
                }

                if ($partial && $this->collUserStories) {
                    foreach ($this->collUserStories as $obj) {
                        if ($obj->isNew()) {
                            $collUserStories[] = $obj;
                        }
                    }
                }

                $this->collUserStories = $collUserStories;
                $this->collUserStoriesPartial = false;
            }
        }

        return $this->collUserStories;
    }

    /**
     * Sets a collection of UserStory objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $userStories A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Project The current object (for fluent API support)
     */
    public function setUserStories(PropelCollection $userStories, PropelPDO $con = null)
    {
        $userStoriesToDelete = $this->getUserStories(new Criteria(), $con)->diff($userStories);


        $this->userStoriesScheduledForDeletion = $userStoriesToDelete;

        foreach ($userStoriesToDelete as $userStoryRemoved) {
            $userStoryRemoved->setProject(null);
        }

        $this->collUserStories = null;
        foreach ($userStories as $userStory) {
            $this->addUserStory($userStory);
        }

        $this->collUserStories = $userStories;
        $this->collUserStoriesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related UserStory objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related UserStory objects.
     * @throws PropelException
     */
    public function countUserStories(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collUserStoriesPartial && !$this->isNew();
        if (null === $this->collUserStories || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collUserStories) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getUserStories());
            }
            $query = UserStoryQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProject($this)
                ->count($con);
        }

        return count($this->collUserStories);
    }

    /**
     * Method called to associate a UserStory object to this object
     * through the UserStory foreign key attribute.
     *
     * @param    UserStory $l UserStory
     * @return Project The current object (for fluent API support)
     */
    public function addUserStory(UserStory $l)
    {
        if ($this->collUserStories === null) {
            $this->initUserStories();
            $this->collUserStoriesPartial = true;
        }

        if (!in_array($l, $this->collUserStories->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddUserStory($l);

            if ($this->userStoriesScheduledForDeletion and $this->userStoriesScheduledForDeletion->contains($l)) {
                $this->userStoriesScheduledForDeletion->remove($this->userStoriesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	UserStory $userStory The userStory object to add.
     */
    protected function doAddUserStory($userStory)
    {
        $this->collUserStories[]= $userStory;
        $userStory->setProject($this);
    }

    /**
     * @param	UserStory $userStory The userStory object to remove.
     * @return Project The current object (for fluent API support)
     */
    public function removeUserStory($userStory)
    {
        if ($this->getUserStories()->contains($userStory)) {
            $this->collUserStories->remove($this->collUserStories->search($userStory));
            if (null === $this->userStoriesScheduledForDeletion) {
                $this->userStoriesScheduledForDeletion = clone $this->collUserStories;
                $this->userStoriesScheduledForDeletion->clear();
            }
            $this->userStoriesScheduledForDeletion[]= $userStory;
            $userStory->setProject(null);
        }

        return $this;
    }

    /**
     * Clears out the collLinkProjectUsers collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Project The current object (for fluent API support)
     * @see        addLinkProjectUsers()
     */
    public function clearLinkProjectUsers()
    {
        $this->collLinkProjectUsers = null; // important to set this to null since that means it is uninitialized
        $this->collLinkProjectUsersPartial = null;

        return $this;
    }

    /**
     * reset is the collLinkProjectUsers collection loaded partially
     *
     * @return void
     */
    public function resetPartialLinkProjectUsers($v = true)
    {
        $this->collLinkProjectUsersPartial = $v;
    }

    /**
     * Initializes the collLinkProjectUsers collection.
     *
     * By default this just sets the collLinkProjectUsers collection to an empty array (like clearcollLinkProjectUsers());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initLinkProjectUsers($overrideExisting = true)
    {
        if (null !== $this->collLinkProjectUsers && !$overrideExisting) {
            return;
        }
        $this->collLinkProjectUsers = new PropelObjectCollection();
        $this->collLinkProjectUsers->setModel('LinkProjectUser');
    }

    /**
     * Gets an array of LinkProjectUser objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Project is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|LinkProjectUser[] List of LinkProjectUser objects
     * @throws PropelException
     */
    public function getLinkProjectUsers($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collLinkProjectUsersPartial && !$this->isNew();
        if (null === $this->collLinkProjectUsers || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collLinkProjectUsers) {
                // return empty collection
                $this->initLinkProjectUsers();
            } else {
                $collLinkProjectUsers = LinkProjectUserQuery::create(null, $criteria)
                    ->filterByProject($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collLinkProjectUsersPartial && count($collLinkProjectUsers)) {
                      $this->initLinkProjectUsers(false);

                      foreach ($collLinkProjectUsers as $obj) {
                        if (false == $this->collLinkProjectUsers->contains($obj)) {
                          $this->collLinkProjectUsers->append($obj);
                        }
                      }

                      $this->collLinkProjectUsersPartial = true;
                    }

                    $collLinkProjectUsers->getInternalIterator()->rewind();

                    return $collLinkProjectUsers;
                }

                if ($partial && $this->collLinkProjectUsers) {
                    foreach ($this->collLinkProjectUsers as $obj) {
                        if ($obj->isNew()) {
                            $collLinkProjectUsers[] = $obj;
                        }
                    }
                }

                $this->collLinkProjectUsers = $collLinkProjectUsers;
                $this->collLinkProjectUsersPartial = false;
            }
        }

        return $this->collLinkProjectUsers;
    }

    /**
     * Sets a collection of LinkProjectUser objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $linkProjectUsers A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Project The current object (for fluent API support)
     */
    public function setLinkProjectUsers(PropelCollection $linkProjectUsers, PropelPDO $con = null)
    {
        $linkProjectUsersToDelete = $this->getLinkProjectUsers(new Criteria(), $con)->diff($linkProjectUsers);


        $this->linkProjectUsersScheduledForDeletion = $linkProjectUsersToDelete;

        foreach ($linkProjectUsersToDelete as $linkProjectUserRemoved) {
            $linkProjectUserRemoved->setProject(null);
        }

        $this->collLinkProjectUsers = null;
        foreach ($linkProjectUsers as $linkProjectUser) {
            $this->addLinkProjectUser($linkProjectUser);
        }

        $this->collLinkProjectUsers = $linkProjectUsers;
        $this->collLinkProjectUsersPartial = false;

        return $this;
    }

    /**
     * Returns the number of related LinkProjectUser objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related LinkProjectUser objects.
     * @throws PropelException
     */
    public function countLinkProjectUsers(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collLinkProjectUsersPartial && !$this->isNew();
        if (null === $this->collLinkProjectUsers || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collLinkProjectUsers) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getLinkProjectUsers());
            }
            $query = LinkProjectUserQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProject($this)
                ->count($con);
        }

        return count($this->collLinkProjectUsers);
    }

    /**
     * Method called to associate a LinkProjectUser object to this object
     * through the LinkProjectUser foreign key attribute.
     *
     * @param    LinkProjectUser $l LinkProjectUser
     * @return Project The current object (for fluent API support)
     */
    public function addLinkProjectUser(LinkProjectUser $l)
    {
        if ($this->collLinkProjectUsers === null) {
            $this->initLinkProjectUsers();
            $this->collLinkProjectUsersPartial = true;
        }

        if (!in_array($l, $this->collLinkProjectUsers->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddLinkProjectUser($l);

            if ($this->linkProjectUsersScheduledForDeletion and $this->linkProjectUsersScheduledForDeletion->contains($l)) {
                $this->linkProjectUsersScheduledForDeletion->remove($this->linkProjectUsersScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	LinkProjectUser $linkProjectUser The linkProjectUser object to add.
     */
    protected function doAddLinkProjectUser($linkProjectUser)
    {
        $this->collLinkProjectUsers[]= $linkProjectUser;
        $linkProjectUser->setProject($this);
    }

    /**
     * @param	LinkProjectUser $linkProjectUser The linkProjectUser object to remove.
     * @return Project The current object (for fluent API support)
     */
    public function removeLinkProjectUser($linkProjectUser)
    {
        if ($this->getLinkProjectUsers()->contains($linkProjectUser)) {
            $this->collLinkProjectUsers->remove($this->collLinkProjectUsers->search($linkProjectUser));
            if (null === $this->linkProjectUsersScheduledForDeletion) {
                $this->linkProjectUsersScheduledForDeletion = clone $this->collLinkProjectUsers;
                $this->linkProjectUsersScheduledForDeletion->clear();
            }
            $this->linkProjectUsersScheduledForDeletion[]= $linkProjectUser;
            $linkProjectUser->setProject(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Project is new, it will return
     * an empty collection; or if this Project has previously
     * been saved, it will retrieve related LinkProjectUsers from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Project.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|LinkProjectUser[] List of LinkProjectUser objects
     */
    public function getLinkProjectUsersJoinUser($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = LinkProjectUserQuery::create(null, $criteria);
        $query->joinWith('User', $join_behavior);

        return $this->getLinkProjectUsers($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->user_id = null;
        $this->name = null;
        $this->url_name = null;
        $this->description = null;
        $this->cover_project = null;
        $this->start_date = null;
        $this->end_date = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
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
            if ($this->collUserStories) {
                foreach ($this->collUserStories as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collLinkProjectUsers) {
                foreach ($this->collLinkProjectUsers as $o) {
                    $o->clearAllReferences($deep);
                }
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collUserStories instanceof PropelCollection) {
            $this->collUserStories->clearIterator();
        }
        $this->collUserStories = null;
        if ($this->collLinkProjectUsers instanceof PropelCollection) {
            $this->collLinkProjectUsers->clearIterator();
        }
        $this->collLinkProjectUsers = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(ProjectPeer::DEFAULT_STRING_FORMAT);
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
     * @return     Project The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[] = ProjectPeer::UPDATED_AT;

        return $this;
    }

}
