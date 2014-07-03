
-- -----------------------------------------------------
-- Table `#__catalogcategory`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__catalogcategory` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT,
  `ImageUrl` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `ThumbUrl` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `CategoryId` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  INDEX `CategoryId_idx3` (`CategoryId` ASC),
  CONSTRAINT `catalogcategory_ibfk_4`
    FOREIGN KEY (`CategoryId`)
    REFERENCES `#__catalogcategory` (`Id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `#__catalogcoupon`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__catalogcoupon` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT,
  `Code` VARCHAR(30) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Discount` INT(11) NOT NULL,
  `Uses` INT(11) NOT NULL,
  `Enable` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE INDEX `Code` (`Code` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `#__languages`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__languages` (
  `lang_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`lang_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `#__catalogcategorylang`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__catalogcategorylang` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT,
  `LangId` INT(11) UNSIGNED NOT NULL,
  `CategoryId` INT(11) NOT NULL,
  `Name` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Alias` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Description` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE INDEX `Id_UNIQUE` (`Id` ASC),
  INDEX `CategoryId_idx` (`CategoryId` ASC),
  INDEX `LangId` (`LangId` ASC),
  CONSTRAINT `catalogcategorylang_ibfk_1`
    FOREIGN KEY (`CategoryId`)
    REFERENCES `#__catalogcategory` (`Id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `catalogcategorylang_ibfk_2`
    FOREIGN KEY (`LangId`)
    REFERENCES `#__languages` (`lang_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `#__catalogpaymentmethod`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__catalogpaymentmethod` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT,
  `Enable` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `#__catalogpaymentmethodlang`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__catalogpaymentmethodlang` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT,
  `PaymentMethodId` INT(11) NOT NULL,
  `Name` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Description` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `LangId` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `PaymentMethodId` (`PaymentMethodId` ASC),
  INDEX `LangId` (`LangId` ASC),
  CONSTRAINT `PaymentMethodLangId`
    FOREIGN KEY (`LangId`)
    REFERENCES `#__languages` (`lang_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `PaymentMethodLang`
    FOREIGN KEY (`PaymentMethodId`)
    REFERENCES `#__catalogpaymentmethod` (`Id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `#__catalogproduct`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__catalogproduct` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT,
  `CategoryId` INT(11) NOT NULL,
  `SalePrice` DOUBLE NULL DEFAULT NULL,
  `RentPrice` DOUBLE NULL DEFAULT NULL,
  `Address` LONGTEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `UpdatedAt` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `CreatedAt` DATETIME NULL DEFAULT NULL,
  `Feature` INT(11) NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `CategoryId_idx` (`CategoryId` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `#__catalogproductimage`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__catalogproductimage` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT,
  `ProductId` INT(11) NOT NULL,
  `ImageUrl` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `ImageThumb` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Main` INT(4) NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `ProductId` (`ProductId` ASC),
  CONSTRAINT `ProductImageId`
    FOREIGN KEY (`ProductId`)
    REFERENCES `#__catalogproduct` (`Id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `#__catalogproductlang`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__catalogproductlang` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT,
  `ProductId` INT(11) NOT NULL,
  `LangId` INT(11) UNSIGNED NOT NULL,
  `Name` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Alias` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Description` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Note` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `ProductId` (`ProductId` ASC),
  INDEX `LangId` (`LangId` ASC),
  CONSTRAINT `ProductLangId`
    FOREIGN KEY (`LangId`)
    REFERENCES `#__languages` (`lang_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `_ProductId`
    FOREIGN KEY (`ProductId`)
    REFERENCES `#__catalogproduct` (`Id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `#__catalogshippingmethod`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__catalogshippingmethod` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT,
  `MinDays` INT(11) NOT NULL,
  `MaxDays` INT(11) NOT NULL,
  `Price` DOUBLE NOT NULL,
  PRIMARY KEY (`Id`))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `#__catalogsalestate`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__catalogsalestate` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`Id`))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `#__catalogsale`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__catalogsale` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT,
  `PaymentMethodId` INT(11) NOT NULL,
  `ShippingMethod` INT(11) NOT NULL,
  `SaleStateId` INT(11) NOT NULL,
  `Total` DOUBLE NOT NULL,
  `UserId` INT(11) NOT NULL,
  `Date` DATETIME NOT NULL,
  `CouponId` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  INDEX `PaymentMethodId` (`PaymentMethodId` ASC, `SaleStateId` ASC, `UserId` ASC, `CouponId` ASC),
  INDEX `SaleStateId` (`SaleStateId` ASC),
  INDEX `UserId` (`UserId` ASC),
  INDEX `CouponId` (`CouponId` ASC),
  INDEX `ShippingMethod` (`ShippingMethod` ASC),
  CONSTRAINT `ShippingMethodSaleId`
    FOREIGN KEY (`ShippingMethod`)
    REFERENCES `#__catalogshippingmethod` (`Id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `catalogsale_ibfk_1`
    FOREIGN KEY (`CouponId`)
    REFERENCES `#__catalogcoupon` (`Id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `PaymentMethodSale`
    FOREIGN KEY (`PaymentMethodId`)
    REFERENCES `#__catalogpaymentmethod` (`Id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `SaleStateSale`
    FOREIGN KEY (`SaleStateId`)
    REFERENCES `#__catalogsalestate` (`Id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `UsersSale`
    FOREIGN KEY (`UserId`)
    REFERENCES `#__users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `#__catalogproductsale`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__catalogproductsale` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT,
  `ProductId` INT(11) NOT NULL,
  `SaleId` INT(11) NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `ProductId` (`ProductId` ASC, `SaleId` ASC),
  INDEX `SaleId` (`SaleId` ASC),
  CONSTRAINT `SaleProductId`
    FOREIGN KEY (`SaleId`)
    REFERENCES `#__catalogsale` (`Id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `ProductSaleId`
    FOREIGN KEY (`ProductId`)
    REFERENCES `#__catalogproduct` (`Id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `#__catalogshippingmethodlang`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__catalogshippingmethodlang` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT,
  `LangId` INT(11) UNSIGNED NOT NULL,
  `ShippingMethodId` INT(11) NOT NULL,
  `Name` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Description` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `LangId` (`LangId` ASC, `ShippingMethodId` ASC),
  INDEX `ShippingMethodId` (`ShippingMethodId` ASC),
  CONSTRAINT `ShippingLanguage`
    FOREIGN KEY (`ShippingMethodId`)
    REFERENCES `#__catalogshippingmethod` (`Id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `ShippingLanguageId`
    FOREIGN KEY (`LangId`)
    REFERENCES `#__languages` (`lang_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `#__catalogsalestatelang`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__catalogsalestatelang` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT,
  `LangId` INT(11) UNSIGNED NOT NULL,
  `SaleStateId` INT(11) NOT NULL,
  `Name` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Description` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `SaleStateId` (`SaleStateId` ASC),
  INDEX `LangId` (`LangId` ASC),
  CONSTRAINT `SaleStateLangLangId`
    FOREIGN KEY (`LangId`)
    REFERENCES `#__languages` (`lang_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `SaleStateLang`
    FOREIGN KEY (`SaleStateId`)
    REFERENCES `#__catalogsalestate` (`Id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `country`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__country` (
  `Id` INT(11) NOT NULL,
  `CountryCode` VARCHAR(10) NOT NULL,
  `Name` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  PRIMARY KEY (`Id`))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `province`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__province` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT,
  `CountryId` INT(11) NOT NULL,
  `Name` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `prov_c_idx` (`CountryId` ASC),
  CONSTRAINT `prov_c`
    FOREIGN KEY (`CountryId`)
    REFERENCES `#__country` (`Id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `cities`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__cities` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT,
  `ProvinceId` INT(11) NOT NULL,
  `Name` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `ci_prov_idx` (`ProvinceId` ASC),
  CONSTRAINT `ci_prov`
    FOREIGN KEY (`ProvinceId`)
    REFERENCES `#__province` (`Id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `#__catalogshippingcities`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__catalogshippingcities` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT,
  `CityId` INT(11) NOT NULL,
  `ShippingMethodId` INT(11) NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `CityId` (`CityId` ASC, `ShippingMethodId` ASC),
  INDEX `ShippingMethodId` (`ShippingMethodId` ASC),
  CONSTRAINT `ShippingCitiesMethod`
    FOREIGN KEY (`ShippingMethodId`)
    REFERENCES `#__catalogshippingmethod` (`Id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `ShippingCities`
    FOREIGN KEY (`CityId`)
    REFERENCES `#__cities` (`Id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `sector`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__sector` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT,
  `CityId` INT(11) NOT NULL,
  `Name` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `ci_sect_idx` (`CityId` ASC),
  CONSTRAINT `ci_sect`
    FOREIGN KEY (`CityId`)
    REFERENCES `#__cities` (`Id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `#__catalogcurrencies`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__catalogcurrencies` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `CurrCode` VARCHAR(45) NOT NULL,
  `Rate` DOUBLE NOT NULL,
  PRIMARY KEY (`Id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `#__catalogcurrencieslang`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__catalogcurrencieslang` (
  `Id` INT(11) NOT NULL,
  `Name` VARCHAR(45) NOT NULL,
  `CurrencyId` INT NOT NULL,
  `LangId` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `curr_id_idx` (`CurrencyId` ASC),
  INDEX `lang_id_idx` (`LangId` ASC),
  CONSTRAINT `curr_id`
    FOREIGN KEY (`CurrencyId`)
    REFERENCES `#__catalogcurrencies` (`Id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `lang_id`
    FOREIGN KEY (`LangId`)
    REFERENCES `#__languages` (`lang_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;
