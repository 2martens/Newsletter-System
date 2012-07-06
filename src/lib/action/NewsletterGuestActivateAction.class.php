<?php
//wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');

/**
 * Manages the activation of guests.
 *
 * @author Jim Martens
 * @copyright 2012 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage action
 * @category Community Framework
 */
class NewsletterGuestActivateAction extends AbstractAction {
    
    /**
     * Contains the guest activation database table.
     * @var string
     */
    protected $activationTable = 'newsletter_guest_activation';
    
    /**
     * Contains the subscriber database table.
     * @var string
     */
    protected $subscriberTable = 'newsletter_subscriber';
    
    /**
     * Contains the unsubscription database table.
     * @var string
     */
    protected $unsubscriptionTable = 'newsletter_unsubscription';
    
    /**
     * Contains the subscriber id.
     * @var int
     */
    protected $subscriberID = 0;
    
    /**
     * Contains the activation token.
     * @var string
     */
    protected $token = '';
    
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
        		FROM wcf'.WCF_N.'_'.$this->activationTable.'
        		WHERE subscriberID = '.$this->subscriberID."
        			AND token = '".escapeString($this->token)."'";
        $row = WCF::getDB()->getFirstRow($sql);
        if ($row['count'] != 1) {
            $message = WCF::getLanguage()->get('wcf.acp.newsletter.optin.invalidToken');
            throw new NamedUserException($message);
        }
        
        //get ip address and convert it into a long
        $ipAddress = ip2long(StringUtil::trim($_SERVER['REMOTE_ADDR']));
        
        //validates the user as a subscriber
        $sql = 'UPDATE wcf'.WCF_N.'_'.$this->activationTable."
        		SET token = '', datetime = ".TIME_NOW.',
        		ip = '.$ipAddress.', activated = 1
        		WHERE subscriberID = '.$this->subscriberID;
        WCF::getDB()->sendQuery($sql);
        
        //checks if there is already a unsubscribe token
        $sqlCheck = 'SELECT COUNT(token) AS count
        			FROM wcf'.WCF_N.'_'.$this->unsubscriptionTable.'
        			WHERE subscriberID = '.intval($this->subscriberID);
        $row = WCF::getDB()->getFirstRow($sqlCheck);
        if (!intval($row['count']))	{
            //adds an unsubscribe token
            $sql = 'INSERT INTO wcf'.WCF_N.'_'.$this->unsubscriptionTable.'
        				(subscriberID, token)
        			VALUES
        				('.intval($this->subscriberID).", '".
                        escapeString(StringUtil::getRandomID())."')";
            WCF::getDB()->sendQuery($sql);
        }
        $this->executed();
        WCF::getTPL()->assign(array(
        	'message' => WCF::getLanguage()->get('wcf.acp.newsletter.optin.activationSuccess'),
            'url' => PAGE_URL.'/index.php?page=Index'.SID_ARG_2ND
        ));
        WCF::getTPL()->display('redirect');
        exit;
    }
}
