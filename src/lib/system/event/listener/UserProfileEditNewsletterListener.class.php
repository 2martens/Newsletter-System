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
    
    /**
     * Contains the subscriber database table.
     * @var string
     */
    protected $subscriberTable = 'newsletter_subscriber';
    
    /**
     * Contains the activation database table.
     * @var string
     */
    protected $activationTable = 'newsletter_activation';
    
    /**
     * Contains the unsubscription database table.
     * @var string
     */
    protected $unsubscriptionTable = 'newsletter_unsubscription';
    
    /**
     * @see EventListener::execute()
     */
    public function execute($eventObj, $className, $eventName) {
        if (!isset($eventObj->activeOptions['acceptNewsletter'])) return;
        $optionGeneral = $eventObj->activeOptions['acceptNewsletter'];
        $optionEmail = $eventObj->activeOptions['acceptNewsletterAsEmail'];
        $optionPM = $eventObj->activeOptions['acceptNewsletterAsPM'];
        $sql = 'SELECT COUNT(userID) AS count
        		FROM wcf'.WCF_N.'_'.$this->subscriberTable.'
        		WHERE userID = '.intval(WCF::getUser()->userID);
        $existCount = WCF::getDB()->getFirstRow($sql);
        
        $sql = 'SELECT COUNT(userID) AS count
        		FROM wcf'.WCF_N.'_'.$this->activationTable.'
        		WHERE userID = '.intval(WCF::getUser()->userID);
        $activationCount = WCF::getDB()->getFirstRow($sql);
        
        if ($optionGeneral['optionValue'] && !$existCount['count'] &&
            ($optionEmail['optionValue'] || $optionPM['optionValue']) &&
            !$activationCount['count']) {
                
            
            NewsletterUtil::sendUserValidationEmail();
        }
        elseif (!$optionGeneral['optionValue']) {
            $editor = WCF::getUser()->getEditor();
            $options = array(
                'acceptNewsletter' => 0
            );
            $editor->updateOptions($options);
            $this->deleteSubscriber();
        }
        elseif ($optionGeneral['optionValue'] && $existCount['count'] &&
            !$optionEmail['optionValue'] && !$optionPM['optionValue']) {
                
            $editor = WCF::getUser()->getEditor();
            $options = array(
                'acceptNewsletter' => 0
            );
            $editor->updateOptions($options);
            $this->deleteSubscriber();
        }
        elseif ($optionGeneral['optionValue'] && !$existCount['count'] &&
            !$optionEmail['optionValue'] && !$optionPM['optionValue']) {
                
            $editor = WCF::getUser()->getEditor();
            $options = array(
                'acceptNewsletter' => 0
            );
            $editor->updateOptions($options);
            $this->deleteSubscriber();
        }
        else {};
        
        WCF::getCache()->clear(WCF_DIR.'cache/', 'cache.newsletter-subscriber-'.PACKAGE_ID.'.php', true);
    }
    
    
    
    /**
     * Deletes this user from the subscriber and activation table.
     */
    protected function deleteSubscriber() {
        $sql = 'SELECT subscriberID
        		FROM wcf'.WCF_N.'_'.$this->subscriberTable.'
        		WHERE userID = '.intval(WCF::getUser()->userID);
        $row = WCF::getDB()->getFirstRow($sql);
        
        $sql = 'DELETE FROM wcf'.WCF_N.'_'.$this->unsubscriptionTable.'
        		WHERE subscriberID = '.intval($row['subscriberID']);
        WCF::getDB()->sendQuery($sql);
        
        $sql = 'DELETE FROM wcf'.WCF_N.'_'.$this->subscriberTable.'
        		WHERE userID = '.intval(WCF::getUser()->userID);
        WCF::getDB()->sendQuery($sql);
        
        $sql = 'DELETE FROM wcf'.WCF_N.'_'.$this->activationTable.'
        		WHERE userID = '.intval(WCF::getUser()->userID);
        WCF::getDB()->sendQuery($sql);
    }
}
