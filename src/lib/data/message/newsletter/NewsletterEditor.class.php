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
     * Contains the database table name.
     * @var string
     */
    protected static $databaseTableStatic = 'newsletter';
    
    /**
     * Updates the database entry of this newsletter with the given parameters.
     *
     * @param int $userID
     * @param string $username
     * @param int $deliveryTime (timestamp)
     * @param string $subject
     * @param string $text
     * @param boolean $enableSmilies
     * @param boolean $enableHtml
     * @param boolean $enableBBCodes
     */
    public function update($userID, $username, $deliveryTime, $subject = '', $text = '', $enableSmilies = true, $enableHtml = false, $enableBBCodes = true) {
        $sql = 'UPDATE wcf'.WCF_N.'_'.$this->databaseTable.'
        		SET userID = '.intval($userID).",
        			username = '".escapeString($username)."',
        			deliveryTime = ".intval($deliveryTime).",
        			subject = '".escapeString($subject)."',
        			text = '".escapeString($text)."',
        			enableSmilies = ".intval($enableSmilies).",
        			enableHtml = ".intval($enableHtml).",
        			enableBBCodes = ".intval($enableBBCodes)."
        		WHERE newsletterID = ".intval($this->messageID);
        WCF::getDB()->sendQuery($sql);
    }
    
    /**
     * Creates a new newsletter with the given parameters.
     *
     * @param int $deliveryTime (timestamp)
     * @param string $subject
     * @param string $text
     * @param boolean $enableSmilies
     * @param boolean $enableHtml
     * @param boolean $enableBBCodes
     *
     * @return NewsletterEditor
     */
    public static function create($deliveryTime, $subject, $text, $enableSmilies = true, $enableHtml = false, $enableBBCodes = true) {
        $userID = intval(WCF::getUser()->userID);
        $username = StringUtil::trim(WCF::getUser()->username);
        $deliveryTime = intval($deliveryTime);
        $subject = StringUtil::trim($subject);
        $text = StringUtil::trim($text);
        
        $sql = 'INSERT INTO wcf'.WCF_N.'_'.self::$databaseTableStatic.'
        			(userID, username, deliveryTime, subject, text, enableSmilies, enableHtml, enableBBCodes)
        		VALUES
        			('.$userID.", '".escapeString($username)."', ".$deliveryTime.", '".
                    escapeString($subject)."', '".escapeString($text)."', ".
                    intval($enableSmilies).', '.intval($enableHtml).', '.intval($enableBBCodes).')';
        WCF::getDB()->sendQuery($sql);
        $newsletterID = WCF::getDB()->getInsertID();
        return new NewsletterEditor($newsletterID);
    }
    
}
