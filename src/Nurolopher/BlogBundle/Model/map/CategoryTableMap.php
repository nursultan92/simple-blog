<?php

namespace Nurolopher\BlogBundle\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'category' table.
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
class CategoryTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Nurolopher.BlogBundle.Model.map.CategoryTableMap';

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
        $this->setName('category');
        $this->setPhpName('Category');
        $this->setClassname('Nurolopher\\BlogBundle\\Model\\Category');
        $this->setPackage('src.Nurolopher.BlogBundle.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('title', 'Title', 'VARCHAR', true, 100, null);
        // validators
        $this->addValidator('title', 'required', 'propel.validator.RequiredValidator', '', '');
        $this->addValidator('title', 'unique', 'propel.validator.UniqueValidator', '', '');
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('PostCategory', 'Nurolopher\\BlogBundle\\Model\\PostCategory', RelationMap::ONE_TO_MANY, array('id' => 'category_id', ), null, null, 'PostCategories');
        $this->addRelation('Post', 'Nurolopher\\BlogBundle\\Model\\Post', RelationMap::MANY_TO_MANY, array(), 'CASCADE', null, 'Posts');
    } // buildRelations()

} // CategoryTableMap
