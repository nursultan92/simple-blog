<?php

namespace Nurolopher\BlogBundle\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'user' table.
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
class UserTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Nurolopher.BlogBundle.Model.map.UserTableMap';

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
        $this->setName('user');
        $this->setPhpName('User');
        $this->setClassname('Nurolopher\\BlogBundle\\Model\\User');
        $this->setPackage('src.Nurolopher.BlogBundle.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('email', 'Email', 'VARCHAR', true, 255, null);
        $this->addColumn('salt', 'Salt', 'VARCHAR', false, 255, null);
        $this->addColumn('password', 'Password', 'VARCHAR', true, 255, null);
        $this->addColumn('firstname', 'Firstname', 'VARCHAR', true, 64, null);
        $this->addColumn('lastname', 'Lastname', 'VARCHAR', false, 64, null);
        $this->addForeignKey('group_id', 'GroupId', 'INTEGER', 'group', 'id', true, null, null);
        // validators
        $this->addValidator('email', 'match', 'propel.validator.MatchValidator', '/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9])+(\.[a-zA-Z0-9_-]+)+$/', 'Please enter a valid email address');
        $this->addValidator('email', 'unique', 'propel.validator.UniqueValidator', '', 'Email already exists');
        $this->addValidator('group_id', 'required', 'propel.validator.RequiredValidator', '', '');
        $this->addValidator('firstname', 'required', 'propel.validator.RequiredValidator', '', '');
        $this->addValidator('firstname', 'minLength', 'propel.validator.MinLengthValidator', '3', 'Firstname must be at least 3 characters');
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Group', 'Nurolopher\\BlogBundle\\Model\\Group', RelationMap::MANY_TO_ONE, array('group_id' => 'id', ), null, null);
        $this->addRelation('Post', 'Nurolopher\\BlogBundle\\Model\\Post', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), 'CASCADE', null, 'Posts');
        $this->addRelation('Comment', 'Nurolopher\\BlogBundle\\Model\\Comment', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), 'CASCADE', null, 'Comments');
    } // buildRelations()

} // UserTableMap
