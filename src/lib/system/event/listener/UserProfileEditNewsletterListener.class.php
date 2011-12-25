<?php
//wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Adds or deletes users from the subscribers database table.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage system.event.listener
 * @category Community Framework
 */
class UserProfileEditNewsletterListener implements EventListener {
    
    protected $databaseTable = 'newsletter_subscriber';
    
    /**
     * @see EventListener::execute()
     */
    public function execute($eventObj, $className, $eventName) {
        $option = $eventObj->activeOptions['acceptNewsletter'];
        if ($option) $this->addSubscriber();
        else $this->deleteSubscriber();
    }
    
    /**
     * Adds this user to the subscriber table.
     */
    protected function addSubscriber() {
        $sql = 'INSERT INTO wcf'.WCF_N.'_'.$this->databaseTable.'
        		(userID, username, email)
        			VALUES
        		('.intval(WCF::getUser()->userID).", '".
                escapeString(WCF::getUser()->username)."', '".
                escapeString(WCF::getUser()->email)."')";
        WCF::getDB()->sendQuery($sql);
    }
    
    /**
     * Deletes this user from the subscriber table.
     */
    protected function deleteSubscriber() {
        $sql = 'DELETE FROM wcf'.WCF_N.'_'.$this->databaseTable.'
        		WHERE userID = '.intval(WCF::getUser()->userID);
        WCF::getDB()->sendQuery($sql);
    }
}
