<?php
//wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Deletes subscribers if the corresponding users are deleted.
 *
 * @author Jim Martens
 * @copyright 2012 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage system.event.listener
 * @category Community Framework
 */
class UserDeleteNewsletterListener implements EventListener {
    
    /**
     * Contains the subscriber database table name.
     * @var string
     */
    protected $subscriberTable = 'newsletter_subscriber';
    
    /**
     * Contains the activation database table name.
     * @var string
     */
    protected $activationTable = 'newsletter_activation';
    
    /**
     * @see EventListener::execute()
     */
    public function execute($eventObj, $className, $eventName) {
        if (!$eventObj->userID) return;
        $sql = 'DELETE FROM wcf'.WCF_N.'_'.$this->subscriberTable.'
        		WHERE userID = '.intval($eventObj->userID);
        WCF::getDB()->sendQuery($sql);
        $sql = 'DELETE FROM wcf'.WCF_N.'_'.$this->activationTable.'
        		WHERE userID = '.intval($eventObj->userID);
        WCF::getDB()->sendQuery($sql);
        //clears cache
        WCF::getCache()->clear(WCF_DIR.'cache/', 'cache.newsletter-subscriber-'.PACKAGE_ID.'.php');
    }
}
