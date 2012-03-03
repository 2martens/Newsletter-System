<?php
//wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheBuilder.class.php');

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
        $sql = 'SELECT newsletterID, userID, username, deliveryTime, subject, text, enableSmilies, enableHtml, enableBBCodes
        		FROM wcf'.WCF_N.'_'.$this->databaseTable.' newsletter
        		ORDER BY newsletter.newsletterID';
        $result = WCF::getDB()->sendQuery($sql);
        $newsletterIDs = array();
        while ($row = WCF::getDB()->fetchArray($result)) {
            $newsletterIDs[$row['newsletterID']] = array(
                'userID' => $row['userID'],
                'username' => $row['username'],
                'deliveryTime' => $row['deliveryTime'],
                'subject' => $row['subject'],
                'text' => $row['text'],
                'enableSmilies' => $row['enableSmilies'],
                'enableHtml' => $row['enableHtml'],
                'enableBBCodes' => $row['enableBBCodes']
            );
        }
        $data['newsletter'] = $newsletterIDs;
        return $data;
    }
}
