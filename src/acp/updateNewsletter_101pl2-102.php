<?php
//wcf imports
require_once(WCF_DIR.'lib/system/cache/CacheHandler.class.php');

WCF::getCache()->clear(WCF_DIR.'cache', 'cache.newsletter-*.php');