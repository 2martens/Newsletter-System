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
     * @see Page::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        if (isset($_REQUEST['newsletterID'])) $this->newsletterID = intval($_REQUEST['newsletterID']);
    }

    /**
     * @see Form::readFormParameters()
     */
    public function readFormParameters() {
        parent::readFormParameters();
        if (isset($_REQUEST['newsletterID'])) $this->newsletterID = intval($_REQUEST['newsletterID']);
    }
    
    /**
     * @see Page::readData()
     */
    public function readData() {
        $newsletter = new NewsletterEditor($this->newsletterID);
        $this->subject = $newsletter->subject;
        $this->text = $newsletter->text;
        $time = $newsletter->deliveryTime;
        $dateArray = explode('-', DateUtil::formatDate('%Y-%m-%d'.(MESSAGE_NEWSLETTERSYSTEM_GENERAL_HOURLYCRONJOB ? '-%H' : ''), $time, false, true));
        $this->dateValues['day'] = $dateArray[2];
        $this->dateValues['month'] = $dateArray[1];
        $this->dateValues['year'] = $dateArray[0];
        if (MESSAGE_NEWSLETTERSYSTEM_GENERAL_HOURLYCRONJOB) {
            $this->dateValues['hour'] = $dateArray[3];
        }
        parent::readData();
    }

    /**
     * @see Form::save()
     */
    public function save() {
        MessageForm::save();
        //create date
        $date = (string) $this->dateValues['year'].'-'.
        (string) $this->dateValues['month'].'-'.
        (string) $this->dateValues['day'].
        (MESSAGE_NEWSLETTERSYSTEM_GENERAL_HOURLYCRONJOB ? ' '.(string) $this->dateValues['hour'].':00:00' : '');
        //convert date to timestamp
        $unixTime = strtotime($date);
        $newsletter = new NewsletterEditor($this->newsletterID);
        $newsletter->update(WCF::getUser()->userID, WCF::getUser()->username,
            $unixTime, $this->subject, $this->text, $this->enableSmilies,
            $this->enableHtml, $this->enableBBCodes);
        $this->saved();
        
        if ($this->sendTestmail) $this->sendTestmail($newsletter);
        $this->success = true;
        
        //resetting cache
        $cacheName = 'newsletter-'.PACKAGE_ID;
        WCF::getCache()->clear(WCF_DIR.'cache/', 'cache.'.$cacheName.'.php');
    }
    
    /**
     * @see Page::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        WCF::getTPL()->assign('newsletterID', $this->newsletterID);
        if ($this->success) {
            WCF::getTPL()->assign('result', 'success');
        }
    }
}
