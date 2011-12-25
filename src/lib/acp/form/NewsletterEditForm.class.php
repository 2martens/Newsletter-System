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
        if (!count($_POST)) {
            $newsletter = new NewsletterEditor($this->newsletterID);
            $this->subject = $newsletter->subject;
            $this->text = $newsletter->text;
            $time = $newsletter->deliveryTime;
            $dateArray = explode('-', DateUtil::formatDate('%Y-%m-%d', $time, false, true));
            $this->dateValues = array(
                'day' => $dateArray[2],
                'month' => $dateArray[1],
                'year' => $dateArray[0]
            );
            parent::readData();
        }
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
        $cacheName = 'newsletter-'.PACKAGE_ID;
        WCF::getCache()->addResource($cacheName, WCF_DIR.'cache/cache.'.$cacheName.'.php', WCF_DIR.'lib/system/cache/CacheBuilderNewsletter.class.php');
        WCF::getCache()->rebuild($cacheName);
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
