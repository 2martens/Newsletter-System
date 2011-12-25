<?php
//wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');

class NewsletterSubscriberDeleteAction extends AbstractSecureAction {
    /**
     * Contains the id of the specific subscriber.
     * @var int
     */
    protected $subscriberID = 0;
    
    /**
     * Contains the name of the database table.
     * @var String
     */
    protected $databaseTable = 'newsletter_subscriber';
    
    /**
     * @see AbstractSecureAction::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        if (isset($_GET['subscriberID'])) $this->subscriberID = intval($_GET['subscriberID']);
    }
    
    /**
     * @see AbstractAction::execute()
     */
    public function execute() {
        parent::execute();
        $sql = 'DELETE FROM wcf'.WCF_N.'_'.$this->databaseTable.'
        		WHERE subscriberID = '.$this->subscriberID;
        WCF::getDB()->sendQuery($sql);
        $this->executed();
        $cacheName = 'newsletter-subscriber-'.PACKAGE_ID;
        $cacheResource = array(
			'cache' => $cacheName,
			'file' => WCF_DIR.'cache/cache.'.$cacheName.'.php',
			'className' => 'CacheBuilderNewsletterSubscriber',
			'classFile' => WCF_DIR.'lib/system/cache/CacheBuilderNewsletterSubscriber.class.php',
			'minLifetime' => 0,
			'maxLifetime' => 0
		);
        WCF::getCache()->rebuild($cacheResource);
        HeaderUtil::redirect('index.php?page=NewsletterSubscriberList&result=success&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
        exit;
    }
}
