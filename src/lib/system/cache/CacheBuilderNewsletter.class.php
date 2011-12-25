<?php
//wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');
require_once(WCF_DIR.'lib/data/user/User.class.php');

/**
 * Build the newsletter list cache.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage system.cache
 * @category Community Framework
 */
class CacheBuilderNewsletter implements CacheBuilder {
    /**
     * Contains the database table name.
     * @var string
     */
    protected $databaseTable = 'newsletter';
    
    /**
     * @see CacheBuilder::getData()
     */
    public function getData($cacheResource) {
        $data = array('newsletter' => array());
        
        //get all newsletters and order them by id
        $sql = 'SELECT newsletterID, userID, deliveryTime, subject, text
        		FROM wcf'.WCF_N.'_'.$this->databaseTable.' newsletter
        		ORDER BY newsletter.newsletterID';
        $result = WCF::getDB()->sendQuery($sql);
        $newsletterIDs = array();
        while ($row = WCF::getDB()->fetchArray($result)) {
            $user = new User($userArray['userID']);
            $newsletterIDs[$row['newsletterID']] = array(
                'userID' => $row['userID'],
                'username' => $user->username,
                'deliveryTime' => $row['deliveryTime'],
                'subject' => $row['subject'],
                'text' => $row['text']
            );
        }
        
        return $data;
    }
}
