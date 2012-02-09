<?php
//wcf imports
require_once(WCF_DIR.'lib/data/message/newsletter/Newsletter.class.php');
require_once(WCF_DIR.'lib/data/message/bbcode/MessageParser.class.php');

/**
 * Represents a viewable newsletter.
 *
 * @author Jim Martens
 * @copyright 2012 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage data.message.newsletter
 * @category Community Framework
 */
class ViewableNewsletter extends Newsletter {
    
    /**
     * Returns formatted message.
     */
    public function getFormattedMessage() {
        $parser = MessageParser::getInstance();
        $parser->setOutputType('text/html');
        return $parser->parse($this->text, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes, false);
    }
}
