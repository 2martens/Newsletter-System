<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObject.class.php');

/**
 * This class represents a log entry.
 *
 * @author Jim Martens
 * @copyright 2013 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage data.message.newsletter.log
 * @category Community Framework
 */
class Log extends DatabaseObject {
	/**
	 * Contains the database table name.
	 * @var string
	 */
	protected $databaseTable = 'newsletter_log';
	
	/**
	 * Creates a Log object.
	 *
	 * @param	integer	$newsletterID
	 * @param	array	$row
	 */
	public function __construct($newsletterID, array $row = array()) {
		if ($logID !== null && intval($logID) > 0) {
			$sql = 'SELECT newsletterID, receivedList
            		FROM wcf'.WCF_N.'_'.$this->databaseTable.' log
            		WHERE log.newsletterID = '.intval($logID);
			$row = WCF::getDB()->getFirstRow($sql);
		}
		parent::__construct($row);
	}
	
	/**
	 * @see DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		$data['newsletterID'] = intval($data['newsletterID']);
		$data['receivedList'] = unserialize(escapeString($data['receivedList']));
		parent::handleData($data);
	}
}
