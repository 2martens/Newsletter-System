<?php
//wcf imports
require_once(WCF_DIR.'lib/data/message/Message.class.php');

/**
 * This class represents a newsletter.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage data.message.newsletter
 * @category Community Framework
 */
class Newsletter extends Message {
    
    protected $databaseTable = 'newsletter';
    
    /**
     * Creates a new Newsletter object.
     *
     * @param int $newsletterID
     * @param array $row
     */
    public function __construct($newsletterID, array $row = array()) {
        if ($newsletterID !== null && intval($newsletterID) > 0) {
            $sql = 'SELECT newsletterID, userID, deliveryTime, subject, text, enableSmilies, enableHtml, enableBBCodes
            		FROM wcf'.WCF_N.'_'.$this->databaseTable.' newsletter
            		WHERE newsletter.newsletterID = '.intval($newsletterID);
            $row = WCF::getDB()->getFirstRow($sql);
        }
        $this->messageID = $row['newsletterID'];
        parent::__construct($row);
    }
}
