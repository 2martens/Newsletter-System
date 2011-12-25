<?php
//wcf imports
require_once(WCF_DIR.'lib/data/cronjobs/Cronjob.class.php');
require_once(WCF_DIR.'lib/acp/action/SendNewsletterAction.class.php');

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
     * @see Cronjob::execute()
     */
    public function execute($data) {
        $action = new SendNewsletterAction();
    }
}
