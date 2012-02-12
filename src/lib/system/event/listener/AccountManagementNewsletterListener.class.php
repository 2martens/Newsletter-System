<?php
//wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Changes email in subscriber table.
 *
 * @author Jim Martens
 * @copyright 2012 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage system.event.listener
 * @category Community Framework
 */
class AccountManagementNewsletterListener implements EventListener {
    
    /**
     * Contains the subscriber database table name.
     * @var string
     */
    protected $subscriberTable = 'newsletter_subscriber';
    
    /**
     * @see EventListener::execute()
     */
    public function execute($eventObj, $className, $eventName) {
        $email = $eventObj->email;
        if (WCF::getUser()->email == $email) return;

        $sql = 'UPDATE wcf'.WCF_N.'_'.$this->subscriberTable."
        		SET email = '".escapeString($email)."'
        		WHERE userID = ".WCF::getUser()->userID;
        WCF::getDB()->sendQuery($sql);
    }
}
