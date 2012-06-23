<?php
//wcf imports
require_once(WCF_DIR.'lib/acp/form/ACPForm.class.php');
require_once(WCF_DIR.'lib/data/user/User.class.php');

/**
 * Offers a form for sending a validation email.
 *
 * @author Jim Martens
 * @copyright 2012 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage acp.form
 * @category Community Framework
 */
class SendValidationEmailForm extends ACPForm {
    
    /**
     * Contains the username.
     * @var string
     */
    public $username = '';
    
    /**
     * Contains the user object.
     * @var User
     */
    public $user = null;
    
    /**
     * @see ACPForm::$activeMenuItem
     */
    public $activeMenuItem = 'wcf.acp.menu.link.content.newslettersystem.sendValidationEmail';
    
    /**
     * @see AbstractPage::$templateName
     */
    public $templateName = 'sendValidationEmail';
    
    /**
     * @see Form::readFormParameters()
     */
    public function readFormParameters() {
        parent::readFormParameters();
        if (isset($_POST['username'])) $this->username = StringUtil::trim($_POST['username']);
    }
    
    /**
     * @see Form::validate()
     */
    public function validate() {
        parent::validate();
        if (empty($this->username)) {
            throw new UserInputException('username');
        }
        $sql = 'SELECT userID, COUNT(userID) AS count
        		FROM wcf'.WCF_N."_user
        		WHERE username = '".escapeString($this->username)."'
        		GROUP BY userID";
        $row = WCF::getDB()->getFirstRow($sql);
        if (!$row['count']) {
            throw new UserInputException('username', 'notValid');
        }
        $this->user = new User($row['userID']);
    }
    
    /**
     * @see Form::save()
     */
    public function save() {
        parent::save();
        NewsletterUtil::sendUserValidationEmail($this->user);
        $this->saved();
        //resetting variables
        $this->username = '';
        $this->user = null;
        WCF::getTPL()->assign('success', true);
    }
    
    /**
     * @see Form::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        WCF::getTPL()->assign(array(
        	'username' => $this->username
        ));
    }
    
}
