
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
-- user_story
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `user_story`;

CREATE TABLE `user_story`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `project_id` INTEGER,
    `numero` VARCHAR(255),
    `description` TEXT,
    `value` INTEGER,
    `complexity` INTEGER,
    `ratio` FLOAT,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `user_story_FI_1` (`project_id`),
    CONSTRAINT `user_story_FK_1`
        FOREIGN KEY (`project_id`)
        REFERENCES `project` (`id`)
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
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `task_FI_1` (`user_story_id`),
    CONSTRAINT `task_FK_1`
        FOREIGN KEY (`user_story_id`)
        REFERENCES `user_story` (`id`)
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
) ENGINE=InnoDB CHARACTER SET='utf8' COLLATE='utf8_bin';

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
