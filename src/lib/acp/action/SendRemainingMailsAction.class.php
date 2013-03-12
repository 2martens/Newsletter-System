<?php
// wcf imports
require_once(WCF_DIR.'lib/action/AbstractAction.class.php');
require_once(WCF_DIR.'lib/data/mail/Mail.class.php');
require_once(WCF_DIR.'lib/data/message/newsletter/log/Log.class.php');
require_once(WCF_DIR.'lib/data/message/newsletter/Newsletter.class.php');
require_once(WCF_DIR.'lib/data/message/newsletter/ViewableNewsletter.class.php');
require_once(WCF_DIR.'lib/data/message/pm/PMEditor.class.php');
require_once(WCF_DIR.'lib/data/user/User.class.php');
require_once(WCF_DIR.'lib/system/newsletter/LogManager.class.php');

/**
 * Sends the remaining mails for the specified newsletter.
 *
 * @author Jim Martens
 * @copyright 2013 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage acp.action
 * @category Community Framework
 */
class SendRemainingMailsAction extends AbstractAction {
	/**
	 * Contains the newsletter id.
	 * @var integer
	 */
	protected $newsletterID = 0;
	
	/**
	 * Contains the unsubscription table.
	 * @var string
	 */
	protected $unsubscriptionTable = 'newsletter_unsubscription';
	
	/**
	 * @see AbstractAction::readParameters()
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
		$this->sendNewsletter();
		
		$this->executed();
		if ($this->newsletterID) {
			HeaderUtil::redirect('index.php?page=NewsletterList&success&packageID='.PACKAGE_ID.SID_ARG_2ND_NOT_ENCODED);
			exit;
		}
	}
	
	/**
	 * Sends the newsletter.
	 */
	protected function sendNewsletter() {
		// read cache
		$cacheName = 'newsletter-subscriber-'.PACKAGE_ID;
		WCF::getCache()->addResource($cacheName, WCF_DIR.'cache/cache.'.$cacheName.'.php', WCF_DIR.'lib/system/cache/CacheBuilderNewsletterSubscriber.class.php');
		$subscribersList = WCF::getCache()->get($cacheName, 'subscribers');
		$unsubscribeTokens = WCF::getCache()->get($cacheName, 'unsubscribeTokens');
		
		// determine the list of subscribers who really need the newsletter
		$log = new Log($this->newsletterID);
		$receivedList = $log->receivedList;
		$realSubscribersList = array_diff_key($subscribersList, $receivedList);
		// register newsletter in log
		LogManager::getInstance()->setNewsletter($this->newsletterID);
		
		$newsletter = new Newsletter($this->newsletterID);
		
		//workaround to make sure that the template is found
		$templatePaths = array(
			WCF_DIR.'templates/',
			WCF_DIR.'acp/templates/'
		);
		WCF::getTPL()->setTemplatePaths($templatePaths);
		
		$newsletterObj = new ViewableNewsletter($id);
		$emailText = $newsletterObj->getFormattedMessage();
		WCF::getTPL()->assign(array(
		'subject' => $newsletter->subject,
		'text' => $emailText
		));
		$content = WCF::getTPL()->fetch($templateName);
		$i = 0;
		usleep(1);
		//sending one mail per subscriber
		//is longer, but safer
		foreach ($realSubscribersList as $subscriber) {
			//sleep 2 seconds after 10 sent mails
			if (fmod($i, 10) == 0) {
				usleep(2000000);
			}
			$unsubscribeToken = '';
			if (!isset($unsubscribeTokens[$subscriber['subscriberID']])) {
				$unsubscribeToken = StringUtil::getRandomID();
				$sql = 'INSERT INTO wcf'.WCF_N.'_'.$this->unsubscriptionTable.'
                			(subscriberID, token)
                		VALUES
                			('.intval($subscriber['subscriberID']).", '".
		                			escapeString($unsubscribeToken)."')";
				WCF::getDB()->sendQuery($sql);
			}
			else {
				$unsubscribeToken = $unsubscribeTokens[$subscriber['subscriberID']]['token'];
			}
		
			$recipient = null;
			if ($subscriber['userID']) {
				$recipient = new User($subscriber['userID']);
					
				// check for non receiving groups
				if (!NewsletterUtil::canReceiveNewsletters($recipient)) {
					continue;
				}
			}
		
			// {$username} stands for the username of the specific subscriber
			// emails are open to both registered users and guests
			if (is_null($recipient) || $recipient->getUserOption('acceptNewsletterAsEmail')) {
				$tmpContent = str_replace('{$username}', $subscriber['username'], $content);
				$tmpContent = str_replace('subscriberID', $subscriber['subscriberID'], $tmpContent);
				$tmpContent = str_replace('token', $unsubscribeToken, $tmpContent);
				$email = $subscriber['email'];
				$mail = new Mail($email, $newsletter['subject'], $tmpContent,
					MESSAGE_NEWSLETTERSYSTEM_GENERAL_FROM);
				//$mail->addBCC(MAIL_ADMIN_ADDRESS); would result in x mails
				$mail->setContentType('text/html');
				$mail->send();
				// logs successful mail
				LogManager::getInstance()->addSubscriber($subscriber['subscriberID'], $email);
			}
			// subscriber has to be a registered user
			if (!is_null($recipient) && $recipient->getUserOption('acceptNewsletterAsPM')) {
				$recipientArray = array();
				$recipientArray[] = array(
					'userID' => $subscriber['userID'],
					'username' => $subscriber['username']
				);
				$admin = new User(MESSAGE_NEWSLETTERSYSTEM_GENERAL_ADMIN);
				$options = array(
					'enableSmilies' => $newsletter['enableSmilies'],
					'enableHtml' => $newsletter['enableHtml'],
					'enableBBCodes' => $newsletter['enableBBCodes']
				);
				$tmpText = str_replace('{$username}', $subscriber['username'], $text);
				$pm = PMEditor::create(false, $recipientArray, array(), $newsletter['subject'], $tmpText, $admin->userID, $admin->username, $options);
			}
			$i++;
		}
		LogManager::getInstance()->destroyInstance();
		WCF::getCache()->clearResource('newsletter-subscriber-'.PACKAGE_ID);
	}
}
