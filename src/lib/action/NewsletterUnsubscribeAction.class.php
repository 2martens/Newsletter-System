<?php
//wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');
require_once(WCF_DIR.'lib/data/message/newsletter/subscriber/NewsletterSubscriber.class.php');
require_once(WCF_DIR.'lib/data/user/User.class.php');

/**
 * Unsubscribes the given subscriber.
 *
 * @author Jim Martens
 * @copyright 2012 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage action
 * @category Community Framework
 */
class NewsletterUnsubscribeAction extends AbstractAction {
    
    /**
     * Contains the subscriber id.
     * @var int
     */
    protected $subscriberID = 0;
    
    /**
     * Contains the token.
     * @var string
     */
    protected $token = '';
    
    /**
     * Contains the unsubscription database table name.
     * @var string
     */
    protected $unsubscriptionTable = 'newsletter_unsubscription';
    
    /**
     * Contains the activation database table.
     * @var string
     */
    protected $activationTable = 'newsletter_activation';
    
    /**
     * Contains the guest activation database table.
     * @var string
     */
    protected $guestActivationTable = 'newsletter_guest_activation';
    
    /**
     * Contains the subscriber database table.
     * @var string
     */
    protected $subscriberTable = 'newsletter_subscriber';
    
    /**
     * @see Action::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        if (isset($_GET['id'])) $this->subscriberID = intval($_GET['id']);
        if (isset($_GET['t'])) $this->token = StringUtil::trim($_GET['t']);
    }
    
    /**
     * @see Action::execute()
     */
    public function execute() {
        parent::execute();
        
        //validates the given token to avoid misusing
        $sql = 'SELECT COUNT(token) AS count
        		FROM wcf'.WCF_N.'_'.$this->unsubscriptionTable.'
        		WHERE subscriberID = '.$this->subscriberID."
        			AND token = '".escapeString($this->token)."'";
        $row = WCF::getDB()->getFirstRow($sql);
        if ($row['count'] != 1) {
            $message = WCF::getLanguage()->get('wcf.acp.newsletter.optin.invalidToken');
            throw new NamedUserException($message);
        }
        
        $subscriber = new NewsletterSubscriber($this->subscriberID);
        
        if ($subscriber->userID != 0) {
            $user = new User($subscriber->userID);
            $editor = $user->getEditor();
            $options = array(
            	'acceptNewsletter' => 0
            );
            $editor->updateOptions($options);
        }
        
        //delete subscriber related entries
        $sql = 'DELETE FROM wcf'.WCF_N.'_'.$this->unsubscriptionTable.'
        		WHERE subscriberID = '.$this->subscriberID."
        			AND token = '".escapeString($this->token)."'";
        WCF::getDB()->sendQuery($sql);
        
        $sql = 'DELETE FROM wcf'.WCF_N.'_'.$this->activationTable.'
        		WHERE userID = '.intval($subscriber->userID);
        WCF::getDB()->sendQuery($sql);
        
        $sql = 'DELETE FROM wcf'.WCF_N.'_'.$this->guestActivationTable.'
        		WHERE subscriberID = '.$this->subscriberID;
        WCF::getDB()->sendQuery($sql);
        
        unset($subscriber);
        
        $sql = 'DELETE FROM wcf'.WCF_N.'_'.$this->subscriberTable.'
        		WHERE subscriberID = '.$this->subscriberID;
        WCF::getDB()->sendQuery($sql);
        
        $this->executed();
        WCF::getTPL()->assign(array(
        	'message' => WCF::getLanguage()->get('wcf.acp.newsletter.unsubscription.success'),
            'url' => PAGE_URL.'/index.php?page=Index'.SID_ARG_2ND
        ));
        WCF::getTPL()->display('redirect');
        exit;
    }
}
