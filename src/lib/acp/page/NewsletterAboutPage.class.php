<?php
//wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

/**
 * Shows the about page.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage acp.page
 * @category Community Framework
 */
class NewsletterAboutPage extends AbstractPage {
    
    public $menuItem = 'wcf.acp.menu.link.content.newslettersystem.info';
    public $templateName = 'newsletterAbout';
    
    /**
     * Contains the current version of the plugin.
     * @var string
     */
    protected $version = '';
    
    /**
     * @see AbstractPage::readData()
     */
    public function readData() {
        parent::readData();
        //gets current version
        $sql = 'SELECT packageVersion
        		FROM wcf'.WCF_N."_package
        		WHERE package = 'de.plugins-zum-selberbauen.newsletter'";
        $row = WCF::getDB()->getFirstRow($sql);
        $this->version = $row['packageVersion'];
    }
    
    /**
     * @see AbstractPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        WCF::getTPL()->assign(array(
        	'version' => $this->version
        ));
    }
    
    /**
     * @see AbstractPage::show()
     */
    public function show() {
        //sets active menu item
        WCFACP::getMenu()->setActiveMenuItem($this->menuItem);
        
        parent::show();
    }
}
