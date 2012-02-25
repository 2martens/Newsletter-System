<?php
//wcf imports
require_once(WCF_DIR.'lib/acp/form/ACPForm.class.php');

/**
 * Shows the ImportSubscriber form.
 *
 * @author Jim Martens
 * @copyright 2012 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage acp.form
 * @category Community Framework
 */
class ImportSubscriberForm extends ACPForm {
    
    /**
     * @see AbstractPage::$templateName
     */
    public $templateName = 'importSubscriber';
    
    /**
     * @see ACPForm::$activeMenuItem
     */
    public $activeMenuItem = 'wcf.acp.menu.link.content.newslettersystem.importSubscriber';
    
    /**
     * @see AbstractPage::$neededPermissions
     */
    public $neededPermissions = 'admin.content.newslettersystem.canImportSubscriber';
    
    /**
     * Contains the filename of the download file.
     * @var string
     */
    protected $downloadFile = '';
    
    /**
     * Contains the content of the upload file.
     * @var array
     */
	protected $uploadFile = array();
	
	/**
	 * Contains the delimeter.
	 * @var string
	 */
	protected $delimeter = '';
	
	/**
	 * Contains the newsletter subscriber database table.
	 * @var string
	 */
	protected $databaseTable = 'newsletter_subscriber';
    
    /**
     * @see Form::readFormParameters()
     */
    public function readFormParameters() {
        parent::readFormParameters();
        if (isset($_POST['downloadFile'])) $this->downloadFile = StringUtil::trim($_POST['downloadFile']);
		if (isset($_FILES['uploadFile'])) $this->uploadFile = $_FILES['uploadFile'];
		if (isset($_POST['delimeter'])) $this->delimeter = StringUtil::trim($_POST['delimeter']);
    }
    
	/**
	 * @see Form::validate()
	 *
	 * @throws UserInputException
	 */
	public function validate() {
		parent::validate();
		
		if (empty($this->delimeter)) {
		    throw new UserInputException('delimeter');
		}
		elseif (strlen($this->delimeter) > 1) {
		    throw new UserInputException('delimeter', 'tooLong');
		}
		
		if (!empty($this->uploadFile['name'])) {
			$this->validateUploadFile();
		}
		else if (!empty($this->downloadFile)) {
			$this->validateDownloadFile();
		}
		else {
			throw new UserInputException('uploadFile');
		}
	}
	
	/**
	 * Validates the upload file input.
	 *
	 * @throws UserInputException
	 */
	protected function validateUploadFile() {
		if (empty($this->uploadFile['tmp_name'])) {
			throw new UserInputException('uploadFile', 'uploadFailed');
		}
		
		$tmpFilename = FileUtil::getTemporaryFilename('subscriberImport_', '.csv');
		if (@copy($this->uploadFile['tmp_name'], $tmpFilename)) {
			@unlink($this->uploadFile['tmp_name']);
			$this->uploadFile['tmp_name'] = $tmpFilename;
		}
	}
	
	/**
	 * Validates the download file input.
	 *
	 * @throws UserInputException
	 */
	protected function validateDownloadFile() {
		if (FileUtil::isURL($this->downloadFile)) {
			//download file
		    $parsedUrl = parse_url($this->downloadFile);
		    $prefix = 'importSubscriber';
		    try {
		        // file transfer via hypertext transfer protocol.
		        if ($parsedUrl['scheme'] == 'http') {
		            $this->downloadFile = FileUtil::downloadFileFromHttp($this->downloadFile, $prefix);
		        }
		        // file transfer via file transfer protocol.
		        elseif ($parsedUrl['scheme'] == 'ftp') {
		            $this->downloadFile = FTPUtil::downloadFileFromFtp($this->downloadFile, $prefix);
		        }
			}
			catch (SystemException $e) {
				throw new UserInputException('downloadFile', 'notFound');
			}
		}
		else {
			// probably local path
			if (!file_exists($this->downloadFile)) {
				throw new UserInputException('downloadFile', 'notFound');
			}
		}
	}
	
	/**
	 * @see Form::save()
	 */
	public function save() {
		parent::save();
		
		$content = '';
		if (count($this->uploadFile)) {
		   $content = file_get_contents($this->uploadFile['tmp_name']);
		}
		elseif ($this->downloadFile) {
		   $content = file_get_contents($this->downloadFile);
		}
		if (empty($content)) return;
		
		//contains all new emails
		$emails = explode($this->delimeter, $content);
		
		$sql = 'INSERT INTO wcf'.WCF_N.'_'.$this->databaseTable.'
				(userID, username, email)
				VALUES ';
		$insertValues = '';
		foreach ($emails as $email) {
		    if (!empty($insertValues)) $insertValues .= ', ';
		    $data = '(';
		    $sqlInner = 'SELECT userID, COUNT(userID) AS count
		    		FROM wcf'.WCF_N."_user
		    		WHERE email = '".escapeString($email)."'";
		    $row = WCF::getDB()->getFirstRow($sqlInner);
		    if ($row['count']) {
		        $user = new User($row['userID']);
		        $data .= $row['userID'].", '".escapeString($user->username)."', '";
		    }
		    else {
		        $data .= "0, '', '";
		    }
		    $data .= escapeString($email)."')";
		    $insertValues .= $data;
		}
		$sql .= $insertValues;
		WCF::getDB()->sendQuery($sql);
		
		WCF::getTPL()->assign('success', true);
		$this->saved();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
	    parent::assignVariables();
	    WCF::getTPL()->assign('delimeter', $this->delimeter);
	}
}
