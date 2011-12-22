<?php
//wcf imports
require_once(WCF_DIR.'lib/data/cronjobs/Cronjob.class.php');
require_once(WCF_DIR.'lib/data/mail/Mail.class.php');

/**
 * Sends the newsletters.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage system.cronjob
 * @category Community Framework
 */
class SendNewsletterCronjob implements Cronjob {
    
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
     * Contains the name of the database table.
     * @var string
     */
    protected $databaseTable = 'newsletter';
    
    /**
     * @see Cronjob::execute()
     */
    public function execute($data) {
        parent::execute($data);
        $this->readNewsletters();
        $this->readSubscribers();
        $this->checkNewsletters();
        $this->sendNewsletters();
    }
    
    /**
     * Sends the newsletters.
     */
    protected function sendNewsletters() {
        $templateName = 'newsletterMail';
        WCF::getTPL()->assign('signature', MESSAGE_NEWSLETTERSYSTEM_GENERAL_SIGNATURE);
        
        //Sends mail to all subscribers. They're listes as bcc to protect their privacy.
        foreach ($outstandingNewsletters as $newsletter) {
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