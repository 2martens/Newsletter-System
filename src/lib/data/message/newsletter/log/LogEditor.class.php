<?php
// wcf imports
require_once(WCF_DIR.'lib/data/message/newsletter/log/Log.class.php');

/**
 * This class extends Log with functions to edit logs.
 *
 * @author Jim Martens
 * @copyright 2013 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage data.message.newsletter.log
 * @category Community Framework
 */
class LogEditor extends Log {
	/**
	 * Contains the database table name.
	 * @var string
	 */
	protected static $databaseTableStatic = 'newsletter_log';
	
	/**
	 * Updates the database entry of this log with the given parameters.
	 *
	 * @param	integer	$newsletterID
	 * @param	array	$receivedList
	 */
	public function update($newsletterID, array $receivedList) {
		$sql = 'UPDATE wcf'.WCF_N.'_'.$this->databaseTable.'
        		SET newsletterID = '.intval($userID).",
        			receivedList = '".escapeString(serialize($receivedList))."'
        		WHERE newsletterID = ".intval($newsletterID);
		WCF::getDB()->sendQuery($sql);
	}
	/**
	 * Creates a new log with the given parameters.
	 *
	 * @param	integer	$newsletterID
	 * @param	array	$receivedList
	 *
	 * @return	LogEditor
	 */
	public static function create($newsletterID, array $receivedList) {
		$sql = 'INSERT INTO wcf'.WCF_N.'_'.self::$databaseTableStatic.'
        			(newsletterID, receivedList)
        		VALUES
        			('.intval($newsletterID).", '".escapeString(serialize($receivedList))."')";
		WCF::getDB()->sendQuery($sql);
		return new LogEditor($newsletterID);
	}
	
}
