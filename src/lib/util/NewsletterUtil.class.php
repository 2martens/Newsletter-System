<?php
//wcf imports
require_once(WCF_DIR.'lib/data/mail/Mail.class.php');
require_once(WCF_DIR.'lib/data/user/User.class.php');
require_once(WCF_DIR.'lib/data/message/newsletter/subscriber/NewsletterSubscriber.class.php');

/**
 * Provides helpful functions for the Newsletter System.
 *
 * @author Jim Martens
 * @copyright 2012 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage util
 * @category Community Framework
 */
class NewsletterUtil {
	/**
	 * Contains the activation database table.
	 * @var string
	 */
    protected static $activationTable = 'newsletter_activation';
    
    /**
     * Contains the guest activation database table.
     * @var string
     */
    protected static $activationGuestTable = 'newsletter_guest_activation';
	
	/**
     * Sends a user validation email.
     *
     * @param User $user
     */
    public static function sendUserValidationEmail(User $user = null) {
        //get user object
        if (null === $user) {
            $user = WCF::getUser();
        }
        
        //check if an activation has already been started
        $sqlCheck = 'SELECT COUNT(token) AS count
        			FROM wcf'.WCF_N.'_'.self::$activationTable.'
        			WHERE userID = '.intval($user->userID);
        $row = WCF::getDB()->getFirstRow($sqlCheck);
        if (intval($row['count'])) {
            $sqlDelete = 'DELETE FROM wcf'.WCF_N.'_'.self::$activationTable.'
            			WHERE userID = '.intval($user->userID);
            WCF::getDB()->sendQuery($sqlDelete);
        }
        //save activation token into database
        $token = StringUtil::getRandomID();
        $sql = 'INSERT INTO wcf'.WCF_N.'_'.self::$activationTable.'
        		(userID, token)
        			VALUES
        		('.intval($user->userID).", '".
                escapeString($token)."')";
        WCF::getDB()->sendQuery($sql);
        
        $url = PAGE_URL.'/index.php?action=NewsletterActivate&id='.$user->userID.'&t='.$token;
        
        $subject = WCF::getLanguage()->get('wcf.acp.newsletter.optin.subject');
        $content = WCF::getLanguage()->getDynamicVariable('wcf.acp.newsletter.optin.text', array(
            'username' => $user->username,
            'url' => $url
        ));
        WCF::getTPL()->assign(array(
            'subject' => $subject,
            'content' => $content
        ));
        $templatePathsOrig = WCF::getTPL()->getTemplatePaths();
        $templatePaths = array(WCF_DIR.'templates/');
        WCF::getTPL()->setTemplatePaths(array_merge($templatePathsOrig, $templatePaths));
        $output = WCF::getTPL()->fetch('validationEmail');
        $mail = new Mail($user->email, $subject, $output, MESSAGE_NEWSLETTERSYSTEM_GENERAL_FROM);
        $mail->setContentType('text/html');
        $mail->send();
    }
    
	/**
     * Sends a guest validation email.
     *
     * @param NewsletterSubscriber $subscriber
     */
    public static function sendGuestValidationEmail(NewsletterSubscriber $subscriber) {
        
        //check if an activation has already been started
        $sqlCheck = 'SELECT COUNT(token) AS count
        			FROM wcf'.WCF_N.'_'.self::$activationGuestTable.'
        			WHERE subscriberID = '.intval($subscriber->subscriberID);
        $row = WCF::getDB()->getFirstRow($sqlCheck);
        if (intval($row['count'])) {
            $sqlDelete = 'DELETE FROM wcf'.WCF_N.'_'.self::$activationGuestTable.'
            			WHERE subscriberID = '.intval($subscriber->subscriberID);
            WCF::getDB()->sendQuery($sqlDelete);
        }
        //save activation token into database
        $token = StringUtil::getRandomID();
        $sql = 'INSERT INTO wcf'.WCF_N.'_'.self::$activationGuestTable.'
        		(subscriberID, token)
        			VALUES
        		('.intval($subscriber->subscriberID).", '".
                escapeString($token)."')";
        WCF::getDB()->sendQuery($sql);
        
        $url = PAGE_URL.'/index.php?action=NewsletterGuestActivate&id='.$subscriber->subscriberID.'&t='.$token;
        
        $subject = WCF::getLanguage()->get('wcf.acp.newsletter.optin.subject');
        $content = WCF::getLanguage()->getDynamicVariable('wcf.acp.newsletter.optin.text', array(
            'username' => WCF::getLanguage()->get('wcf.acp.newsletter.optin.hello'),
            'url' => $url
        ));
        WCF::getTPL()->assign(array(
            'subject' => $subject,
            'content' => $content
        ));
        $templatePathsOrig = WCF::getTPL()->getTemplatePaths();
        $templatePaths = array(WCF_DIR.'templates/');
        WCF::getTPL()->setTemplatePaths(array_merge($templatePathsOrig, $templatePaths));
        $output = WCF::getTPL()->fetch('validationEmail');
        $mail = new Mail($subscriber->email, $subject, $output, MESSAGE_NEWSLETTERSYSTEM_GENERAL_FROM);
        $mail->setContentType('text/html');
        $mail->send();
    }
    
    /**
     * Checks whether the given user can receive newsletters or not.
     *
     * @param 	User $user
     * @return 	True, if the user can receive newsletters, false if not.
     */
    public static function canReceiveNewsletters(User $user) {
    	$nonReceivingGroups = explode(',', MESSAGE_NEWSLETTERSYSTEM_GENERAL_NONRECEIVING_GROUPS);
    	$groupIDs = $user->getGroupIDs();
    	
    	foreach ($groupIDs as $groupID) {
    		if (in_array($groupID, $nonReceivingGroups)) {
    			return false;
    		}
    	}
    	return true;
    }
}
