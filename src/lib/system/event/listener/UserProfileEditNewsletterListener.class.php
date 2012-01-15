<?php
//wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');
require_once(WCF_DIR.'lib/data/mail/Mail.class.php');

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
     * @see EventListener::execute()
     */
    public function execute($eventObj, $className, $eventName) {
        if (!isset($eventObj->activeOptions['acceptNewsletter'])) return;
        $option = $eventObj->activeOptions['acceptNewsletter'];
        $sql = 'SELECT COUNT(userID) AS count
        		FROM wcf'.WCF_N.'_'.$this->subscriberTable.'
        		WHERE userID = '.intval(WCF::getUser()->userID);
        $existCount = WCF::getDB()->getFirstRow($sql);
        
        if ($option['optionValue'] && !$existCount['count']) $this->sendValidationEmail();
        elseif (!$option['optionValue'] && $existCount['count']) $this->deleteSubscriber();
        else return;
        
        WCF::getCache()->clear(WCF_DIR.'cache/', 'cache.newsletter-subscriber-'.PACKAGE_ID.'.php', true);
    }
    
    /**
     * Sends a validation email.
     */
    protected function sendValidationEmail() {
        //save activation token into database
        $token = StringUtil::getRandomID();
        $sql = 'INSERT INTO wcf'.WCF_N.'_newsletter_activation
        		(userID, token)
        			VALUES
        		('.intval(WCF::getUser()->userID).", '".
                escapeString($token)."')";
        WCF::getDB()->sendQuery($sql);
        
        $url = PAGE_URL.'/index.php?action=NewsletterActivate&id='.WCF::getUser()->userID.'&t='.$token;
        
        $subject = WCF::getLanguage()->get('wcf.acp.newsletter.optin.subject');
        $content = WCF::getLanguage()->getDynamicVariable('wcf.acp.newsletter.optin.text', array(
            'username' => WCF::getUser()->username,
            'url' => $url
        ));
        WCF::getTPL()->assign(array(
            'subject' => $subject,
            'content' => $content
        ));
        $output = WCF::getTPL()->fetch('validationEmail');
        $mail = new Mail(WCF::getUser()->email, $subject, $output, MESSAGE_NEWSLETTERSYSTEM_GENERAL_FROM);
        $mail->setContentType('text/html');
        $mail->send();
    }
    
    /**
     * Deletes this user from the subscriber table.
     */
    protected function deleteSubscriber() {
        $sql = 'DELETE FROM wcf'.WCF_N.'_'.$this->subscriberTable.'
        		WHERE userID = '.intval(WCF::getUser()->userID);
        WCF::getDB()->sendQuery($sql);
        
        $sql = 'DELETE FROM wcf'.WCF_N.'_'.$this->activationTable.'
        		WHERE userID = '.intval(WCF::getUser()->userID);
        WCF::getDB()->sendQuery($sql);
    }
}
