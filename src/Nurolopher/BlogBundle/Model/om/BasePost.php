<?php

namespace Nurolopher\BlogBundle\Model\om;

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
use Nurolopher\BlogBundle\Model\Category;
use Nurolopher\BlogBundle\Model\CategoryQuery;
use Nurolopher\BlogBundle\Model\Comment;
use Nurolopher\BlogBundle\Model\CommentQuery;
use Nurolopher\BlogBundle\Model\Post;
use Nurolopher\BlogBundle\Model\PostCategory;
use Nurolopher\BlogBundle\Model\PostCategoryQuery;
use Nurolopher\BlogBundle\Model\PostPeer;
use Nurolopher\BlogBundle\Model\PostQuery;
use Nurolopher\BlogBundle\Model\User;
use Nurolopher\BlogBundle\Model\UserQuery;

abstract class BasePost extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Nurolopher\\BlogBundle\\Model\\PostPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        PostPeer
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
     * The value for the title field.
     * @var        string
     */
    protected $title;

    /**
     * The value for the body field.
     * @var        string
     */
    protected $body;

    /**
     * The value for the user_id field.
     * @var        int
     */
    protected $user_id;

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
     * @var        User
     */
    protected $aUser;

    /**
     * @var        PropelObjectCollection|PostCategory[] Collection to store aggregation of PostCategory objects.
     */
    protected $collPostCategories;
    protected $collPostCategoriesPartial;

    /**
     * @var        PropelObjectCollection|Comment[] Collection to store aggregation of Comment objects.
     */
    protected $collComments;
    protected $collCommentsPartial;

    /**
     * @var        PropelObjectCollection|Category[] Collection to store aggregation of Category objects.
     */
    protected $collCategories;

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
    protected $categoriesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $postCategoriesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $commentsScheduledForDeletion = null;

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
     * Get the [title] column value.
     *
     * @return string
     */
    public function getTitle()
    {

        return $this->title;
    }

    /**
     * Get the [body] column value.
     *
     * @return string
     */
    public function getBody()
    {

        return $this->body;
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
     * @return Post The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = PostPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [title] column.
     *
     * @param  string $v new value
     * @return Post The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[] = PostPeer::TITLE;
        }


        return $this;
    } // setTitle()

    /**
     * Set the value of [body] column.
     *
     * @param  string $v new value
     * @return Post The current object (for fluent API support)
     */
    public function setBody($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->body !== $v) {
            $this->body = $v;
            $this->modifiedColumns[] = PostPeer::BODY;
        }


        return $this;
    } // setBody()

    /**
     * Set the value of [user_id] column.
     *
     * @param  int $v new value
     * @return Post The current object (for fluent API support)
     */
    public function setUserId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->user_id !== $v) {
            $this->user_id = $v;
            $this->modifiedColumns[] = PostPeer::USER_ID;
        }

        if ($this->aUser !== null && $this->aUser->getId() !== $v) {
            $this->aUser = null;
        }


        return $this;
    } // setUserId()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Post The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            $currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->created_at = $newDateAsString;
                $this->modifiedColumns[] = PostPeer::CREATED_AT;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Post The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            $currentDateAsString = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->updated_at = $newDateAsString;
                $this->modifiedColumns[] = PostPeer::UPDATED_AT;
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
            $this->title = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->body = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->user_id = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
            $this->created_at = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->updated_at = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 6; // 6 = PostPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Post object", $e);
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

        if ($this->aUser !== null && $this->user_id !== $this->aUser->getId()) {
            $this->aUser = null;
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
            $con = Propel::getConnection(PostPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = PostPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aUser = null;
            $this->collPostCategories = null;

            $this->collComments = null;

            $this->collCategories = null;
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
            $con = Propel::getConnection(PostPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = PostQuery::create()
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
            $con = Propel::getConnection(PostPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(PostPeer::CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(PostPeer::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(PostPeer::UPDATED_AT)) {
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
                PostPeer::addInstanceToPool($this);
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

            if ($this->aUser !== null) {
                if ($this->aUser->isModified() || $this->aUser->isNew()) {
                    $affectedRows += $this->aUser->save($con);
                }
                $this->setUser($this->aUser);
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

            if ($this->categoriesScheduledForDeletion !== null) {
                if (!$this->categoriesScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->categoriesScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($pk, $remotePk);
                    }
                    PostCategoryQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->categoriesScheduledForDeletion = null;
                }

                foreach ($this->getCategories() as $category) {
                    if ($category->isModified()) {
                        $category->save($con);
                    }
                }
            } elseif ($this->collCategories) {
                foreach ($this->collCategories as $category) {
                    if ($category->isModified()) {
                        $category->save($con);
                    }
                }
            }

            if ($this->postCategoriesScheduledForDeletion !== null) {
                if (!$this->postCategoriesScheduledForDeletion->isEmpty()) {
                    PostCategoryQuery::create()
                        ->filterByPrimaryKeys($this->postCategoriesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->postCategoriesScheduledForDeletion = null;
                }
            }

            if ($this->collPostCategories !== null) {
                foreach ($this->collPostCategories as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->commentsScheduledForDeletion !== null) {
                if (!$this->commentsScheduledForDeletion->isEmpty()) {
                    foreach ($this->commentsScheduledForDeletion as $comment) {
                        // need to save related object because we set the relation to null
                        $comment->save($con);
                    }
                    $this->commentsScheduledForDeletion = null;
                }
            }

            if ($this->collComments !== null) {
                foreach ($this->collComments as $referrerFK) {
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

        $this->modifiedColumns[] = PostPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . PostPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(PostPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(PostPeer::TITLE)) {
            $modifiedColumns[':p' . $index++]  = '`title`';
        }
        if ($this->isColumnModified(PostPeer::BODY)) {
            $modifiedColumns[':p' . $index++]  = '`body`';
        }
        if ($this->isColumnModified(PostPeer::USER_ID)) {
            $modifiedColumns[':p' . $index++]  = '`user_id`';
        }
        if ($this->isColumnModified(PostPeer::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`created_at`';
        }
        if ($this->isColumnModified(PostPeer::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`updated_at`';
        }

        $sql = sprintf(
            'INSERT INTO `post` (%s) VALUES (%s)',
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
                    case '`title`':
                        $stmt->bindValue($identifier, $this->title, PDO::PARAM_STR);
                        break;
                    case '`body`':
                        $stmt->bindValue($identifier, $this->body, PDO::PARAM_STR);
                        break;
                    case '`user_id`':
                        $stmt->bindValue($identifier, $this->user_id, PDO::PARAM_INT);
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

            if ($this->aUser !== null) {
                if (!$this->aUser->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aUser->getValidationFailures());
                }
            }


            if (($retval = PostPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collPostCategories !== null) {
                    foreach ($this->collPostCategories as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collComments !== null) {
                    foreach ($this->collComments as $referrerFK) {
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
        $pos = PostPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getTitle();
                break;
            case 2:
                return $this->getBody();
                break;
            case 3:
                return $this->getUserId();
                break;
            case 4:
                return $this->getCreatedAt();
                break;
            case 5:
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
        if (isset($alreadyDumpedObjects['Post'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Post'][$this->getPrimaryKey()] = true;
        $keys = PostPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getTitle(),
            $keys[2] => $this->getBody(),
            $keys[3] => $this->getUserId(),
            $keys[4] => $this->getCreatedAt(),
            $keys[5] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aUser) {
                $result['User'] = $this->aUser->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collPostCategories) {
                $result['PostCategories'] = $this->collPostCategories->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collComments) {
                $result['Comments'] = $this->collComments->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = PostPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setTitle($value);
                break;
            case 2:
                $this->setBody($value);
                break;
            case 3:
                $this->setUserId($value);
                break;
            case 4:
                $this->setCreatedAt($value);
                break;
            case 5:
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
        $keys = PostPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setTitle($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setBody($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setUserId($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setCreatedAt($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setUpdatedAt($arr[$keys[5]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(PostPeer::DATABASE_NAME);

        if ($this->isColumnModified(PostPeer::ID)) $criteria->add(PostPeer::ID, $this->id);
        if ($this->isColumnModified(PostPeer::TITLE)) $criteria->add(PostPeer::TITLE, $this->title);
        if ($this->isColumnModified(PostPeer::BODY)) $criteria->add(PostPeer::BODY, $this->body);
        if ($this->isColumnModified(PostPeer::USER_ID)) $criteria->add(PostPeer::USER_ID, $this->user_id);
        if ($this->isColumnModified(PostPeer::CREATED_AT)) $criteria->add(PostPeer::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(PostPeer::UPDATED_AT)) $criteria->add(PostPeer::UPDATED_AT, $this->updated_at);

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
        $criteria = new Criteria(PostPeer::DATABASE_NAME);
        $criteria->add(PostPeer::ID, $this->id);

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
     * @param object $copyObj An object of Post (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setTitle($this->getTitle());
        $copyObj->setBody($this->getBody());
        $copyObj->setUserId($this->getUserId());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getPostCategories() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPostCategory($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getComments() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addComment($relObj->copy($deepCopy));
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
     * @return Post Clone of current object.
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
     * @return PostPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new PostPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a User object.
     *
     * @param                  User $v
     * @return Post The current object (for fluent API support)
     * @throws PropelException
     */
    public function setUser(User $v = null)
    {
        if ($v === null) {
            $this->setUserId(NULL);
        } else {
            $this->setUserId($v->getId());
        }

        $this->aUser = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the User object, it will not be re-added.
        if ($v !== null) {
            $v->addPost($this);
        }


        return $this;
    }


    /**
     * Get the associated User object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return User The associated User object.
     * @throws PropelException
     */
    public function getUser(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aUser === null && ($this->user_id !== null) && $doQuery) {
            $this->aUser = UserQuery::create()->findPk($this->user_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aUser->addPosts($this);
             */
        }

        return $this->aUser;
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
        if ('PostCategory' == $relationName) {
            $this->initPostCategories();
        }
        if ('Comment' == $relationName) {
            $this->initComments();
        }
    }

    /**
     * Clears out the collPostCategories collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Post The current object (for fluent API support)
     * @see        addPostCategories()
     */
    public function clearPostCategories()
    {
        $this->collPostCategories = null; // important to set this to null since that means it is uninitialized
        $this->collPostCategoriesPartial = null;

        return $this;
    }

    /**
     * reset is the collPostCategories collection loaded partially
     *
     * @return void
     */
    public function resetPartialPostCategories($v = true)
    {
        $this->collPostCategoriesPartial = $v;
    }

    /**
     * Initializes the collPostCategories collection.
     *
     * By default this just sets the collPostCategories collection to an empty array (like clearcollPostCategories());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPostCategories($overrideExisting = true)
    {
        if (null !== $this->collPostCategories && !$overrideExisting) {
            return;
        }
        $this->collPostCategories = new PropelObjectCollection();
        $this->collPostCategories->setModel('PostCategory');
    }

    /**
     * Gets an array of PostCategory objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Post is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|PostCategory[] List of PostCategory objects
     * @throws PropelException
     */
    public function getPostCategories($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collPostCategoriesPartial && !$this->isNew();
        if (null === $this->collPostCategories || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collPostCategories) {
                // return empty collection
                $this->initPostCategories();
            } else {
                $collPostCategories = PostCategoryQuery::create(null, $criteria)
                    ->filterByPost($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collPostCategoriesPartial && count($collPostCategories)) {
                      $this->initPostCategories(false);

                      foreach ($collPostCategories as $obj) {
                        if (false == $this->collPostCategories->contains($obj)) {
                          $this->collPostCategories->append($obj);
                        }
                      }

                      $this->collPostCategoriesPartial = true;
                    }

                    $collPostCategories->getInternalIterator()->rewind();

                    return $collPostCategories;
                }

                if ($partial && $this->collPostCategories) {
                    foreach ($this->collPostCategories as $obj) {
                        if ($obj->isNew()) {
                            $collPostCategories[] = $obj;
                        }
                    }
                }

                $this->collPostCategories = $collPostCategories;
                $this->collPostCategoriesPartial = false;
            }
        }

        return $this->collPostCategories;
    }

    /**
     * Sets a collection of PostCategory objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $postCategories A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Post The current object (for fluent API support)
     */
    public function setPostCategories(PropelCollection $postCategories, PropelPDO $con = null)
    {
        $postCategoriesToDelete = $this->getPostCategories(new Criteria(), $con)->diff($postCategories);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->postCategoriesScheduledForDeletion = clone $postCategoriesToDelete;

        foreach ($postCategoriesToDelete as $postCategoryRemoved) {
            $postCategoryRemoved->setPost(null);
        }

        $this->collPostCategories = null;
        foreach ($postCategories as $postCategory) {
            $this->addPostCategory($postCategory);
        }

        $this->collPostCategories = $postCategories;
        $this->collPostCategoriesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related PostCategory objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related PostCategory objects.
     * @throws PropelException
     */
    public function countPostCategories(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collPostCategoriesPartial && !$this->isNew();
        if (null === $this->collPostCategories || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPostCategories) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getPostCategories());
            }
            $query = PostCategoryQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPost($this)
                ->count($con);
        }

        return count($this->collPostCategories);
    }

    /**
     * Method called to associate a PostCategory object to this object
     * through the PostCategory foreign key attribute.
     *
     * @param    PostCategory $l PostCategory
     * @return Post The current object (for fluent API support)
     */
    public function addPostCategory(PostCategory $l)
    {
        if ($this->collPostCategories === null) {
            $this->initPostCategories();
            $this->collPostCategoriesPartial = true;
        }

        if (!in_array($l, $this->collPostCategories->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddPostCategory($l);

            if ($this->postCategoriesScheduledForDeletion and $this->postCategoriesScheduledForDeletion->contains($l)) {
                $this->postCategoriesScheduledForDeletion->remove($this->postCategoriesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	PostCategory $postCategory The postCategory object to add.
     */
    protected function doAddPostCategory($postCategory)
    {
        $this->collPostCategories[]= $postCategory;
        $postCategory->setPost($this);
    }

    /**
     * @param	PostCategory $postCategory The postCategory object to remove.
     * @return Post The current object (for fluent API support)
     */
    public function removePostCategory($postCategory)
    {
        if ($this->getPostCategories()->contains($postCategory)) {
            $this->collPostCategories->remove($this->collPostCategories->search($postCategory));
            if (null === $this->postCategoriesScheduledForDeletion) {
                $this->postCategoriesScheduledForDeletion = clone $this->collPostCategories;
                $this->postCategoriesScheduledForDeletion->clear();
            }
            $this->postCategoriesScheduledForDeletion[]= clone $postCategory;
            $postCategory->setPost(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Post is new, it will return
     * an empty collection; or if this Post has previously
     * been saved, it will retrieve related PostCategories from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Post.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|PostCategory[] List of PostCategory objects
     */
    public function getPostCategoriesJoinCategory($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = PostCategoryQuery::create(null, $criteria);
        $query->joinWith('Category', $join_behavior);

        return $this->getPostCategories($query, $con);
    }

    /**
     * Clears out the collComments collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Post The current object (for fluent API support)
     * @see        addComments()
     */
    public function clearComments()
    {
        $this->collComments = null; // important to set this to null since that means it is uninitialized
        $this->collCommentsPartial = null;

        return $this;
    }

    /**
     * reset is the collComments collection loaded partially
     *
     * @return void
     */
    public function resetPartialComments($v = true)
    {
        $this->collCommentsPartial = $v;
    }

    /**
     * Initializes the collComments collection.
     *
     * By default this just sets the collComments collection to an empty array (like clearcollComments());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initComments($overrideExisting = true)
    {
        if (null !== $this->collComments && !$overrideExisting) {
            return;
        }
        $this->collComments = new PropelObjectCollection();
        $this->collComments->setModel('Comment');
    }

    /**
     * Gets an array of Comment objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Post is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Comment[] List of Comment objects
     * @throws PropelException
     */
    public function getComments($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCommentsPartial && !$this->isNew();
        if (null === $this->collComments || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collComments) {
                // return empty collection
                $this->initComments();
            } else {
                $collComments = CommentQuery::create(null, $criteria)
                    ->filterByPost($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCommentsPartial && count($collComments)) {
                      $this->initComments(false);

                      foreach ($collComments as $obj) {
                        if (false == $this->collComments->contains($obj)) {
                          $this->collComments->append($obj);
                        }
                      }

                      $this->collCommentsPartial = true;
                    }

                    $collComments->getInternalIterator()->rewind();

                    return $collComments;
                }

                if ($partial && $this->collComments) {
                    foreach ($this->collComments as $obj) {
                        if ($obj->isNew()) {
                            $collComments[] = $obj;
                        }
                    }
                }

                $this->collComments = $collComments;
                $this->collCommentsPartial = false;
            }
        }

        return $this->collComments;
    }

    /**
     * Sets a collection of Comment objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $comments A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Post The current object (for fluent API support)
     */
    public function setComments(PropelCollection $comments, PropelPDO $con = null)
    {
        $commentsToDelete = $this->getComments(new Criteria(), $con)->diff($comments);


        $this->commentsScheduledForDeletion = $commentsToDelete;

        foreach ($commentsToDelete as $commentRemoved) {
            $commentRemoved->setPost(null);
        }

        $this->collComments = null;
        foreach ($comments as $comment) {
            $this->addComment($comment);
        }

        $this->collComments = $comments;
        $this->collCommentsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Comment objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Comment objects.
     * @throws PropelException
     */
    public function countComments(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCommentsPartial && !$this->isNew();
        if (null === $this->collComments || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collComments) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getComments());
            }
            $query = CommentQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPost($this)
                ->count($con);
        }

        return count($this->collComments);
    }

    /**
     * Method called to associate a Comment object to this object
     * through the Comment foreign key attribute.
     *
     * @param    Comment $l Comment
     * @return Post The current object (for fluent API support)
     */
    public function addComment(Comment $l)
    {
        if ($this->collComments === null) {
            $this->initComments();
            $this->collCommentsPartial = true;
        }

        if (!in_array($l, $this->collComments->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddComment($l);

            if ($this->commentsScheduledForDeletion and $this->commentsScheduledForDeletion->contains($l)) {
                $this->commentsScheduledForDeletion->remove($this->commentsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	Comment $comment The comment object to add.
     */
    protected function doAddComment($comment)
    {
        $this->collComments[]= $comment;
        $comment->setPost($this);
    }

    /**
     * @param	Comment $comment The comment object to remove.
     * @return Post The current object (for fluent API support)
     */
    public function removeComment($comment)
    {
        if ($this->getComments()->contains($comment)) {
            $this->collComments->remove($this->collComments->search($comment));
            if (null === $this->commentsScheduledForDeletion) {
                $this->commentsScheduledForDeletion = clone $this->collComments;
                $this->commentsScheduledForDeletion->clear();
            }
            $this->commentsScheduledForDeletion[]= $comment;
            $comment->setPost(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Post is new, it will return
     * an empty collection; or if this Post has previously
     * been saved, it will retrieve related Comments from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Post.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Comment[] List of Comment objects
     */
    public function getCommentsJoinUser($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CommentQuery::create(null, $criteria);
        $query->joinWith('User', $join_behavior);

        return $this->getComments($query, $con);
    }

    /**
     * Clears out the collCategories collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Post The current object (for fluent API support)
     * @see        addCategories()
     */
    public function clearCategories()
    {
        $this->collCategories = null; // important to set this to null since that means it is uninitialized
        $this->collCategoriesPartial = null;

        return $this;
    }

    /**
     * Initializes the collCategories collection.
     *
     * By default this just sets the collCategories collection to an empty collection (like clearCategories());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initCategories()
    {
        $this->collCategories = new PropelObjectCollection();
        $this->collCategories->setModel('Category');
    }

    /**
     * Gets a collection of Category objects related by a many-to-many relationship
     * to the current object by way of the post_category cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Post is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|Category[] List of Category objects
     */
    public function getCategories($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collCategories || null !== $criteria) {
            if ($this->isNew() && null === $this->collCategories) {
                // return empty collection
                $this->initCategories();
            } else {
                $collCategories = CategoryQuery::create(null, $criteria)
                    ->filterByPost($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collCategories;
                }
                $this->collCategories = $collCategories;
            }
        }

        return $this->collCategories;
    }

    /**
     * Sets a collection of Category objects related by a many-to-many relationship
     * to the current object by way of the post_category cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $categories A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Post The current object (for fluent API support)
     */
    public function setCategories(PropelCollection $categories, PropelPDO $con = null)
    {
        $this->clearCategories();
        $currentCategories = $this->getCategories(null, $con);

        $this->categoriesScheduledForDeletion = $currentCategories->diff($categories);

        foreach ($categories as $category) {
            if (!$currentCategories->contains($category)) {
                $this->doAddCategory($category);
            }
        }

        $this->collCategories = $categories;

        return $this;
    }

    /**
     * Gets the number of Category objects related by a many-to-many relationship
     * to the current object by way of the post_category cross-reference table.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param boolean $distinct Set to true to force count distinct
     * @param PropelPDO $con Optional connection object
     *
     * @return int the number of related Category objects
     */
    public function countCategories($criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collCategories || null !== $criteria) {
            if ($this->isNew() && null === $this->collCategories) {
                return 0;
            } else {
                $query = CategoryQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByPost($this)
                    ->count($con);
            }
        } else {
            return count($this->collCategories);
        }
    }

    /**
     * Associate a Category object to this object
     * through the post_category cross reference table.
     *
     * @param  Category $category The PostCategory object to relate
     * @return Post The current object (for fluent API support)
     */
    public function addCategory(Category $category)
    {
        if ($this->collCategories === null) {
            $this->initCategories();
        }

        if (!$this->collCategories->contains($category)) { // only add it if the **same** object is not already associated
            $this->doAddCategory($category);
            $this->collCategories[] = $category;

            if ($this->categoriesScheduledForDeletion and $this->categoriesScheduledForDeletion->contains($category)) {
                $this->categoriesScheduledForDeletion->remove($this->categoriesScheduledForDeletion->search($category));
            }
        }

        return $this;
    }

    /**
     * @param	Category $category The category object to add.
     */
    protected function doAddCategory(Category $category)
    {
        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$category->getPosts()->contains($this)) { $postCategory = new PostCategory();
            $postCategory->setCategory($category);
            $this->addPostCategory($postCategory);

            $foreignCollection = $category->getPosts();
            $foreignCollection[] = $this;
        }
    }

    /**
     * Remove a Category object to this object
     * through the post_category cross reference table.
     *
     * @param Category $category The PostCategory object to relate
     * @return Post The current object (for fluent API support)
     */
    public function removeCategory(Category $category)
    {
        if ($this->getCategories()->contains($category)) {
            $this->collCategories->remove($this->collCategories->search($category));
            if (null === $this->categoriesScheduledForDeletion) {
                $this->categoriesScheduledForDeletion = clone $this->collCategories;
                $this->categoriesScheduledForDeletion->clear();
            }
            $this->categoriesScheduledForDeletion[]= $category;
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->title = null;
        $this->body = null;
        $this->user_id = null;
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
            if ($this->collPostCategories) {
                foreach ($this->collPostCategories as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collComments) {
                foreach ($this->collComments as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCategories) {
                foreach ($this->collCategories as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aUser instanceof Persistent) {
              $this->aUser->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collPostCategories instanceof PropelCollection) {
            $this->collPostCategories->clearIterator();
        }
        $this->collPostCategories = null;
        if ($this->collComments instanceof PropelCollection) {
            $this->collComments->clearIterator();
        }
        $this->collComments = null;
        if ($this->collCategories instanceof PropelCollection) {
            $this->collCategories->clearIterator();
        }
        $this->collCategories = null;
        $this->aUser = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(PostPeer::DEFAULT_STRING_FORMAT);
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
     * @return     Post The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[] = PostPeer::UPDATED_AT;

        return $this;
    }

}
