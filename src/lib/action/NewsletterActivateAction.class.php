<?php
//wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');
require_once(WCF_DIR.'lib/data/user/User.class.php');

/**
 * Activates a specific user.
 *
 * @author Jim Martens
 * @copyright 2012 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage action
 * @category Community Framework
 */
class NewsletterActivateAction extends AbstractAction {
    
    /**
     * Contains the user id.
     * @var int
     */
    protected $userID = 0;
    
    /**
     * Contains the activation token.
     * @var string
     */
    protected $token = '';
    
    /**
     * Contains the activation database table.
     * @var string
     */
    protected $activationTable = 'newsletter_activation';
    
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
        if (isset($_GET['id'])) $this->userID = intval($_GET['id']);
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
        		WHERE userID = '.$this->userID;
        $row = WCF::getDB()->getFirstRow($sql);
        if ($row['count'] != 1) {
            $message = WCF::getLanguage()->get('wcf.acp.newsletter.optin.invalidToken');
            throw new NamedUserException($message);
        }
        
        //validates the user as a subscriber
        $sql = 'UPDATE wcf'.WCF_N.'_'.$this->activationTable."
        		SET token = '', activated = 1
        		WHERE userID = ".$this->userID;
        WCF::getDB()->sendQuery($sql);
        
        $user = new User($this->userID);
        
        $sql = 'INSERT INTO wcf'.WCF_N.'_'.$this->subscriberTable.'
        		(userID, username, email)
        			VALUES
        		('.$this->userID.", '".
                escapeString($user->username)."', '".
                escapeString($user->email)."')";
        WCF::getDB()->sendQuery($sql);
        
        //clears cache
        WCF::getCache()->clear(WCF_DIR.'cache/', 'cache.newsletter-subscriber-'.PACKAGE_ID.'.php', true);
        
        $this->executed();
        WCF::getTPL()->assign(array(
        	'message' => WCF::getLanguage()->get('wcf.acp.newsletter.optin.activationSuccess'),
            'url' => PAGE_URL.'/index.php?page=Index'.SID_ARG_2ND
        ));
        WCF::getTPL()->display('redirect');
        exit;
    }
    
}