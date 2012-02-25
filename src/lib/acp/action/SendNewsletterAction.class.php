<?php
//wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');
require_once(WCF_DIR.'lib/data/mail/Mail.class.php');
require_once(WCF_DIR.'lib/data/user/User.class.php');
require_once(WCF_DIR.'lib/data/message/pm/PMEditor.class.php');

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
     * If true, the action was called by the hourly cronjob.
     * @var false
     */
    protected $hourly = false;
    
    /**
     * Creates a new SendNewsletterAction object.
     *
     * @param boolean $hourly
     *
     * @see AbstractAction::__construct()
     */
    public function __construct($hourly = false) {
        $this->hourly = $hourly;
        parent::__construct();
    }
    
    /**
     * @see AbstractSecureAction::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        if (isset($_GET['id'])) $this->newsletterID = intval($_GET['id']);
    }
    
    /**
     * @see AbstractAction::execute()
     */
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
        if ($this->newsletterID) {
            HeaderUtil::redirect('index.php?page=NewsletterList&success'.SID_ARG_2ND);
            exit;
        }
    }
    
 	/**
     * Sends the newsletters.
     */
    protected function sendNewsletters() {
        $templateName = 'newsletterMail';
        
        //Sends mail to all subscribers. They're listes as bcc to protect their privacy.
        foreach ($this->outstandingNewsletters as $id => $newsletter) {
            $text = $newsletter['text'];
            //workaround to make sure that the template is found
            $templatePaths = array(
                WCF_DIR.'templates/',
                WCF_DIR.'acp/templates/'
            );
            WCF::getTPL()->setTemplatePaths($templatePaths);
            
            WCF::getTPL()->assign(array(
                'subject' => $newsletter['subject'],
            	'text' => $text
            ));
            $content = WCF::getTPL()->fetch($templateName);
            
            //sending one mail per subscriber
            //is longer, but safer
            foreach ($this->subscribersList as $subscriber) {
                $recipient = new User($subscriber['userID']);
                // {$username} stands for the username of the specific subscriber
                $content = str_replace('{$username}', $subscriber['username'], $content);
                if ($recipient->getUserOption('acceptNewsletterAsEmail')) {
                    $email = $subscriber['email'];
                    $mail = new Mail($email, $newsletter['subject'], $content,
                    MESSAGE_NEWSLETTERSYSTEM_GENERAL_FROM);
                    //$mail->addBCC(MAIL_ADMIN_ADDRESS); would result in x mails
                    $mail->setContentType('text/html');
                    $mail->send();
                }
                if ($recipient->getUserOption('acceptNewsletterAsPM')) {
                    $recipientArray = array();
                    $recipientArray[] = array(
                        'userID' => $subscriber['userID'],
                        'username' => $subscriber['username']
                    );
                    $admin = new User(MESSAGE_NEWSLETTERSYSTEM_GENERAL_ADMIN);
                    $options = array(
                        'enableSmilies' => $newsletter['enableSmilies'],
                        'enableHtml' => $newsletter['enableHtml'],
                        'enableBBCodes' => $newsletter['enableBBCodes']
                    );
                    $pm = PMEditor::create(false, $recipientArray, array(), $newsletter['subject'], $text, $admin->userID, $admin->username, $options);
                }
            }
        }
    }
    
    /**
     * Reads the newsletters.
     */
    protected function readNewsletters() {
        //add cache resource
        $cacheName = 'newsletter-'.PACKAGE_ID;
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
            
            $date = date('Y-m-d'.($this->hourly ? ' H' : ''), $newsletter['deliveryTime']);
            $now = date('Y-m-d'.($this->hourly ? ' H' : ''), TIME_NOW);
            if ($date == $now) {
                $this->outstandingNewsletters[$id] = $newsletter;
            }
        }
    }
}
