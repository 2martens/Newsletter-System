DROP TABLE IF EXISTS wcf1_newsletter_guest_activation;
CREATE TABLE wcf1_newsletter_guest_activation (
    subscriberID INT(10) NOT NULL PRIMARY KEY,
    token VARCHAR(255) NOT NULL DEFAULT '',
    activated TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE wcf1_newsletter_subscriber DROP INDEX userID;