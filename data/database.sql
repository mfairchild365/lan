SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `lan` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
USE `lan` ;

-- -----------------------------------------------------
-- Table `lan`.`users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `lan`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(256) NOT NULL ,
  `date_created` DATETIME NOT NULL ,
  `date_edited` DATETIME NULL ,
  `status` ENUM('ONLINE', 'OFFLINE') NULL DEFAULT OFFLINE ,
  `ip` VARCHAR(32) NOT NULL ,
  `mac` VARCHAR(16) NOT NULL ,
  `host_name` VARCHAR(256) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `mac` (`mac` ASC) ,
  INDEX `date_edited` (`date_edited` ASC) ,
  INDEX `date_created` (`date_created` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `lan`.`messages`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `lan`.`messages` (
  `id` INT NOT NULL ,
  `users_id` INT(45) NULL ,
  `date_created` DATETIME NULL ,
  `date_edited` DATETIME NULL ,
  `message` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_messages_users_idx` (`users_id` ASC) ,
  INDEX `date_created` (`date_created` ASC) ,
  INDEX `date_edited` (`date_edited` ASC) ,
  CONSTRAINT `fk_messages_users`
    FOREIGN KEY (`users_id` )
    REFERENCES `lan`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
