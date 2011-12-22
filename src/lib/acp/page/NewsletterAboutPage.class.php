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
    
    public $menuItem = 'wcf.acp.menu.link.content.newsletterSystem.info';
    public $templateName = 'newsletterAbout';
    
    /**
     * Contains the current version of the plugin.
     * @var string
     */
    protected $version = '';
    
    /**
     * Shows whether an update is available or not.
     * @var boolean
     */
    protected $updateAvailable = false;
    
    /**
     * @see AbstractPage::readData()
     */
    public function readData() {
        parent::readData();
        if (!function_exists('fsockopen')) $this->version = 'latest version';
        elseif (!defined('MESSAGE_NEWSLETTERSYSTEM_GENERAL_LOGIN') || MESSAGE_NEWSLETTERSYSTEM_GENERAL_LOGIN == '') return;
        $url = 'http://update.plugins-zum-selberbauen.de/index.php?action=CurrentVersion&package=de.plugins-zum-selberbauen.newsletter';
        $urlParsed = parse_url($url);
        $path = $urlParsed['path'];
        if (!isset($urlParsed['port'])) $urlParsed['port'] = 80;
        if (isset($urlParsed['query'])) $path .= '?'.$urlParsed['query'];
        $request = 'GET '. $path ." HTTP/1.1\r\n";
        $request .= 'Host: '. $urlParsed['host']."\r\n";
        $authLine = 'Authorization: Basic '.base64_encode(/*'JimMartens:S5GPoKzR'*/MESSAGE_NEWSLETTERSYSTEM_GENERAL_LOGIN)."\r\n";
        $response = RemoteUtil::sendRequest($request, $urlParsed, true, $authLine);
        $response = RemoteUtil::getResponse($response);
        $this->version = $response[0];
        
        $sql = 'SELECT packageVersion
        		FROM wcf'.WCF_N."_package
        		WHERE package = 'de.plugins-zum-selberbauen.newsletter'";
        $row = WCF::getDB()->getFirstRow($sql);
        if ($this->version != $row['packageVersion']) $this->updateAvailable = true;
    }
    
    /**
     * @see AbstractPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        WCF::getTPL()->assign('version', $this->version);
        if ($this->updateAvailable) {
            WCF::getTPL()->assign('update', true);
        }
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
