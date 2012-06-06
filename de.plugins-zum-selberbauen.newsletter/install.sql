DROP TABLE IF EXISTS wcf1_newsletter;
CREATE TABLE wcf1_newsletter (
    newsletterID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    userID INT(10) NOT NULL DEFAULT 0,
    username VARCHAR(255) NOT NULL DEFAULT '',
    subject VARCHAR(255) NOT NULL DEFAULT '',
    text MEDIUMTEXT NOT NULL,
    deliveryTime INT(10) NOT NULL DEFAULT 0,
    enableSmilies TINYINT(1) NOT NULL DEFAULT 1,
    enableHtml TINYINT(1) NOT NULL DEFAULT 0,
    enableBBCodes TINYINT(1) NOT NULL DEFAULT 1,
    FULLTEXT KEY (subject, text),
    KEY (userID),
    KEY (deliveryTime)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wcf1_newsletter_subscriber;
CREATE TABLE wcf1_newsletter_subscriber (
    subscriberID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    userID INT(10) NOT NULL DEFAULT 0,
    username VARCHAR(255) NOT NULL DEFAULT '',
    email VARCHAR(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wcf1_newsletter_activation;
CREATE TABLE wcf1_newsletter_activation (
    userID INT(10) NOT NULL PRIMARY KEY,
    token VARCHAR(255) NOT NULL DEFAULT '',
    datetime INT(10) NOT NULL DEFAULT 0,
    ip INT(10) UNSIGNED NOT NULL DEFAULT 0,
    activated TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wcf1_newsletter_guest_activation;
CREATE TABLE wcf1_newsletter_guest_activation (
    subscriberID INT(10) NOT NULL PRIMARY KEY,
    token VARCHAR(255) NOT NULL DEFAULT '',
    datetime INT(10) NOT NULL DEFAULT 0,
    ip INT(10) UNSIGNED NOT NULL DEFAULT 0,
    activated TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wcf1_newsletter_unsubscription;
CREATE TABLE wcf1_newsletter_unsubscription (
    subscriberID INT(10) NOT NULL PRIMARY KEY,
    token VARCHAR(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;