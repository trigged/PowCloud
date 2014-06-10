<?php

use Illuminate\Database\Migrations\Migration;

class AppTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $sql = "
       SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


-- -----------------------------------------------------
-- Table `x-cms`.`team`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `x-cms`.`team` ;

CREATE TABLE IF NOT EXISTS `x-cms`.`team` (
  `id` INT NOT NULL,
  `name` VARCHAR(45) NULL,
  `author` VARCHAR(45) NULL,
  `Members` VARCHAR(45) NULL,
  `created_at` Datetime NULL,
  `updated_at` Datetime NULL,
  `deleted_at` Datetime NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `x-cms`.`service`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `x-cms`.`service` ;

CREATE TABLE IF NOT EXISTS `x-cms`.`service` (
  `id` INT NOT NULL,
  `name` VARCHAR(45) NULL,
  `type` VARCHAR(45) NULL,
  `params` VARCHAR(45) NULL,
  `created_at` Datetime NULL,
  `updated_at` Datetime NULL,
  `deleted_at` Datetime NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `x-cms`.`app`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `x-cms`.`app` ;

CREATE TABLE IF NOT EXISTS `x-cms`.`app` (
  `id` INT NOT NULL,
  `name` VARCHAR(45) NULL,
  `Author` VARCHAR(45) NULL,
  `team_id` INT NOT NULL,
  `info` VARCHAR(45) NULL,
  `service_id` INT NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `deleted_at` DATETIME NULL,
  `user_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_App_team1_idx` (`team_id` ASC),
  INDEX `fk_app_service1_idx` (`service_id` ASC),
  INDEX `fk_app_user1_idx` (`user_id` ASC))
ENGINE = InnoDB;
-- -----------------------------------------------------
-- Table `x-cms`.`atu_relation`
-- -----------------------------------------------------

DROP TABLE IF EXISTS `x-cms`.`atu_relation` ;

CREATE TABLE IF NOT EXISTS `x-cms`.`atu_relation` (
  `team_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `app_id` INT NOT NULL,
  `created_at` VARCHAR(45) NULL,
  `updated_at` VARCHAR(45) NULL,
  `deleted_at` VARCHAR(45) NULL,
  PRIMARY KEY (`team_id`, `user_id`, `app_id`))
ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

";


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

}