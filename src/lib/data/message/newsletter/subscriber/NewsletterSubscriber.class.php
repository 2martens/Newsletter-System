<?php
//wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');

/**
 * Represents a subscriber in the database.
 *
 * @author Jim Martens
 * @copyright 2012 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage data.message.newsletter.subscriber
 * @category Community Framework
 */
class NewsletterSubscriber extends DatabaseObject {
    
    /**
     * Contains the subscriber database table name.
     * @var string
     */
    protected $subscriberTable = 'newsletter_subscriber';
    
    /**
     * Creates a new NewsletterSubscriber object.
     *
     * @param int $subscriberID
     * @param array $row
     */
    public function __construct($subscriberID, $row = array()) {
        if ($subscriberID) {
            $sql = 'SELECT subscriberID, userID, username, email
            		FROM wcf'.WCF_N.'_'.$this->subscriberTable.'
            		WHERE subscriberID = '.intval($subscriberID);
            $row = WCF::getDB()->getFirstRow($sql);
        }
        parent::__construct($row);
    }
}
