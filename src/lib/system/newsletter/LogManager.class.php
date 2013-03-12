<?php
// wcf imports
require_once(WCF_DIR.'lib/data/message/newsletter/log/LogEditor.class.php');

/**
 * Manages the log.
 *
 * @author Jim Martens
 * @copyright 2013 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage system.newsletter
 * @category Community Framework
 */
class LogManager {
	
	/**
	 * Contains an instance of LogManager.
	 * @var LogManager
	 */
	protected static $instance = null;
	
	/**
	 * Contains a LogEditor object.
	 * @var LogEditor
	 */
	protected $logEditor = null;
	
	/**
	 * Contains the newsletter id.
	 * @var integer
	 */
	protected $newsletterID = 0;
	
	/**
	 * Contains the list of the subscribers who already received the newsletter.
	 * @var	array
	 */
	protected $receivedList = array();
	
	/**
	 * Returns an instance of the LogManager.
	 *
	 * @return	LogManager
	 */
	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new LogManager();
		}
		return self::$instance;
	}
	
	/**
	 * Resets the instance.
	 */
	public static function destroyInstance() {
		self::$instance = null;
	}
	
	/**
	 * Sets the newsletter id.
	 * @param	integer	$newsletterID
	 */
	public function setNewsletter($newsletterID) {
		$this->newsletterID = intval($newsletterID);
	}
	
	/**
	 * Adds a subscriber to the list of receivers.
	 *
	 * @param	integer	$subscriberID
	 * @param	string	$email
	 */
	public function addSubscriber($subscriberID, $email) {
		$this->receivedList[intval($subscriberID)] = StringUtil::trim($email);
	}
	
	/**
	 * Saves the changes into database.
	 */
	public function __destruct() {
		$this->receivedList = array_unique($this->receivedList);
		$this->logEditor->update($newsletterID, $this->receivedList);
	}
	
	/**
	 * Public constructor not supported.
	 */
	private function __construct() {
		$logEditor = new LogEditor($this->newsletterID);
		if ($logEditor->newsletterID === null) {
			$logEditor = LogEditor::create($this->newsletterID, $this->receivedList);
		}
		$this->logEditor = $logEditor;
		$this->receivedList = $this->logEditor->receivedList;
	}
}
