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
     * Contains the redirect url.
     * @var string
     */
    protected $url = '';
    
    /**
     * @see Action::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        if (isset($_GET['id'])) $this->userID = intval($_GET['id']);
        if (isset($_GET['token'])) $this->token = StringUtil::trim($_GET['token']);
        if (isset($_GET['url'])) $this->url = StringUtil::trim($_GET['url']);
    }
    
    /**
     * @see Action::execute()
     */
    public function execute() {
        parent::execute();
        
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
        
        $this->executed();
        
        HeaderUtil::redirect($this->url);
        exit;
    }
    
}
