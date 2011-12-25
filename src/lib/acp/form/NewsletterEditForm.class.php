<?php
//wcf imports
require_once(WCF_DIR.'lib/acp/form/NewsletterAddForm.class.php');

/**
 * Shows the newsletter edit form.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage acp.form
 * @category Community Framework
 */
class NewsletterEditForm extends NewsletterAddForm {
    public $action = 'edit';
    
    /**
     * If true, the save process was successful.
     * @var boolean
     */
    protected $success = false;
    
    /**
     * Contains the newsletter id.
     * @var int
     */
    protected $newsletterID = 0;

    /**
     * @see AbstractPage::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        if (isset($_REQUEST['newsletterID'])) $this->newsletterID = intval($_REQUEST['newsletterID']);
    }

    /**
     * @see NewsletterAddForm::readFormParameters()
     */
    public function readFormParameters() {
        parent::readFormParameters();
        if (isset($_REQUEST['newsletterID'])) $this->newsletterID = intval($_REQUEST['newsletterID']);
    }

    /**
     * @see NewsletterAddForm::save()
     */
    public function save() {
        ACPForm::save();
        //create date
        $date = (string) $this->dateValues['year'].'-'.
        (string) $this->dateValues['month'].'-'.
        (string) $this->dateValues['day'];
        //convert date to timestamp
        $unixTime = strtotime($date);
        $newsletter = new NewsletterEditor($this->newsletterID);
        $newsletter->update(WCF::getUser()->userID, $unixTime, $this->subject, $this->text);
        $this->saved();
        $this->success = true;
    }
    
    /**
     * @see NewsletterAddForm::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        if ($success) {
            WCF::getTPL()->assign('result', 'success');
        }
    }
}
