<?php
//wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');
require_once(WCF_DIR.'lib/data/mail/Mail.class.php');
require_once(WCF_DIR.'lib/data/user/User.class.php');
require_once(WCF_DIR.'lib/data/message/newsletter/ViewableNewsletter.class.php');
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
     * @var array
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
     * Contains a list of all unsubscribe tokens.
     * @var array
     */
    protected $unsubscribeTokens = array();
    
    /**
     * If true, the action was called by the hourly cronjob.
     * @var false
     */
    protected $hourly = false;
    
    /**
     * Contains the unsubscription table name.
     * @var string
     */
    protected $unsubscriptionTable = 'newsletter_unsubscription';
    
    /**
     * Creates a new SendNewsletterAction object.
     *
     * @param boolean $hourly
     *
     * @see AbstractAction::__construct()
     */
    public function __construct($hourly = false) {
        $this->hourly = $hourly;
        //makes sure that bbcodes cache resource exists
        WCF::getCache()->addResource('bbcodes', WCF_DIR.'cache/cache.bbcodes.php', WCF_DIR.'lib/system/cache/CacheBuilderBBCodes.class.php');
        WCF::getCache()->addResource('smileys', WCF_DIR.'cache/cache.smileys.php', WCF_DIR.'lib/system/cache/CacheBuilderSmileys.class.php');
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
            HeaderUtil::redirect('index.php?page=NewsletterList&success&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
            exit;
        }
    }
    
 	/**
     * Sends the newsletters.
     */
    protected function sendNewsletters() {
        $templateName = 'newsletterMail';
        
        //Sends mail to all subscribers.
        foreach ($this->outstandingNewsletters as $id => $newsletter) {
            $text = $newsletter['text'];
            
            //workaround to make sure that the template is found
            $templatePaths = array(
                WCF_DIR.'templates/',
                WCF_DIR.'acp/templates/'
            );
            WCF::getTPL()->setTemplatePaths($templatePaths);
            
            $newsletterObj = new ViewableNewsletter($id);
            $emailText = $newsletterObj->getFormattedMessage();
            WCF::getTPL()->assign(array(
                'subject' => $newsletter['subject'],
            	'text' => $emailText
            ));
            $content = WCF::getTPL()->fetch($templateName);
            $i = 0;
            usleep(1);
            //sending one mail per subscriber
            //is longer, but safer
            foreach ($this->subscribersList as $subscriber) {
                //sleep 2 seconds after 10 sent mails
                if (fmod($i, 10) == 0) {
                    usleep(2000000);
                }
                $unsubscribeToken = '';
                if (!isset($this->unsubscribeTokens[$subscriber['subscriberID']])) {
                	$unsubscribeToken = StringUtil::getRandomID();
                	$sql = 'INSERT INTO wcf'.WCF_N.'_'.$this->unsubscriptionTable.'
                			(subscriberID, token)
                		VALUES
                			('.intval($subscriber['subscriberID']).", '".
        						escapeString($unsubscribeToken)."')";
                	WCF::getDB()->sendQuery($sql);
                }
                else {
                	$unsubscribeToken = $this->unsubscribeTokens[$subscriber['subscriberID']]['token'];
                }
                
                $recipient = null;
                if ($subscriber['userID']) {
                	$recipient = new User($subscriber['userID']);
                	
                	// check for non receiving groups
                	if (!NewsletterUtil::canReceiveNewsletters($recipient)) {
                		continue;
                	}
                }
                
                
                // {$username} stands for the username of the specific subscriber
                if (is_null($recipient) || $recipient->getUserOption('acceptNewsletterAsEmail')) {
                    $tmpContent = str_replace('{$username}', $subscriber['username'], $content);
                    $tmpContent = str_replace('subscriberID', $subscriber['subscriberID'], $tmpContent);
                    $tmpContent = str_replace('token', $unsubscribeToken, $tmpContent);
                    $email = $subscriber['email'];
                    $mail = new Mail($email, $newsletter['subject'], $tmpContent,
                    MESSAGE_NEWSLETTERSYSTEM_GENERAL_FROM);
                    //$mail->addBCC(MAIL_ADMIN_ADDRESS); would result in x mails
                    $mail->setContentType('text/html');
                    $mail->send();
                }
                if (!is_null($recipient) && $recipient->getUserOption('acceptNewsletterAsPM')) {
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
                    $tmpText = str_replace('{$username}', $subscriber['username'], $text);
                    $pm = PMEditor::create(false, $recipientArray, array(), $newsletter['subject'], $tmpText, $admin->userID, $admin->username, $options);
                }
                $i++;
            }
        }
        WCF::getCache()->clearResource('newsletter-subscriber-'.PACKAGE_ID);
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
        $this->unsubscribeTokens = WCF::getCache()->get($cacheName, 'unsubscribeTokens');
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
