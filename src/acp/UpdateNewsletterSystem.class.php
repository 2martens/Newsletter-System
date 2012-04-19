<?php

/**
 * Updates the newsletter system from version 1.1.x to 1.2.x.
 *
 * @author Jim Martens
 * @copyright 2012 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @category Community Framework
 */
class UpdateNewsletterSystem {
    /**
     * Contains the unsubscription database table.
     * @var string
     */
    protected $unsubscriptionTable = 'newsletter_unsubscription';
    
    /**
     * Creates a new UpdateNewsletterSystem object.
     */
    public function __construct() {
        $this->insertUnsubscribeTokens();
    }
    
    /**
     * Inserts the unsubscribe tokens for all subscribers.
     */
    protected function insertUnsubscribeTokens() {
        //add cache resource
        $cacheName = 'newsletter-subscriber-'.PACKAGE_ID;
        WCF::getCache()->addResource($cacheName, WCF_DIR.'cache/cache.'.$cacheName.'.php', WCF_DIR.'lib/system/cache/CacheBuilderNewsletterSubscriber.class.php');
        
        //get options
        $subscribersList = WCF::getCache()->get($cacheName, 'subscribers');
        
        $sql = 'SELECT subscriberID
        		FROM wcf'.WCF_N.'_'.$this->unsubscriptionTable;
        $result = WCF::getDB()->sendQuery($sql);
        while ($row = WCF::getDB()->fetchArray($result)) {
            if (isset($subscribersList[$row['subscriberID']])) {
                unset($subscribersList[$row['subscriberID']]);
            }
        }
        
        $sql = 'INSERT INTO wcf'.WCF_N.'_'.$this->unsubscriptionTable."
        			(subscriberID, token)
        		VALUES
        			(a, 'b')";
        foreach ($subscribersList as $subscriberID => $subscriber) {
            //inserts an unsubscribe token for the subscriber
            $tmpSql = str_replace('a', $subscriberID, $sql);
            $tmpSql = str_replace('b', escapeString(StringUtil::getRandomID()), $tmpSql);
            WCF::getDB()->sendQuery($tmpSql);
        }
    }
}
new UpdateNewsletterSystem();