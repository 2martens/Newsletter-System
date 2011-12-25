<?php
//wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');

/**
 * Deletes the newsletter with the given id.s
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage acp.action
 * @category Community Framework
 */
class NewsletterDeleteAction extends AbstractSecureAction {
    
    /**
     * Contains the newsletter id.
     * @var int
     */
    protected $newsletterID = 0;
    
    /**
     * Contains the database table name.
     * @var string
     */
    protected $databaseTable = 'newsletter';
    
    /**
     * @see AbstractSecureAction::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        if (isset($_GET['newsletterID'])) $this->newsletterID = intval($_GET['newsletterID']);
    }
    
    /**
     * @see AbstractAction::execute()
     */
    public function execute() {
        parent::execute();
        $sql = 'DELETE FROM wcf'.WCF_N.'_'.$this->databaseTable.'
        		WHERE newsletterID = '.$this->newsletterID;
        WCF::getDB()->sendQuery($sql);
        $this->executed();
        //resetting cache
        $cacheName = 'newsletter-'.PACKAGE_ID;
        $cacheResource = array(
			'cache' => $cacheName,
			'file' => WCF_DIR.'cache/cache.'.$cacheName.'.php',
			'className' => 'CacheBuilderNewsletter',
			'classFile' => WCF_DIR.'lib/system/cache/CacheBuilderNewsletter.class.php',
			'minLifetime' => 0,
			'maxLifetime' => 0
		);
        WCF::getCache()->rebuild($cacheResource);
        HeaderUtil::redirect('index.php?page=NewsletterList&result=success&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
        exit;
    }
}
