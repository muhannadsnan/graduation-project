-- Function: full_path

-- DROP FUNCTION IF EXISTS `full_path`;

DELIMITER |

CREATE FUNCTION `full_path` 
(
  `s`  char(32),
  `d`  char(10)
)
RETURNS text charset utf8
  DETERMINISTIC
BEGIN

DECLARE v1 CHAR(32) DEFAULT "";
DECLARE rooti TEXT DEFAULT "";

  SET v1 = s;

  WHILE v1 != "0" DO
    
    SELECT DRoot into v1 from dirs where DID = v1 ;
    
    IF rooti = "" THEN
        SET rooti = v1;
    ELSE
	SET rooti = CONCAT(rooti, d, v1);
    END IF;
  END WHILE;

RETURN rooti;
END|

DELIMITER ;



-- ----------------------------------------------------------------------------------------------------------------------

-- Function: full_path_text

-- DROP FUNCTION IF EXISTS `full_path_text`;

DELIMITER |

CREATE FUNCTION `full_path_text` 
(
  `s`  char(32),
  `d`  char(10)
)
RETURNS text charset utf8
  DETERMINISTIC
BEGIN

DECLARE v1 CHAR(100) DEFAULT "";
DECLARE v2 CHAR(100) DEFAULT "";
DECLARE rooti TEXT DEFAULT "";

  SET v1 = s;

  WHILE v1 != "0" DO
    
    SELECT DRoot, DName into v1, v2 from dirs where DID = v1 ;
    
    IF rooti = "" THEN
        SET rooti = v2;
    ELSE
	SET rooti = CONCAT(rooti, d, v2);
    END IF;
  END WHILE;

RETURN rooti;
END|

DELIMITER ;

-- --------------------------------------------------------------------------------------------------------------------------

-- Function: full_path_text_en

-- DROP FUNCTION IF EXISTS `full_path_text_en`;

DELIMITER |

CREATE FUNCTION `full_path_text_en` 
(
  `s`  char(32),
  `d`  char(10)
)
RETURNS text charset utf8
  DETERMINISTIC
BEGIN

DECLARE v1 CHAR(100) DEFAULT "";
DECLARE v2 CHAR(100) DEFAULT "";
DECLARE rooti TEXT DEFAULT "";

  SET v1 = s;

  WHILE v1 != "0" DO
    
    SELECT DRoot, DName_en into v1, v2 from dirs where DID = v1 ;
    
    IF rooti = "" THEN
        SET rooti = v2;
    ELSE
	SET rooti = CONCAT(rooti, d, v2);
    END IF;
  END WHILE;

RETURN rooti;
END|

DELIMITER ;

-- ------------------------------------------------------------------------------------------------------------------------------

CREATE VIEW `dirs_count` AS select `dirs`.`DID` AS `DID`,`dirs`.`DName` AS `DName`,`dirs`.`DRoot` AS `DRoot`,`dirs`.`NDate` AS `NDate`,ifnull(`dirs_gc`.`c`,0) AS `c` from (`dirs` left join `dirs_gc` on((`dirs`.`DID` = `dirs_gc`.`DRoot`)));

-- -------------------------------------------------------------------------------------------------------------------------------

CREATE VIEW `dirs_gc` AS select `dirs`.`DRoot` AS `DRoot`,count(`dirs`.`DID`) AS `c` from `dirs` group by `dirs`.`DRoot`;

-- --------------------------------------------------------------------------------------------------------------------------------

CREATE VIEW `dirs_path` AS select `dirs`.`DID` AS `DID`,`dirs`.`DName` AS `DName`,`full_path`(`dirs`.`DID`,_utf8'|') AS `DPath`,`full_path_text`(`dirs`.`DID`,_utf8'|') AS `DTextPath` from `dirs`;

-- ---------------------------------------------------------------------------------------------------------------------------------

CREATE VIEW `dirs_path_en` AS select `dirs`.`DID` AS `DID`,`dirs`.`DName_en` AS `DName`,`full_path`(`dirs`.`DID`,_utf8'|') AS `DPath`,`full_path_text_en`(`dirs`.`DID`,_utf8'|') AS `DTextPath` from `dirs`;

-- -----------------------------------------------------------------------------------------------------------------------------------

