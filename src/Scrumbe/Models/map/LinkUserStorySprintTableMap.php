<?php

namespace Scrumbe\Models\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'link_user_story_sprint' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator..map
 */
class LinkUserStorySprintTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = '.map.LinkUserStorySprintTableMap';

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
        $this->setName('link_user_story_sprint');
        $this->setPhpName('LinkUserStorySprint');
        $this->setClassname('Scrumbe\\Models\\LinkUserStorySprint');
        $this->setPackage('');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('user_story_id', 'UserStoryId', 'INTEGER', 'user_story', 'id', false, null, null);
        $this->addForeignKey('sprint_id', 'SprintId', 'INTEGER', 'sprint', 'id', false, null, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('UserStory', 'Scrumbe\\Models\\UserStory', RelationMap::MANY_TO_ONE, array('user_story_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Sprint', 'Scrumbe\\Models\\Sprint', RelationMap::MANY_TO_ONE, array('sprint_id' => 'id', ), 'CASCADE', null);
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

} // LinkUserStorySprintTableMap
