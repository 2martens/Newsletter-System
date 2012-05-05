<?php
//wcf imports
require_once(WCF_DIR.'lib/form/AbstractForm.class.php');
require_once(WCF_DIR.'lib/data/mail/Mail.class.php');

/**
 * Adds a guest for the newsletter.
 *
 * @author Jim Martens
 * @copyright 2012 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage action
 * @category Community Framework
 */
class NewsletterRegisterGuestForm extends AbstractForm {
    
    /**
     * Contains the email.
     * @var string
     */
    protected $email = '';
    
    /**
     * If true, the newsletter subscription is accepted.
     * @var boolean
     */
    protected $checkbox = false;
    
    /**
     * Contains the newsletter subscriber table name.
     * @var string
     */
    protected $subscriberTable = 'newsletter_subscriber';
    
    /**
     * Contains the newsletter guest activation table name.
     * @var string
     */
    protected $activationTable = 'newsletter_guest_activation';
    
    /**
     * @see AbstractPage::$templateName
     */
    public $templateName = 'subscribeNewsletterGuest';
    
    /**
     * @see Form::readFormParameters()
     */
    public function readFormParameters() {
        parent::readFormParameters();
        if (isset($_POST['email'])) $this->email = StringUtil::trim($_POST['email']);
        if (isset($_POST['checkbox'])) $this->checkbox = (boolean) intval($_POST['checkbox']);
    }
    
    /**
     * @see Form::validate()
     */
    public function validate() {
        parent::validate();
        if (empty($this->email)) {
            throw new UserInputException('email');
        }
        
        $sql = 'SELECT COUNT(email) AS count
        		FROM wcf'.WCF_N.'_'.$this->subscriberTable."
        		WHERE email = '".escapeString($this->email)."'";
        $row = WCF::getDB()->getFirstRow($sql);
        if ($row['count']) {
            throw new UserInputException('email', 'notUnique');
        }
        
        if (!$this->checkbox) {
            throw new UserInputException('checkbox', 'notAgreed');
        }
    }
    
    /**
     * @see Form::save()
     */
    public function save() {
        parent::save();
        //save activation token into database
        $token = StringUtil::getRandomID();
        $sql = 'INSERT INTO wcf'.WCF_N.'_'.$this->subscriberTable."
        		(email)
        			VALUES
        		('".escapeString($this->email)."')";
        WCF::getDB()->sendQuery($sql);
        $subscriberID = WCF::getDB()->getInsertID();
        
        //clears cache
        WCF::getCache()->clear(WCF_DIR.'cache/', 'cache.newsletter-subscriber-'.PACKAGE_ID.'.php', true);
                
        $sql = 'INSERT INTO wcf'.WCF_N.'_'.$this->activationTable.'
        		(subscriberID, token)
        			VALUES
        		('.intval($subscriberID).", '".escapeString($token)."')";
        WCF::getDB()->sendQuery($sql);
        
        $url = PAGE_URL.'/index.php?action=NewsletterGuestActivate&id='.$subscriberID.'&t='.$token;
        
        $subject = WCF::getLanguage()->get('wcf.acp.newsletter.optin.subject');
        $content = WCF::getLanguage()->getDynamicVariable('wcf.acp.newsletter.optin.text', array(
            'username' => WCF::getLanguage()->get('wcf.acp.newsletter.optin.hello'),
            'url' => $url
        ));
        WCF::getTPL()->assign(array(
            'subject' => $subject,
            'content' => $content
        ));
        $output = WCF::getTPL()->fetch('validationEmail');
        $mail = new Mail($this->email, $subject, $output, MESSAGE_NEWSLETTERSYSTEM_GENERAL_FROM);
        $mail->setContentType('text/html');
        $mail->send();
        
        $this->saved();
        WCF::getTPL()->assign(array(
        	'message' => WCF::getLanguage()->get('wcf.acp.newsletter.optin.activationPending'),
            'url' => PAGE_URL.'/index.php?page=Index'.SID_ARG_2ND
        ));
        WCF::getTPL()->display('redirect');
        exit;
    }
    
    /**
     * @see Page::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        WCF::getTPL()->assign(array(
            'email' => $this->email,
            'checkbox' => $this->checkbox
        ));
    }
}
