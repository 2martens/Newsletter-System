<?php
//wcf imports
require_once(WCF_DIR.'lib/data/mail/Mail.class.php');
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Handles the newsletter subscription during registration.
 *
 * @author Jim Martens
 * @copyright 2012 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage system.event.listener
 * @category Community Framework
 */
class RegisterNewsletterListener implements EventListener {
    
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
     * Contains the template name.
     * @var string
     */
    protected $templateName = 'registerNewsletter';
    
    /**
     * If true, the user accepts the newsletter.
     * @var boolean
     */
    protected $acceptNewsletter = false;
    
    /**
     * If true, the user wants to get newsletters via email.
     * @var boolean
     */
    protected $acceptNewsletterAsEmail = true;
    
    /**
     * If true, the user wants to get newsletters via pm.
     * @var boolean
     */
    protected $acceptNewsletterAsPM = false;
    
    /**
     * @see EventListener::execute()
     */
    public function execute($eventObj, $className, $eventName) {
        if ($className != 'RegisterForm') return;
        $this->{$eventName}($eventObj);
    }
    
    /**
     * Read form parameters.
     */
    protected function readFormParameters() {
        if (isset($_POST['acceptNewsletter'])) $this->acceptNewsletter = true;
        if (isset($_POST['acceptNewsletterAsEmail'])) $this->acceptNewsletterAsEmail = true;
        elseif (count($_POST) && !isset($_POST['acceptNewsletterAsEmail'])) $this->acceptNewsletterAsEmail = false;
        
        if (isset($_POST['acceptNewsletterAsPM'])) $this->acceptNewsletterAsPM = true;
    }
    
    /**
     * Validates the input.
     *
     * @throws UserInputException
     */
    protected function validate() {
        $this->readFormParameters();
        if ($this->acceptNewsletter && !$this->acceptNewsletterAsEmail && !$this->acceptNewsletterAsPM) {
			$this->acceptNewsletter = $this->acceptNewsletterAsEmail = $this->acceptNewsletterAsPM = false;
		}
		elseif (!$this->acceptNewsletter) {
            $this->acceptNewsletterAsEmail = $this->acceptNewsletterAsPM = false;
        }
    }
    
    /**
     * Saves the decision.
     *
     * @param object $eventObj
     */
    protected function saved($eventObj) {
        $this->validate();
        $editor = $eventObj->user->getEditor();
        $options = array(
            'acceptNewsletter' => intval($this->acceptNewsletter),
            'acceptNewsletterAsEmail' => intval($this->acceptNewsletterAsEmail),
            'acceptNewsletterAsPM' => intval($this->acceptNewsletterAsPM)
        );
        $editor->updateOptions($options);
        if ($this->acceptNewsletter) {
            $this->sendValidationEmail($eventObj);
        }
    }
    
    /**
     * Assigns necessary variables.
     *
     * @param object $eventObj
     */
    protected function assignVariables($eventObj) {
        $this->readFormParameters();
        WCF::getTPL()->assign(array(
        	'acceptNewsletter' => $this->acceptNewsletter,
            'acceptNewsletterAsEmail' => $this->acceptNewsletterAsEmail,
            'acceptNewsletterAsPM' => $this->acceptNewsletterAsPM,
            'errorField' => $eventObj->errorField,
            'errorType' => $eventObj->errorType
        ));
        $content = WCF::getTPL()->fetch($this->templateName);
        WCF::getTPL()->append('additionalFields', $content);
    }
    
	/**
     * Sends a validation email.
     *
     * @param object $eventObj
     */
    protected function sendValidationEmail($eventObj) {
        //save activation token into database
        $token = StringUtil::getRandomID();
        $sql = 'INSERT INTO wcf'.WCF_N.'_'.$this->activationTable.'
        		(userID, token)
        			VALUES
        		('.intval($eventObj->user->userID).", '".
                escapeString($token)."')";
        WCF::getDB()->sendQuery($sql);
        
        $url = PAGE_URL.'/index.php?action=NewsletterActivate&id='.$eventObj->user->userID.'&t='.$token;
        
        $subject = WCF::getLanguage()->get('wcf.acp.newsletter.optin.subject');
        $content = WCF::getLanguage()->getDynamicVariable('wcf.acp.newsletter.optin.text', array(
            'username' => $eventObj->user->username,
            'url' => $url
        ));
        WCF::getTPL()->assign(array(
            'subject' => $subject,
            'content' => $content
        ));
        $output = WCF::getTPL()->fetch('validationEmail');
        $mail = new Mail($eventObj->user->email, $subject, $output, MESSAGE_NEWSLETTERSYSTEM_GENERAL_FROM);
        $mail->setContentType('text/html');
        $mail->send();
    }
}
