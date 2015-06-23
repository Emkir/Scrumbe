<?php

namespace Scrumbe\Models\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'user_story' table.
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
class UserStoryTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = '.map.UserStoryTableMap';

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
        $this->setName('user_story');
        $this->setPhpName('UserStory');
        $this->setClassname('Scrumbe\\Models\\UserStory');
        $this->setPackage('');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('project_id', 'ProjectId', 'INTEGER', 'project', 'id', false, null, null);
        $this->addColumn('number', 'Number', 'VARCHAR', false, 255, null);
        $this->addColumn('description', 'Description', 'LONGVARCHAR', false, null, null);
        $this->addColumn('value', 'Value', 'INTEGER', false, null, null);
        $this->addColumn('complexity', 'Complexity', 'INTEGER', false, null, null);
        $this->addColumn('ratio', 'Ratio', 'FLOAT', false, null, null);
        $this->addColumn('priority', 'Priority', 'VARCHAR', false, 255, null);
        $this->addColumn('label', 'Label', 'VARCHAR', false, 255, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Project', 'Scrumbe\\Models\\Project', RelationMap::MANY_TO_ONE, array('project_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Task', 'Scrumbe\\Models\\Task', RelationMap::ONE_TO_MANY, array('id' => 'user_story_id', ), null, null, 'Tasks');
        $this->addRelation('LinkUserStorySprint', 'Scrumbe\\Models\\LinkUserStorySprint', RelationMap::ONE_TO_MANY, array('id' => 'user_story_id', ), 'CASCADE', null, 'LinkUserStorySprints');
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

} // UserStoryTableMap
