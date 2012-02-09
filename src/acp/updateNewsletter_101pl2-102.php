<?php
//wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheHandler.class.php');

CacheHandler::clear(WCF_DIR.'cache', 'cache.newsletter-*.php');