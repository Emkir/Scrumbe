<?php

namespace Scrumbe\Models\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'kanban_task' table.
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
class KanbanTaskTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = '.map.KanbanTaskTableMap';

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
        $this->setName('kanban_task');
        $this->setPhpName('KanbanTask');
        $this->setClassname('Scrumbe\\Models\\KanbanTask');
        $this->setPackage('');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('task_id', 'TaskId', 'INTEGER', 'task', 'id', false, null, null);
        $this->addForeignKey('sprint_id', 'SprintId', 'INTEGER', 'sprint', 'id', false, null, null);
        $this->addColumn('task_position', 'TaskPosition', 'INTEGER', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Task', 'Scrumbe\\Models\\Task', RelationMap::MANY_TO_ONE, array('task_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Sprint', 'Scrumbe\\Models\\Sprint', RelationMap::MANY_TO_ONE, array('sprint_id' => 'id', ), 'CASCADE', null);
    } // buildRelations()

} // KanbanTaskTableMap
