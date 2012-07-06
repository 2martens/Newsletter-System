<?php
//wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');
require_once(WCF_DIR.'lib/data/message/newsletter/subscriber/NewsletterSubscriber.class.php');
require_once(WCF_DIR.'lib/data/user/UserEditor.class.php');

/**
 * Deletes a subscriber.
 *
 * @author Jim Martens
 * @copyright 2012 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage acp.action
 * @category Community Framework
 */
class NewsletterSubscriberDeleteAction extends AbstractSecureAction {
    /**
     * Contains the id of the specific subscriber.
     * @var int
     */
    protected $subscriberID = 0;
    
    /**
     * Contains the name of the subscriber database table.
     * @var string
     */
    protected $subscriberTable = 'newsletter_subscriber';
    
    /**
     * Contains the name of the activation database table.
     * @var string
     */
    protected $activationTable = 'newsletter_activation';
    
    /**
     * Contains the name of the guest activation database table.
     * @var string
     */
    protected $guestActivationTable = 'newsletter_guest_activation';
    
    /**
     * Contains the name of the unsubscription database table.
     * @var string
     */
    protected $unsubscriptionTable = 'newsletter_unsubscription';
    
    /**
     * @see AbstractSecureAction::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        if (isset($_GET['subscriberID'])) $this->subscriberID = intval($_GET['subscriberID']);
    }
    
    /**
     * @see AbstractAction::execute()
     */
    public function execute() {
        parent::execute();
        $subscriber = new NewsletterSubscriber($this->subscriberID);
        
        //deletes user subscribers
        $sql = 'DELETE FROM wcf'.WCF_N.'_'.$this->activationTable.'
        		WHERE userID = '.intval($subscriber->userID);
        WCF::getDB()->sendQuery($sql);
        
        //resets user setting
        $user = new UserEditor($subscriber->userID);
        $options = array(
            'acceptNewsletter' => 0
        );
        $user->updateOptions($options);
        
        //deletes guest subscribers
        $sql = 'DELETE FROM wcf'.WCF_N.'_'.$this->guestActivationTable.'
        		WHERE subscriberID = '.$this->subscriberID;
        WCF::getDB()->sendQuery($sql);
        
        //deletes unsubscribe tokens
        $sql = 'DELETE FROM wcf'.WCF_N.'_'.$this->unsubscriptionTable.'
                WHERE subscriberID = '.$this->subscriberID;
        WCF::getDB()->sendQuery($sql);
        
        $sql = 'DELETE FROM wcf'.WCF_N.'_'.$this->subscriberTable.'
        		WHERE subscriberID = '.$this->subscriberID;
        WCF::getDB()->sendQuery($sql);
        
        $this->executed();
        
        //clear cache
        $cacheName = 'newsletter-subscriber-'.PACKAGE_ID;
        WCF::getCache()->clear(WCF_DIR.'cache/', 'cache.'.$cacheName.'.php');
        HeaderUtil::redirect('index.php?page=NewsletterSubscriberList&result=success&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
        exit;
    }
}
