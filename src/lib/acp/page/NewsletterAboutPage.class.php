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
     * Shows whether an update is available or not.
     * @var boolean
     */
    protected $updateAvailable = false;
    
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
        
        //can not continue without one of these things
        if (!function_exists('fsockopen')) return;
        elseif (!defined('MESSAGE_NEWSLETTERSYSTEM_GENERAL_LOGIN') || MESSAGE_NEWSLETTERSYSTEM_GENERAL_LOGIN == '') return;
        
        //contains the url to an action which shows the current version of the given package
        $url = 'http://update.plugins-zum-selberbauen.de/index.php?action=CurrentVersion&package=de.plugins-zum-selberbauen.newsletter';
        $urlParsed = parse_url($url);
        $path = $urlParsed['path'];
        if (!isset($urlParsed['port'])) $urlParsed['port'] = 80;
        if (isset($urlParsed['query'])) $path .= '?'.$urlParsed['query'];
        
        //generating HTTP request
        $request = 'GET '. $path ." HTTP/1.1\r\n";
        $request .= 'Host: '. $urlParsed['host']."\r\n";
        $authLine = 'Authorization: Basic '.base64_encode(MESSAGE_NEWSLETTERSYSTEM_GENERAL_LOGIN)."\r\n";
        //sending HTTP request with browser data and authorization
        $response = RemoteUtil::sendRequest($request, $urlParsed, true, $authLine);
        $response = RemoteUtil::getResponse($response);
        
        if ($this->version != $response[0]) $this->updateAvailable = true;
    }
    
    /**
     * @see AbstractPage::assignVariables()
     */
    public function assignVariables() {
        parent::assignVariables();
        WCF::getTPL()->assign(array(
        	'version' => $this->version,
            'update' => $this->updateAvailable
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
