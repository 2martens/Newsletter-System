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
class NewsletterAddForm extends ACPForm {
    public $activeMenuItem = 'wcf.acp.menu.link.content.newsletterSystem.writeNewsletter';
    public $templateName = 'newsletterAdd';
    
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
     * Contains the read date values.
     * @var array<int>
     */
    protected $dateValues = array('day' => 0, 'month' => 0, 'year' => 0);
    
    /**
     * Contains the options to be chosen in the form.
     * @var array
     */
    protected $dateOptions = array('day' => array(), 'month' => array(), 'year' => array());
    
    /**
     * Contains the result of adding or editing a newsletter.
     * @var string
     */
    protected $result = '';
    
    /**
     * @see AbstractForm::readFormParameters()
     */
    public function readFormParameters() {
        parent::readFormParameters();
        if (isset($_POST['subject'])) $this->subject = StringUtil::trim($_POST['subject']);
        if (isset($_POST['content'])) $this->text = StringUtil::trim($_POST['content']);
        if (isset($_POST['day'])) $this->dateValues['day'] = intval($_POST['day']);
        if (isset($_POST['month'])) $this->dateValues['month'] = intval($_POST['month']);
        if (isset($_POST['year'])) $this->dateValues['year'] = intval($_POST['year']);
        if (isset($_REQUEST['result'])) $this->result = StringUtil::trim($_REQUEST['result']);
    }
    
    /**
     * @see AbstractForm::readData()
     */
    public function readData() {
        parent::readData();
        for ($i = 1; $i <= 31; $i++) {
            $this->dateOptions['day'][$i] = (string) $i;
        }
        for ($i = 1; $i <= 12; $i++) {
            $this->dateOptions['month'][$i] = (string) $i;
        }
        for ($i = 2011; $i <= 2038; $i++) {
            $this->dateOptions['year'][$i] = (string) $i;
        }
    }
    
    /**
     * @see AbstractForm::validate()
     */
    public function validate() {
        parent::validate();
        $this->validateSubject();
        $this->validateText();
        $this->validateDate();
    }
    
    /**
     * @see AbstractForm::save()
     */
    public function save() {
        parent::save();
        //create date
        $date = (string) $this->dateValues['year'].'-'.
            (string) $this->dateValues['month'].'-'.
            (string) $this->dateValues['day'];
        //convert date to timestamp
        $unixTime = strtotime($date);
        $newsletter = NewsletterEditor::create(WCF::getUser()->userID, $unixTime,
                    $this->subject, $this->text);
        
        $this->saved();
        HeaderUtil::redirect('index.php?form=NewsletterAdd&result=success');
        exit;
    }
    
    /**
     * @see AbstractForm::assignVariables()
     */
    public function assignVariables() {
        WCF::getTPL()->assign(array(
            'subject' => $this->subject,
            'text' => $this->text,
            'day' => $dateValues['day'],
            'month' => $dateValues['month'],
            'year' => $dateValues['year'],
            'action' => 'add',
            'dateOptions' => $this->dateOptions,
            'result' => $this->result
        ));
    }
    
    /**
     * Validates the subject.
     * @throws UserInputException
     */
    protected function validateSubject() {
        if (empty($this->subject)) {
            throw new UserInputException('subject');
        }
        if (strlen($this->subject) < 4) {
            throw new UserInputException('subject', 'tooShort');
        }
    }
    
    /**
     * Validates the text.
     * @throws UserInputException
     */
    protected function validateText() {
        if (empty($this->text)) {
            throw new UserInputException('text');
        }
    }
    
    /**
     * Validates the date.
     * @throws UserInputException
     */
    protected function validateDate() {
        if (!checkdate($this->dateValues['month'], $this->dateValues['day'], $this->dateValues['year'])) {
            throw new UserInputException('date', 'notValidated');
        }
    }
}