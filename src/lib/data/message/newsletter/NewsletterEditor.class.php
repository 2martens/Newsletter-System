<?php
//wcf imports
require_once(WCF_DIR.'lib/data/message/newsletter/Newsletter.class.php');

/**
 * This class extends Newsletter with functions to edit newsletters.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage data.message.newsletter
 * @category Community Framework
 */
class NewsletterEditor extends Newsletter {
    
    /**
     * Updates the database entry of this newsletter with the given parameters.
     *
     * @param int $newsletterID
     * @param int $userID
     * @param int $deliveryTime (timestamp)
     * @param string $subject
     * @param string $text
     */
    public function update($newsletterID, $userID, $deliveryTime, $subject = '', $text = '') {
        $sql = 'UPDATE wcf'.WCF_N.'_'.$this->databaseTable.' newsletter
        		SET userID = '.intval($userID).',
        			deliveryTime = '.intval($deliveryTime).",
        			subject = '".escapeString($subject)."',
        			text = '".escapeString($text)."'
        		WHERE newsletter.newsletterID = ".intval($newsletterID);
        WCF::getDB()->sendQuery($sql);
    }
    
    /**
     * Creates a new newsletter with the given parameters.
     *
     * @param int $userID
     * @param int $deliveryTime (timestamp)
     * @param string $subject
     * @param string $text
     *
     * @return NewsletterEditor
     */
    public static function create($userID, $deliveryTime, $subject, $text) {
        $userID = intval($userID);
        $deliveryTime = intval($deliveryTime);
        $subject = StringUtil::trim($subject);
        $text = StringUtil::trim($text);
        
        $sql = 'INSERT INTO wcf'.WCF_N.'_'.$this->databaseTable.' newsletter
        			(userID, deliveryTime, subject, text)
        		VALUES
        			('.$userID.', '.$deliveryTime.", '".
                    escapeString($subject)."', '".escapeString($text)."')";
        WCF::getDB()->sendQuery($sql);
        $newsletterID = WCF::getDB()->getInsertID();
        return new NewsletterEditor($newsletterID);
    }
    
}
