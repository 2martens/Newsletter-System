<?php
//wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');
require_once(WCF_DIR.'lib/data/mail/Mail.class.php');

/**
 * Sends a specified newsletter.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage acp.action
 * @category Community Framework
 */
class SendNewsletterAction extends AbstractAction {
    
    /**
     * Contains the newsletter id.
     * @var int
     */
    protected $newsletterID = 0;
    
    /**
     * Contains a list of all newsletters.
     * @var string
     */
    protected $newsletterList = array();
    
    /**
     * Contains a list of all newsletters which have to be sended.
     * @var array
     */
    protected $outstandingNewsletters = array();
    
    /**
     * Contains a list of all subscribers.
     * @var array
     */
    protected $subscribersList = array();
    
    /**
     * @see AbstractSecureAction::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        if (isset($_GET['id'])) $this->newsletterID = intval($_GET['id']);
    }
    
    public function execute() {
        parent::execute();
        $this->readNewsletters();
        $this->readSubscribers();
        if (!$this->newsletterID) {
            $this->checkNewsletters();
        } else {
            $this->outstandingNewsletters[$this->newsletterID] = $this->newsletterList[$this->newsletterID];
        }
        $this->sendNewsletters();
    }
    
 	/**
     * Sends the newsletters.
     */
    protected function sendNewsletters() {
        $templateName = 'newsletterMail';
        
        //Sends mail to all subscribers. They're listes as bcc to protect their privacy.
        foreach ($this->outstandingNewsletters as $newsletter) {
            $text = $newsletter['text'];
            WCF::getTPL()->assign('text', $text);
            $content = WCF::getTPL()->fetch($templateName);
            $mail = new Mail(MAIL_ADMIN_ADDRESS, $newsletter['subject'], $content,
                MESSAGE_NEWSLETTERSYSTEM_GENERAL_FROM);
            foreach ($this->subscribersList as $subscriber) {
                $email = $subscriber['email'];
                $mail->addBCC($email);
            }
            $mail->send();
        }
    }
    
    /**
     * Reads the newsletters.
     */
    protected function readNewsletters() {
        //add cache resource
        $cacheName = 'newsletter-list-'.PACKAGE_ID;
        WCF::getCache()->addResource($cacheName, WCF_DIR.'cache/cache.'.$cacheName.'.php', WCF_DIR.'lib/system/cache/CacheBuilderNewsletter.class.php');
        
        //get options
        $this->newsletterList = WCF::getCache()->get($cacheName, 'newsletter');
    }
    
    /**
     * Reads the subscribers.
     */
    protected function readSubscribers() {
        //add cache resource
        $cacheName = 'newsletter-subscriber-'.PACKAGE_ID;
        WCF::getCache()->addResource($cacheName, WCF_DIR.'cache/cache.'.$cacheName.'.php', WCF_DIR.'lib/system/cache/CacheBuilderNewsletterSubscriber.class.php');
        
        //get options
        $this->subscribersList = WCF::getCache()->get($cacheName, 'subscribers');
    }
    
    /**
     * Checks the newsletters for time of delivery.
     */
    protected function checkNewsletters() {
        foreach ($this->newsletterList as $id => $newsletter) {
            $date = date('Y-m-d', $newsletter['deliveryTime']);
            $now = date('Y-m-d', TIME_NOW);
            if ($date == $now) {
                $this->outstandingNewsletters[$id] = $newsletter;
            }
        }
    }
}
