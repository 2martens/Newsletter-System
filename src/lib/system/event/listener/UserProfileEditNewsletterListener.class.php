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
        
        $cacheName = 'newsletter-subscriber-'.PACKAGE_ID;
        $cacheFile = WCF_DIR.'lib/system/cache/CacheBuilderNewsletterSubscriber.class.php';
        $cacheResource = array(
			'cache' => $cacheName,
			'file' => WCF_DIR.'cache/cache.'.$cacheName.'.php',
			'className' => StringUtil::getClassName($cacheFile),
			'classFile' => $cacheFile,
			'minLifetime' => 0,
			'maxLifetime' => 0
		);
        WCF::getCache()->rebuild($cacheResource);
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
        
        $url = PAGE_URL.'/index.php?action=NewsletterActivate&amp;id='.WCF::getUser()->userID.'&amp;token='.$token;
        
        $subject = WCF::getLanguage()->get('wcf.acp.newsletter.optin.subject');
        $content = WCF::getLanguage()->getDynamicVariable('wcf.acp.newsletter.optin.text', array(
            'username' => WCF::getUser()->username,
            'url' => $url
        ));
        $mail = new Mail(WCF::getUser()->email, $subject, $content, MESSAGE_NEWSLETTERSYSTEM_GENERAL_FROM);
        $mail->send();
    }
    
    /**
     * Deletes this user from the subscriber table.
     */
    protected function deleteSubscriber() {
        $sql = 'DELETE FROM wcf'.WCF_N.'_'.$this->subscriberTable.'
        		WHERE userID = '.intval(WCF::getUser()->userID);
        WCF::getDB()->sendQuery($sql);
    }
}
