<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1431786509.
 * Generated on 2015-05-16 16:28:29 by vagrant
 */
class PropelMigration_1431786509
{

    public function preUp($manager)
    {
        // add the pre-migration code here
    }

    public function postUp($manager)
    {
        // add the post-migration code here
    }

    public function preDown($manager)
    {
        // add the pre-migration code here
    }

    public function postDown($manager)
    {
        // add the post-migration code here
    }

    /**
     * Get the SQL statements for the Up migration
     *
     * @return array list of the SQL strings to execute for the Up migration
     *               the keys being the datasources
     */
    public function getUpSQL()
    {
        return array (
  'default' => '
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `link_project_user` DROP FOREIGN KEY `link_project_user_FK_2`;

ALTER TABLE `link_project_user` ADD CONSTRAINT `link_project_user_FK_2`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE CASCADE;

ALTER TABLE `task`
    ADD `position` INTEGER AFTER `description`,
    ADD `progress` VARCHAR(255) AFTER `position`;

ALTER TABLE `user_story` CHANGE `progress` `priority` VARCHAR(255);

ALTER TABLE `user_story`
    ADD `label` VARCHAR(255) AFTER `priority`;

CREATE TABLE `sprint`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `project_id` INTEGER,
    `name` VARCHAR(255),
    `start_date` DATE,
    `end_date` DATE,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `sprint_FI_1` (`project_id`),
    CONSTRAINT `sprint_FK_1`
        FOREIGN KEY (`project_id`)
        REFERENCES `project` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET=\'utf8\' COLLATE=\'utf8_bin\';

CREATE TABLE `link_user_story_sprint`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_story_id` INTEGER,
    `sprint_id` INTEGER,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `link_user_story_sprint_FI_1` (`user_story_id`),
    INDEX `link_user_story_sprint_FI_2` (`sprint_id`),
    CONSTRAINT `link_user_story_sprint_FK_1`
        FOREIGN KEY (`user_story_id`)
        REFERENCES `user_story` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `link_user_story_sprint_FK_2`
        FOREIGN KEY (`sprint_id`)
        REFERENCES `sprint` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET=\'utf8\' COLLATE=\'utf8_bin\';

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

    /**
     * Get the SQL statements for the Down migration
     *
     * @return array list of the SQL strings to execute for the Down migration
     *               the keys being the datasources
     */
    public function getDownSQL()
    {
        return array (
  'default' => '
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `sprint`;

DROP TABLE IF EXISTS `link_user_story_sprint`;

ALTER TABLE `link_project_user` DROP FOREIGN KEY `link_project_user_FK_2`;

ALTER TABLE `link_project_user` ADD CONSTRAINT `link_project_user_FK_2`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`);

ALTER TABLE `task` DROP `position`;

ALTER TABLE `task` DROP `progress`;

ALTER TABLE `user_story` CHANGE `priority` `progress` VARCHAR(255);

ALTER TABLE `user_story` DROP `label`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}