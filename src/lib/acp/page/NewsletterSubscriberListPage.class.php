<?php
//wcf imports
require_once(WCF_DIR.'lib/page/SortablePage.class.php');

/**
 * Shows a list of subscribers.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage acp.page
 * @category Community Framework
 */
class NewsletterSubscriberListPage extends SortablePage {
    public $neededPermissions = 'admin.content.newslettersystem.canSeeSubscriberOverview';
    public $templateName = 'newsletterSubscriberList';
    public $defaultSortField = MESSAGE_NEWSLETTERSYSTEM_GENERAL_SORTFIELD_SUBSCRIBER;
    public $defaultSortOrder = MESSAGE_NEWSLETTERSYSTEM_GENERAL_SORTORDER_SUBSCRIBER;
    public $itemsPerPage = MESSAGE_NEWSLETTERSYSTEM_GENERAL_ITEMS;
    
    /**
     * Contains the result of deleting a subscriber.
     * @var string
     */
    protected $result = '';
    
    /**
     * Contains the result of resending the validation email.
     * @var string
     */
    protected $success = '';
    
    /**
     * Contains the subscribers list.
     * @var array
     */
    protected $subscribersList = array();
    
    /**
     * Contains the current subscribers list.
     * @var array
     */
    protected $currentSubscribersList = array();
    
    /**
     * Contains the database table name.
     * @var string
     */
    protected $databaseTable = 'newsletter_subscriber';
    
    public function readParameters() {
        parent::readParameters();
        if (isset($_REQUEST['result'])) $this->result = StringUtil::trim($_REQUEST['result']);
        if (isset($_REQUEST['success'])) $this->success = StringUtil::trim($_REQUEST['success']);
    }
    
    /**
     * @see SortablePage::readData()
     */
    public function readData() {
        $this->readSubscribers();
        parent::readData();
        $this->sortSubscribers();
    }
    
    /**
     * @see SortablePage::validateSortField()
     */
    public function validateSortField() {
        parent::validateSortField();
        $allowedSortFields = array(
            'subscriberID',
            'username',
            'email'
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
            'subscribers' => $this->currentSubscribersList,
            'result' => $this->result,
            'success' => $this->success
        ));
    }
    
    /**
     * @see MultipleLinkPage::countItems()
     */
    public function countItems() {
        parent::countItems();
        return count($this->subscribersList);
    }
    
	/**
     * @see AbstractPage::show()
     */
    public function show() {
        // enable menu item
		WCFACP::getMenu()->setActiveMenuItem('wcf.acp.menu.link.content.newslettersystem.subscriberList');
		
		parent::show();
    }
    
    /**
     * Reads the newsletter subscribers.
     */
    protected function readSubscribers() {
        //add cache resource
        $cacheName = 'newsletter-subscriber-'.PACKAGE_ID;
        WCF::getCache()->addResource($cacheName, WCF_DIR.'cache/cache.'.$cacheName.'.php', WCF_DIR.'lib/system/cache/CacheBuilderNewsletterSubscriber.class.php');
        
        //get options
        $this->subscribersList = WCF::getCache()->get($cacheName, 'subscribers');
        $this->currentSubscribersList = array_slice($this->subscribersList, ($this->pageNo - 1) * $this->itemsPerPage, $this->itemsPerPage, true);
    }
    
    /**
     * Sorts the subscribers.
     */
    protected function sortSubscribers() {
        
        $sql = 'SELECT subscriberID, userID, username, email
        		FROM wcf'.WCF_N.'_'.$this->databaseTable.'
        		';
        $sqlOrder = '';
        switch ($this->sortField) {
            case 'username':
                $sqlOrder = 'ORDER BY username';
                break;
            case 'email':
                $sqlOrder = 'ORDER BY email';
                break;
            case 'subscriberID':
                if ($this->sortOrder == 'DESC') {
                    $this->subscribersList = array_reverse($this->subscribersList, true);
                    $this->currentSubscribersList = array_reverse($this->currentSubscribersList, true);
                }
            default:
                return; //does nothing and exits the method
        }
        $sql .= $sqlOrder.' '.$this->sortOrder;
        $result = WCF::getDB()->sendQuery($sql, $this->itemsPerPage, ($this->pageNo - 1) * $this->itemsPerPage);
        $tmpArray = array();
        while ($row = WCF::getDB()->fetchArray($result)) {
            $tmpArray[$row['subscriberID']] = array(
                'userID' => $row['userID'],
                'username' => $row['username'],
                'email' => $row['email']
            );
        }
        $this->subscribersList = $this->currentSubscribersList = $tmpArray;
    }
}
