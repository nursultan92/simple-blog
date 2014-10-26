
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- post
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `post`;

CREATE TABLE `post`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `body` TEXT NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- category
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `category`;

CREATE TABLE `category`
(
    `id` INTEGER NOT NULL,
    `title` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- post_category
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `post_category`;

CREATE TABLE `post_category`
(
    `post_id` INTEGER NOT NULL,
    `category_id` INTEGER NOT NULL,
    PRIMARY KEY (`post_id`,`category_id`),
    UNIQUE INDEX `post_category_U_1` (`post_id`, `category_id`),
    INDEX `post_category_FI_2` (`category_id`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- comment
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `comment`;

CREATE TABLE `comment`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `body` TEXT NOT NULL,
    `post_id` INTEGER,
    `user_id` INTEGER,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `comment_FI_1` (`post_id`),
    INDEX `comment_FI_2` (`user_id`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- user
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user`
(
    `id` INTEGER NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `salt` VARCHAR(255),
    `password` VARCHAR(255) NOT NULL,
    `firstname` VARCHAR(64) NOT NULL,
    `lastname` VARCHAR(64),
    `group_id` INTEGER,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `user_U_1` (`email`),
    INDEX `user_FI_1` (`group_id`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- group
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `group`;

CREATE TABLE `group`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `roles` TEXT NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
