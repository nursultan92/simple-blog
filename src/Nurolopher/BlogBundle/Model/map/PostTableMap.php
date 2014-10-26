<?php

namespace Nurolopher\BlogBundle\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'post' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.src.Nurolopher.BlogBundle.Model.map
 */
class PostTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Nurolopher.BlogBundle.Model.map.PostTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('post');
        $this->setPhpName('Post');
        $this->setClassname('Nurolopher\\BlogBundle\\Model\\Post');
        $this->setPackage('src.Nurolopher.BlogBundle.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('title', 'Title', 'VARCHAR', true, 255, null);
        $this->addColumn('body', 'Body', 'LONGVARCHAR', true, null, null);
        $this->addForeignKey('user_id', 'UserId', 'INTEGER', 'user', 'id', true, null, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
        // validators
        $this->addValidator('title', 'required', 'propel.validator.RequiredValidator', '', 'Title is required');
        $this->addValidator('body', 'required', 'propel.validator.RequiredValidator', '', 'Post body is required');
        $this->addValidator('body', 'minLength', 'propel.validator.MinLengthValidator', '4', 'Post body must be at least 4 characters');
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('User', 'Nurolopher\\BlogBundle\\Model\\User', RelationMap::MANY_TO_ONE, array('user_id' => 'id', ), null, null);
        $this->addRelation('PostCategory', 'Nurolopher\\BlogBundle\\Model\\PostCategory', RelationMap::ONE_TO_MANY, array('id' => 'post_id', ), null, null, 'PostCategories');
        $this->addRelation('Comment', 'Nurolopher\\BlogBundle\\Model\\Comment', RelationMap::ONE_TO_MANY, array('id' => 'post_id', ), null, null, 'Comments');
        $this->addRelation('Category', 'Nurolopher\\BlogBundle\\Model\\Category', RelationMap::MANY_TO_MANY, array(), null, null, 'Categories');
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'timestampable' =>  array (
  'create_column' => 'created_at',
  'update_column' => 'updated_at',
  'disable_updated_at' => 'false',
),
        );
    } // getBehaviors()

} // PostTableMap
