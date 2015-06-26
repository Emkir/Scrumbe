
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- project
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `project`;

CREATE TABLE `project`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255),
    `url_name` VARCHAR(255),
    `description` TEXT,
    `cover_project` VARCHAR(255) DEFAULT '/assets/img/back-home.jpg',
    `start_date` DATE,
    `end_date` DATE,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB CHARACTER SET='utf8' COLLATE='utf8_bin';

-- ---------------------------------------------------------------------
-- sprint
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `sprint`;

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
) ENGINE=InnoDB CHARACTER SET='utf8' COLLATE='utf8_bin';

-- ---------------------------------------------------------------------
-- user_story
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `user_story`;

CREATE TABLE `user_story`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `project_id` INTEGER,
    `number` INTEGER,
    `description` TEXT,
    `value` INTEGER,
    `complexity` INTEGER,
    `ratio` FLOAT,
    `priority` VARCHAR(255),
    `label` VARCHAR(255),
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `user_story_FI_1` (`project_id`),
    CONSTRAINT `user_story_FK_1`
        FOREIGN KEY (`project_id`)
        REFERENCES `project` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET='utf8' COLLATE='utf8_bin';

-- ---------------------------------------------------------------------
-- task
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `task`;

CREATE TABLE `task`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_story_id` INTEGER,
    `time` VARCHAR(255),
    `description` TEXT,
    `progress` VARCHAR(255),
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `task_FI_1` (`user_story_id`),
    CONSTRAINT `task_FK_1`
        FOREIGN KEY (`user_story_id`)
        REFERENCES `user_story` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET='utf8' COLLATE='utf8_bin';

-- ---------------------------------------------------------------------
-- user
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255),
    `password` VARCHAR(255),
    `salt` VARCHAR(255),
    `roles` TEXT,
    `email` VARCHAR(255),
    `firstname` VARCHAR(255),
    `lastname` VARCHAR(255),
    `avatar` VARCHAR(255),
    `domain` VARCHAR(255),
    `business` VARCHAR(255),
    `validation_token` VARCHAR(255),
    `validate` TINYINT(1),
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `user_U_1` (`username`),
    UNIQUE INDEX `user_U_2` (`email`)
) ENGINE=InnoDB CHARACTER SET='utf8' COLLATE='utf8_bin';

-- ---------------------------------------------------------------------
-- link_project_user
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `link_project_user`;

CREATE TABLE `link_project_user`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `project_id` INTEGER,
    `user_id` INTEGER,
    `admin` TINYINT(1),
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `link_project_user_FI_1` (`project_id`),
    INDEX `link_project_user_FI_2` (`user_id`),
    CONSTRAINT `link_project_user_FK_1`
        FOREIGN KEY (`project_id`)
        REFERENCES `project` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `link_project_user_FK_2`
        FOREIGN KEY (`user_id`)
        REFERENCES `user` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET='utf8' COLLATE='utf8_bin';

-- ---------------------------------------------------------------------
-- link_user_story_sprint
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `link_user_story_sprint`;

CREATE TABLE `link_user_story_sprint`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `user_story_id` INTEGER,
    `sprint_id` INTEGER,
    `user_story_position` INTEGER,
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
) ENGINE=InnoDB CHARACTER SET='utf8' COLLATE='utf8_bin';

-- ---------------------------------------------------------------------
-- kanban_task
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `kanban_task`;

CREATE TABLE `kanban_task`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `task_id` INTEGER,
    `sprint_id` INTEGER,
    `task_position` INTEGER,
    PRIMARY KEY (`id`),
    INDEX `kanban_task_FI_1` (`task_id`),
    INDEX `kanban_task_FI_2` (`sprint_id`),
    CONSTRAINT `kanban_task_FK_1`
        FOREIGN KEY (`task_id`)
        REFERENCES `task` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `kanban_task_FK_2`
        FOREIGN KEY (`sprint_id`)
        REFERENCES `sprint` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET='utf8' COLLATE='utf8_bin';

-- ---------------------------------------------------------------------
-- beta
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `beta`;

CREATE TABLE `beta`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(255),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB CHARACTER SET='utf8' COLLATE='utf8_bin';

-- ---------------------------------------------------------------------
-- contact
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `contact`;

CREATE TABLE `contact`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255),
    `email` VARCHAR(255),
    `message` TEXT,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB CHARACTER SET='utf8' COLLATE='utf8_bin';

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
