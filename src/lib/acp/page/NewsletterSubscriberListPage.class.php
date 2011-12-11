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
    public $neededPermissions = 'admin.content.newsletterSystem.canSeeSubscriberOverview';
    public $templateName = 'newsletterSubscriberList';
    public $defaultSortField = MESSAGE_NEWSLETTERSYSTEM_GENERAL_SORTFIELD;
    public $defaultSortOrder = MESSAGE_NEWSLETTERSYSTEM_GENERAL_SORTORDER;
    public $itemsPerPage = MESSAGE_NEWSLETTERSYSTEM_GENERAL_ITEMS;
    
    /**
     * Contains the subscribers list.
     * @var array
     */
    protected $subscribersList = array();
    
    /**
     * @see SortablePage::readData()
     */
    public function readData() {
        $this->readSubscribers();
        parent::readData();
    }
    
    /**
     * @see SortablePage::validateSortField()
     */
    public function validateSortField() {
        parent::validateSortField();
        $allowedSortFields = array(
            'id',
            'name',
            'email'
        );
        $inArray = false;
        //Checks whether the sort field is allowed or not.
        foreach ($allowedSortFields as $field) {
            if ($this->sortField != $field) continue;
            $inArray = true;
        }
        if (!$inArray) {
            $this->sortField = $this->defaulSortField;
        }
    }
    
    /**
     * @see SortablePage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        WCF::getTPL()->assign(array(
            'subscribers' => $this->subscribersList
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
     * Reads the newsletter subscribers.
     */
    protected function readSubscribers() {
        //add cache resource
        $cacheName = 'newsletter-subscriber-'.PACKAGE_ID;
        WCF::getCache()->addResource($cacheName, WCF_DIR.'cache/cache.'.$cacheName.'.php', WCF_DIR.'lib/system/cache/CacheBuilderNewsletterSubscriber.class.php');
        
        //get options
        $this->subscribersList = WCF::getCache()->get($cacheName, 'subscribers');
    }
    
}
