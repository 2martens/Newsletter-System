<?php
//wcf imports
require_once(WCF_DIR.'lib/acp/form/ACPForm.class.php');

/**
 * Shows the write newsletter form.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage acp.form
 * @category Community Framework
 */
class WriteNewsletterForm extends ACPForm {
    public $activeMenuItem = '';
    
    /**
     * Contains the subject of the newsletter.
     * @var string
     */
    protected $subject = '';
    
    /**
     * Contains the content of the newsletter.
     * @var string
     */
    protected $text = '';
    
    /**
     * @see AbstractForm::readFormParameters()
     */
    public function readFormParameters() {
        parent::readFormParameters();
        if (isset($_POST['subject'])) $this->subject = StringUtil::trim($_POST['subject']);
        if (isset($_POST['content'])) $this->text = StringUtil::trim($_POST['content']);
    }
    
    /**
     * @see AbstractForm::validate()
     */
    public function validate() {
        parent::validate();
        $this->validateSubject();
        $this->validateText();
    }
    
    /**
     * @see AbstractForm::save()
     */
    public function save() {
        parent::save();
        //TODO: implement save method
        $this->saved();
    }
    
    /**
     * @see AbstractForm::assignVariables()
     */
    public function assignVariables() {
        WCF::getTPL()->assign(array(
            'subject' => $this->subject,
            'text' => $this->text
        ));
    }
}
