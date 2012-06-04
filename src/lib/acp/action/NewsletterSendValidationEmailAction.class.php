<?php
//wcf imports
require_once(WCF_DIR.'lib/action/AbstractSecureAction.class.php');
require_once(WCF_DIR.'lib/data/message/newsletter/subscriber/NewsletterSubscriber.class.php');

/**
 * Sends the validation email.
 *
 * @author Jim Martens
 * @copyright 2012 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage acp.action
 * @category Community Framework
 */
class NewsletterSendValidationEmailAction extends AbstractSecureAction {
    
    /**
     * Contains the subscriber id.
     * @var int
     */
    protected $subscriberID = 0;
    
    /**
     * @see Action::readParameters()
     */
    public function readParameters() {
        parent::readParameters();
        if (isset($_GET['subscriberID'])) $this->subscriberID = intval($_GET['subscriberID']);
    }
    
    /**
     * @see Action::execute()
     */
    public function execute() {
        parent::execute();
        $subscriber = new NewsletterSubscriber($this->subscriberID);
        if ($subscriber->userID) {
            $user = new User($subscriber->userID);
            NewsletterUtil::sendUserValidationEmail($user);
        }
        else {
            NewsletterUtil::sendGuestValidationEmail($subscriber);
        }
        $this->executed();
        HeaderUtil::redirect('index.php?page=NewsletterSubscriberList&success=success&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
        exit;
    }
}
