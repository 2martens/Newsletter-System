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
     * Contains the subscriber database table.
     * @var string
     */
    protected $subscriberTable = 'newsletter_subscriber';
    
    /**
     * Contains the unsubscription database table.
     * @var string
     */
    protected $unsubscriptionTable = 'newsletter_unsubscription';
    
    /**
     * @see CacheBuilder::getData()
     */
    public function getData($cacheResource) {
        $data = array('subscribers' => array(), 'unsubscribeTokens' => array());
        
        //get all subscribers and list them by id
        $sql = 'SELECT subscriberID, userID, username, email
        		FROM wcf'.WCF_N.'_'.$this->subscriberTable.' subscribers
        		ORDER BY subscribers.subscriberID';
        $result = WCF::getDB()->sendQuery($sql);
        $subscriberIDs = array();
        while ($row = WCF::getDB()->fetchArray($result)) {
            $subscriberIDs[$row['subscriberID']] = array(
                'subscriberID' => $row['subscriberID'],
                'userID' => $row['userID'],
            	'username' => $row['username'],
            	'email' => $row['email']
            );
        }
        $data['subscribers'] = $subscriberIDs;
        
        $sql = 'SELECT subscriberID, token
        		FROM wcf'.WCF_N.'_'.$this->unsubscriptionTable.' subscribers
        		ORDER BY subscribers.subscriberID';
        $result = WCF::getDB()->sendQuery($sql);
        $tokens = array();
        while ($row = WCF::getDB()->fetchArray($result)) {
            $tokens[$row['subscriberID']] = array(
                'subscriberID' => $row['subscriberID'],
                'token' => $row['token']
            );
        }
        $data['unsubscribeTokens'] = $tokens;
        return $data;
    }
}
