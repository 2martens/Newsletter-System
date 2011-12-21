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
        HeaderUtil::redirect('index.php?page=NewsletterSubscriberList&result=success');
        exit;
    }
}
