
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- drivers
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `drivers`;

CREATE TABLE `drivers`
(
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(32) NOT NULL,
    `mac` CHAR(17) NOT NULL,
    `ip` VARCHAR(15) NOT NULL,
    `added` DATETIME NOT NULL,
    `last_check_in` DATETIME NOT NULL,
    `coil_contact` TINYINT(1) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- drivers_unregistered
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `drivers_unregistered`;

CREATE TABLE `drivers_unregistered`
(
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `mac` CHAR(17) NOT NULL,
    `ip` VARCHAR(15) NOT NULL,
    `last_check_in` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `macip` (`mac`, `ip`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- driver_pins
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `driver_pins`;

CREATE TABLE `driver_pins`
(
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `driver_id` int(10) unsigned NOT NULL,
    `pin` tinyint(3) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `driver_pins_fi_1` (`driver_id`),
    CONSTRAINT `driver_pins_fk_1`
        FOREIGN KEY (`driver_id`)
        REFERENCES `drivers` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- driver_pin_values
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `driver_pin_values`;

CREATE TABLE `driver_pin_values`
(
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `driver_pin_id` int(10) unsigned NOT NULL,
    `state_id` int(10) unsigned NOT NULL,
    `value` int(5) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `driver_pin_values_fi_1` (`driver_pin_id`),
    INDEX `driver_pin_values_fi_2` (`state_id`),
    CONSTRAINT `driver_pin_values_fk_1`
        FOREIGN KEY (`driver_pin_id`)
        REFERENCES `driver_pins` (`id`),
    CONSTRAINT `driver_pin_values_fk_2`
        FOREIGN KEY (`state_id`)
        REFERENCES `states` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- driver_outputs
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `driver_outputs`;

CREATE TABLE `driver_outputs`
(
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `driver_id` int(10) unsigned NOT NULL,
    `name` VARCHAR(32) NOT NULL,
    `mapping` int(10) unsigned NOT NULL,
    `overlap_value` int(10) unsigned NOT NULL,
    `central_value` int(10) unsigned NOT NULL,
    `default_delay` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `driver_outputs_fi_1` (`driver_id`),
    CONSTRAINT `driver_outputs_fk_1`
        FOREIGN KEY (`driver_id`)
        REFERENCES `drivers` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- driver_output_pins
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `driver_output_pins`;

CREATE TABLE `driver_output_pins`
(
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `driver_output_id` int(10) unsigned NOT NULL,
    `driver_pin_id` int(10) unsigned NOT NULL,
    `type` TINYINT DEFAULT 0 NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `driver_output_pins_u_21a026` (`driver_pin_id`),
    INDEX `driver_output_pins_fi_1` (`driver_output_id`),
    CONSTRAINT `driver_output_pins_fk_1`
        FOREIGN KEY (`driver_output_id`)
        REFERENCES `driver_outputs` (`id`),
    CONSTRAINT `driver_output_pins_fk_2`
        FOREIGN KEY (`driver_pin_id`)
        REFERENCES `driver_pins` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- output_views
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `output_views`;

CREATE TABLE `output_views`
(
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(32) NOT NULL,
    `display_order` int(3) unsigned NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- output_view_output
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `output_view_output`;

CREATE TABLE `output_view_output`
(
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `output_id` int(10) unsigned NOT NULL,
    `driver_output_id` int(10) unsigned NOT NULL,
    `display_order` int(3) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `outputpinid` (`output_id`, `driver_output_id`),
    INDEX `output_view_output_fi_2` (`driver_output_id`),
    CONSTRAINT `output_view_output_fk_1`
        FOREIGN KEY (`output_id`)
        REFERENCES `output_views` (`id`),
    CONSTRAINT `output_view_output_fk_2`
        FOREIGN KEY (`driver_output_id`)
        REFERENCES `driver_outputs` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- states
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `states`;

CREATE TABLE `states`
(
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(10) unsigned NOT NULL,
    `time` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `users_fi_1` (`user_id`),
    CONSTRAINT `users_fk_1`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- state_bookmarks
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `state_bookmarks`;

CREATE TABLE `state_bookmarks`
(
    `id` int(10) unsigned NOT NULL,
    `description` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `state_bookmarks_fk_1`
        FOREIGN KEY (`id`)
        REFERENCES `states` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- users
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users`
(
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(32) NOT NULL,
    `first_login` DATETIME NOT NULL,
    `last_login` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
