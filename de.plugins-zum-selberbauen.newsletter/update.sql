ALTER TABLE `wcf1_newsletter`
    ADD COLUMN `enableSmilies` TINYINT(1) NOT NULL DEFAULT 1 AFTER `deliveryTime`,
    ADD COLUMN `enableHtml` TINYINT(1) NOT NULL DEFAULT 0 AFTER `enableSmilies`,
    ADD COLUMN `enableBBCodes` TINYINT(1) NOT NULL DEFAULT 1 AFTER `enableHtml`;
    