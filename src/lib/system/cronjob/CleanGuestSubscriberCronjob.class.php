<?php
//wcf imports
require_once(WCF_DIR.'lib/data/cronjobs/Cronjob.class.php');

/**
 * Cleans the guest subscribers.
 *
 * @author Jim Martens
 * @copyright 2012 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage system.cronjob
 * @category Community Framework
 */
class CleanGuestSubscriberCronjob implements Cronjob {
	
    /**
     * Contains the guest activation database table.
     * @var string
     */
	protected $activationTable = 'newsletter_guest_activation';
	
	/**
	 * Contains the newsletter subscriber database table.
	 * @var string
	 */
	protected $subscriberTable = 'newsletter_subscriber';
	
	/**
     * @see Cronjob::execute()
     */
    public function execute($data) {
        $sql = 'SELECT activation.subscriberID
        		FROM wcf'.WCF_N.'_'.$this->activationTable.' activation
        		LEFT JOIN wcf'.WCF_N.'_'.$this->subscriberTable.' subscriber
        			ON (subscriber.subscriberID = activation.subscriberID)
        		WHERE activation.activated = 0';
        $result = WCF::getDB()->sendQuery($sql);
        
        $subscriberIDs = array();
        
        while ($row = WCF::getDB()->fetchArray($result)) {
            $subscriberIDs[] = $row['subscriberID'];
        }
        $sqlSubscriber = 'DELETE FROM wcf'.WCF_N.'_'.$this->subscriberTable.'
        				WHERE subscriberID = ';
        $sqlActivation = 'DELETE FROM wcf'.WCF_N.'_'.$this->activationTable.'
        				WHERE subscriberID = ';
        foreach ($subscriberIDs as $id) {
            $tmpSubscriberSql = $sqlSubscriber.intval($id);
            $tmpActivationSql = $sqlActivation.intval($id);
            WCF::getDB()->sendQuery($tmpActivationSql);
            WCF::getDB()->sendQuery($tmpSubscriberSql);
        }
    }
}
