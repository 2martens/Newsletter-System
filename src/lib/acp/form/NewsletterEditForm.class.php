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
     * @see NewsletterAddForm::readData()
     */
    public function readData() {
        $newsletter = new NewsletterEditor($this->newsletterID);
        $this->subject = $newsletter->subject;
        $this->text = $newsletter->text;
        $time = $newsletter->deliveryTime;
        $dateArray = explode('-', DateUtil::formatDate('%Y-%m-%d'.(MESSAGE_NEWSLETTERSYSTEM_GENERAL_HOURLYCRONJOB ? ' %H' : ''), $time, false, true));
        $this->dateValues = array(
            'day' => $dateArray[2],
            'month' => $dateArray[1],
            'year' => $dateArray[0]
        );
        if (MESSAGE_NEWSLETTERSYSTEM_GENERAL_HOURLYCRONJOB) {
            $this->dateValues['hour'] = $dateArray[3];
        }
        parent::readData();
    }

    /**
     * @see NewsletterAddForm::save()
     */
    public function save() {
        MessageForm::save();
        //create date
        $date = (string) $this->dateValues['year'].'-'.
        (string) $this->dateValues['month'].'-'.
        (string) $this->dateValues['day'].
        (MESSAGE_NEWSLETTERSYSTEM_GENERAL_HOURLYCRONJOB ? ' '.(string) $this->dateValues['hour'] : '');
        //convert date to timestamp
        $unixTime = strtotime($date);
        $newsletter = new NewsletterEditor($this->newsletterID);
        $newsletter->update(WCF::getUser()->userID, WCF::getUser()->username,
            $unixTime, $this->subject, $this->text, $this->enableSmilies,
            $this->enableHtml, $this->enableBBCodes);
        $this->saved();
        $this->success = true;
        
        //resetting cache
        $cacheName = 'newsletter-'.PACKAGE_ID;
        WCF::getCache()->clear(WCF_DIR.'cache/', 'cache.'.$cacheName.'.php');
    }
    
    /**
     * @see NewsletterAddForm::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        WCF::getTPL()->assign('newsletterID', $this->newsletterID);
        if ($this->success) {
            WCF::getTPL()->assign('result', 'success');
        }
    }
}
