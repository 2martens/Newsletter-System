DROP TABLE IF EXISTS wcf1_newsletter_unsubscription;
CREATE TABLE wcf1_newsletter_unsubscription (
    subscriberID INT(10) NOT NULL PRIMARY KEY,
    token VARCHAR(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;