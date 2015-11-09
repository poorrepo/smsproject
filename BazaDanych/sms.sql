SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `sms` DEFAULT CHARACTER SET utf8 ;
USE `sms` ;

-- -----------------------------------------------------
-- Table `sms`.`archive_tasks`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sms`.`archive_tasks` (
  `id_archive_task` INT(11) NOT NULL AUTO_INCREMENT,
  `phone_number` VARCHAR(15) NOT NULL,
  `message` VARCHAR(500) CHARACTER SET 'utf8' COLLATE 'utf8_polish_ci' NOT NULL,
  `sender` SMALLINT(6) NOT NULL,
  `send_time` DATETIME NOT NULL,
  `executed_by` VARCHAR(45) NULL DEFAULT NULL,
  `rec_bin` BIT(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`id_archive_task`))
ENGINE = InnoDB
AUTO_INCREMENT = 1001
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `sms`.`black_list_numbers`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sms`.`black_list_numbers` (
  `id_black_list_number` INT(11) NOT NULL AUTO_INCREMENT,
  `black_list_numbers_number` VARCHAR(45) NOT NULL,
  `black_list_numbers_comment` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`id_black_list_number`))
ENGINE = InnoDB
AUTO_INCREMENT = 10
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sms`.`contacts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sms`.`contacts` (
  `id_contact` INT(11) NOT NULL AUTO_INCREMENT,
  `contact_number` INT(11) NOT NULL,
  `contact_nickname` VARCHAR(45) NOT NULL,
  `contact_name` VARCHAR(45) NOT NULL,
  `contact_lastname` VARCHAR(45) NOT NULL,
  `contact_address` VARCHAR(200) NOT NULL,
  `contact_email` VARCHAR(200) NOT NULL,
  `contact_disabled` BIT(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`id_contact`))
ENGINE = InnoDB
AUTO_INCREMENT = 8
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sms`.`message_board`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sms`.`message_board` (
  `id_message_board` INT(11) NOT NULL AUTO_INCREMENT,
  `message_message_board` VARCHAR(5000) NOT NULL,
  `date_message_board` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_message_board`))
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sms`.`tasks`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sms`.`tasks` (
  `id_task` INT(11) NOT NULL AUTO_INCREMENT,
  `phone_number` VARCHAR(15) NOT NULL,
  `message` VARCHAR(500) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL,
  `sender` SMALLINT(6) NOT NULL,
  `send_time` DATETIME NOT NULL,
  `selected_by` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`id_task`))
ENGINE = InnoDB
AUTO_INCREMENT = 201
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `sms`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sms`.`users` (
  `id_user` INT(11) NOT NULL AUTO_INCREMENT,
  `user_name` VARCHAR(45) NOT NULL,
  `user_pass` VARCHAR(32) NOT NULL,
  `register_date` DATETIME NOT NULL,
  `disabled` BIT(1) NOT NULL DEFAULT b'0',
  `access_level` SMALLINT(6) NOT NULL DEFAULT '10',
  `user_imie` VARCHAR(45) NOT NULL,
  `user_nazwisko` VARCHAR(45) NOT NULL,
  `user_dzial` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id_user`))
ENGINE = InnoDB
AUTO_INCREMENT = 13
DEFAULT CHARACTER SET = utf8;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
USE `sms`;

DELIMITER $$
USE `sms`$$
CREATE
DEFINER=`sms`@`localhost`
TRIGGER `sms`.`move_to_arch`
BEFORE DELETE ON `sms`.`tasks`
FOR EACH ROW
IF OLD.selected_by != '' OR OLD.selected_by != 'null' THEN BEGIN
		INSERT INTO `sms`.`archive_tasks` 
		(
			`id_archive_task`, 
			`phone_number`, 
			`message`, 
			`sender`,
			`send_time`, 
			`executed_by`
		) 
		VALUES (OLD.id_task, OLD.phone_number, OLD.message, OLD.sender, OLD.send_time, OLD.selected_by);
		END;
	END IF$$

USE `sms`$$
CREATE
DEFINER=`sms`@`localhost`
TRIGGER `sms`.`users_insert_currentdatatime`
BEFORE INSERT ON `sms`.`users`
FOR EACH ROW
SET NEW.register_date = NOW()$$


DELIMITER ;
