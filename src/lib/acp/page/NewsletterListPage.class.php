<?php
//wcf imports
require_once(WCF_DIR.'lib/page/SortablePage.class.php');

/**
 * Shows a list of all newsletters.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage acp.page
 * @category Community Framework
 */
class NewsletterListPage extends SortablePage {
    public $neededPermissions = 'admin.content.newslettersystem.canSeeNewsletterOverview';
    public $templateName = 'newsletterList';
    public $defaultSortField = MESSAGE_NEWSLETTERSYSTEM_GENERAL_SORTFIELD_NEWSLETTER;
    public $defaultSortOrder = MESSAGE_NEWSLETTERSYSTEM_GENERAL_SORTORDER_NEWSLETTER;
    public $itemsPerPage = MESSAGE_NEWSLETTERSYSTEM_GENERAL_ITEMS;
    
    /**
     * Contains the result of deleting a newsletter.
     * @var string
     */
    protected $result = '';
    
    /**
     * If true, the newsletter has been successfully sent.
     * @var boolean
     */
    protected $success = false;
    
    /**
     * Contains the newsletter list.
     * @var array
     */
    protected $newsletterList = array();
    
    /**
     * Contains the current newsletter list.
     * @var array
     */
    protected $currentNewsletterList = array();
    
    /**
     * Contains the database table name.
     * @var string
     */
    protected $databaseTable = 'newsletter';
    
    public function readParameters() {
        parent::readParameters();
        if (isset($_REQUEST['result'])) $this->result = StringUtil::trim($_REQUEST['result']);
        if (isset($_REQUEST['success'])) $this->success = true;
    }
    
	/**
     * @see SortablePage::readData()
     */
    public function readData() {
        $this->readNewsletters();
        parent::readData();
        $this->sortNewsletters();
    }
    
	/**
     * @see SortablePage::validateSortField()
     */
    public function validateSortField() {
        parent::validateSortField();
        $allowedSortFields = array(
            'newsletterID',
            'username',
            'deliveryTime',
            'subject'
        );
        $inArray = false;
        //Checks whether the sort field is allowed or not.
        foreach ($allowedSortFields as $field) {
            if ($this->sortField != $field) continue;
            $inArray = true;
        }
        if (!$inArray) {
            $this->sortField = $this->defaultSortField;
        }
    }
    
    /**
     * @see SortablePage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        WCF::getTPL()->assign(array(
            'newsletters' => $this->currentNewsletterList,
            'result' => $this->result,
            'success' => $this->success
        ));
    }
    
    /**
     * @see MultipleLinkPage::countItems()
     */
    public function countItems() {
        parent::countItems();
        return count($this->newsletterList);
    }
    
    /**
     * @see AbstractPage::show()
     */
    public function show() {
        // enable menu item
		WCFACP::getMenu()->setActiveMenuItem('wcf.acp.menu.link.content.newslettersystem.newsletterList');
		
		parent::show();
    }
    
    /**
     * Reads the newsletters.
     */
    protected function readNewsletters() {
        //add cache resource
        $cacheName = 'newsletter-'.PACKAGE_ID;
        WCF::getCache()->addResource($cacheName, WCF_DIR.'cache/cache.'.$cacheName.'.php', WCF_DIR.'lib/system/cache/CacheBuilderNewsletter.class.php');
        
        //get options
        $this->newsletterList = WCF::getCache()->get($cacheName, 'newsletter');
        $this->currentNewsletterList = array_slice($this->newsletterList, ($this->pageNo - 1) * $this->itemsPerPage, $this->itemsPerPage, true);
    }
    
	/**
     * Sorts the subscribers.
     */
    protected function sortNewsletters() {
        
        $sql = 'SELECT newsletterID, userID, username, subject, deliveryTime
        		FROM wcf'.WCF_N.'_'.$this->databaseTable.'
        		';
        $sqlOrder = '';
        switch ($this->sortField) {
            case 'username':
                $sqlOrder = 'ORDER BY username';
                break;
            case 'subject':
                $sqlOrder = 'ORDER BY subject';
                break;
            case 'deliveryTime':
                $sqlOrder = 'ORDER BY deliveryTime';
                break;
            case 'newsletterID':
                if ($this->sortOrder == 'DESC') {
                    $this->newsletterList = array_reverse($this->newsletterList, true);
                    $this->currentNewsletterList = array_reverse($this->currentNewsletterList, true);
                }
            default:
                return; //does nothing and exits the method
        }
        $sql .= $sqlOrder.' '.$this->sortOrder;
        $result = WCF::getDB()->sendQuery($sql, $this->itemsPerPage, ($this->pageNo - 1) * $this->itemsPerPage);
        $tmpArray = array();
        while ($row = WCF::getDB()->fetchArray($result)) {
            $tmpArray[$row['newsletterID']] = array(
                'userID' => $row['userID'],
                'username' => $row['username'],
                'subject' => $row['subject'],
                'deliveryTime' => $row['deliveryTime']
            );
        }
        $this->newsletterList = $this->currentNewsletterList = $tmpArray;
    }
}
