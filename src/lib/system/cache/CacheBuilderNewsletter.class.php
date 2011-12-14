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
    public function getData() {
        $data = array('newsletter' => array());
        
        //get all newsletters and order them by id
        $sql = 'SELECT newsletterID, userID, subject
        		FROM wcf'.WCF_N.'_'.$this->databaseTable.' newsletter
        		ORDER BY newsletter.newsletterID';
        $result = WCF::getDB()->sendQuery($sql);
        $newsletterIDs = array();
        while ($row = WCF::getDB()->fetchArray($result)) {
            $newsletterIDs[$row['newsletterID']] = array(
                'userID' => $row['userID'],
                'subject' => $row['subject']
            );
        }
        
        if (count($newsletterIDs) > 0) {
            require_once(WCF_DIR.'lib/data/user/User.class.php');
            foreach ($newsletterIDs as $id => $userArray) {
                $user = new User($userArray['userID']);
                $name = $user->username;
                $data['newsletter'][] = array(
                	'newsletterID' => $id,
                	'username' => $name,
                    'subject' => $userArray['subject']
                );
            }
        }
        return $data;
    }
}
