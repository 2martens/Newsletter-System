<?php
//wcf imports
require_once(WCF_DIR.'lib/data/cronjobs/Cronjob.class.php');

/**
 * Sends the newsletters.
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage system.cronjob
 * @category Community Framework
 */
class SendNewsletterCronjob implements Cronjob {
    
    /**
     * Contains a list of all newsletters.
     * @var string
     */
    protected $newsletterList = array();
    
    /**
     * Contains a list of all newsletters which have to be sended.
     * @var array
     */
    protected $outstandingNewsletters = array();
    
    /**
     * @see Cronjob::execute()
     */
    public function execute($data) {
        parent::execute($data);
        $this->readNewsletters();
        $this->checkNewsletters();
        $this->sendNewsletters();
    }
    
    /**
     * Sends the newsletters.
     */
    protected function sendNewsletters() {
        //TODO: Implement sendNewsletters method.
        
    }
    
    
    /**
     * Reads the newsletters.
     */
    protected function readNewsletters() {
        //add cache resource
        $cacheName = 'newsletter-list-'.PACKAGE_ID;
        WCF::getCache()->addResource($cacheName, WCF_DIR.'cache/cache.'.$cacheName.'.php', WCF_DIR.'lib/system/cache/CacheBuilderNewsletter.class.php');
        
        //get options
        $this->newsletterList = WCF::getCache()->get($cacheName, 'newsletter');
    }
    
    /**
     * Checks the newsletters for time of delivery.
     */
    protected function checkNewsletters() {
        foreach ($this->newsletterList as $id => $newsletter) {
            $date = date('Y-m-d', $newsletter['deliveryTime']);
            $now = date('Y-m-d', TIME_NOW);
            if ($date == $now) {
                $this->outstandingNewsletters[$id] = $newsletter;
            }
        }
    }
}