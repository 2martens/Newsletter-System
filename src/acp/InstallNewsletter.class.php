<?php
require_once('./global.php');
require_once(WCF_DIR.'lib/data/user/group/GroupEditor.class.php');

/**
 * Sets the delivered group options to true for the admin group.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage acp
 * @category Community Framework
 */
class InstallNewsletter {
    
    /**
     * Contains the package identifier of this package.
     * @var string
     */
    protected $package = 'de.plugins-zum-selberbauen.newsletter';
    
    /**
     * Contains the admin group id.
     * @var int
     */
    protected $groupID = 4;
    
    /**
     * Contains a group editor object.
     * @var GroupEditor
     */
    protected $group = null;
    
    /**
     * Creates a new InstallNewsletter object.
     */
    public function __construct() {
        $this->group = new GroupEditor($this->groupID);
        $this->installGroupOptions();
    }
    
    /**
     * Installs the group options.
     */
    protected function installGroupOptions() {
        //database tables
        $databaseTablePackage = 'package';
        $databaseTableGroupOption = 'group_option';

        //reading packageID
        $sql = 'SELECT packageID
			FROM wcf'.WCF_N.'_'.$databaseTablePackage."'
			WHERE package = '".escapeString($package)."'";
        $row = WCF::getDB()->getFistRow($sql);
        $packageID = intval($row['packageID']);

        //reading options from this package
        $sql = 'SELECT optionID, optionName
			FROM wcf'.WCF_N.'_'.$databaseTableGroupOption.'
			WHERE packageID = '.$packageID;
        $result = WCF::getDB()->sendQuery($sql);
        $groupOptions = array();
        while ($row = WCF::getDB()->fetchArray($result)) {
            $groupOptions[] = array(
                'optionID' => intval($row['optionID']),
                'optionName' => StringUtil::trim($row['optionName']),
                'optionValue' => 1
            );
        }
        
        $this->group->update($this->group->groupName, $groupOptions);
    }
}

new InstallNewsletter();