<?php

namespace Nurolopher\BlogBundle\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'post_category' table.
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
class PostCategoryTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Nurolopher.BlogBundle.Model.map.PostCategoryTableMap';

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
        $this->setName('post_category');
        $this->setPhpName('PostCategory');
        $this->setClassname('Nurolopher\\BlogBundle\\Model\\PostCategory');
        $this->setPackage('src.Nurolopher.BlogBundle.Model');
        $this->setUseIdGenerator(false);
        $this->setIsCrossRef(true);
        // columns
        $this->addForeignPrimaryKey('post_id', 'PostId', 'INTEGER' , 'post', 'id', true, null, null);
        $this->addForeignPrimaryKey('category_id', 'CategoryId', 'INTEGER' , 'category', 'id', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Post', 'Nurolopher\\BlogBundle\\Model\\Post', RelationMap::MANY_TO_ONE, array('post_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Category', 'Nurolopher\\BlogBundle\\Model\\Category', RelationMap::MANY_TO_ONE, array('category_id' => 'id', ), null, null);
    } // buildRelations()

} // PostCategoryTableMap
