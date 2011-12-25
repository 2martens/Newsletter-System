<?php
//wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

/**
 * Builds the newsletter subscribers cache.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newletter
 * @subpackage system.cache
 * @category Community Framework
 */
class CacheBuilderNewsletterSubscriber implements CacheBuilder {
    /**
     * Contains the database table name.
     * @var String
     */
    protected $databaseTable = 'newsletter_subscriber';
    
    /**
     * @see CacheBuilder::getData()
     */
    public function getData($cacheResource) {
        $data = array('subscribers' => array());
        
        //get all subscribers and list them by id
        $sql = 'SELECT subscriberID, userID, username, email
        		FROM wcf'.WCF_N.'_'.$this->databaseTable.' subscribers
        		ORDER BY subscribers.subscriberID';
        $result = WCF::getDB()->sendQuery($sql);
        $subscriberIDs = array();
        while ($row = WCF::getDB()->fetchArray($result)) {
            $subscriberIDs[$row['subscriberID']] = array(
                'userID' => $row['userID'],
            	'username' => $row['username'],
            	'email' => $row['email']
            );
        }
        $this->data['subscribers'] = $subscriberIDs;
        return $data;
    }
}
