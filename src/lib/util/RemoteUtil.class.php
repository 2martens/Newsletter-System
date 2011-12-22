<?php

/**
 * Implements some useful functions for remote control
 *
 * @author Jim Martens
 * @copyright 2011 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.newsletter
 * @subpackage util
 * @category Community Framework
 */
class RemoteUtil {
    
    /**
     * Sends a HTTP request with the given parameters.
     *
     * @param string $request
     * @param string $url
     * @param boolean $defaultEntries
     * @param string $authLine
     *
     * @return array $return
     */
    public static function sendRequest($request, $url, $defaultEntries = true, $authLine = '') {
        $urlParsed = $url;
        if ($defaultEntries) {
            $request .= 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:8.0) Gecko/20100101 Firefox/8.0'."\r\n";
            $request .= 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'."\r\n";
            $request .= 'Accept-Language: de-de,de;q=0.8,en-us;q=0.5,en;q=0.3'."\r\n";
            $request .= 'Accept-Encoding: gzip, deflate'."\r\n";
            $request .= 'Accept-Charset: UTF-8,*'."\r\n";
            $request .= 'DNT: 1'."\r\n";
            $request .= 'Connection: keep-alive'."\r\n";
        }
        if (!empty($authLine)) $request .= $authLine."\r\n";
        $request .= "\r\n";
        
        $timeout = (int)round(10/2+0.00000000001);
        $pointer = fsockopen($urlParsed['host'], $urlParsed['port'], $errno, $errstr, $timeout);
        socket_set_timeout($pointer, $timeout);
        
        fwrite($pointer, $request);
        $response = '';
        $status = socket_get_status($pointer);
        while (!$status['timed_out'] && !$status['eof']) {
            $response .= fgets($pointer);
            $status = socket_get_status($pointer);
        }
        fclose($pointer);
        $res = str_replace("\r\n", "\n", $response);
        $res = str_replace("\r", "\n", $res);
        $res = str_replace("\t", ' ', $res);
        $ares = explode("\n", $res);
        $firstLine = explode(' ', array_shift($ares), 3);
        
        $return = array();
        $return['status'] = StringUtil::trim($firstLine[1]);
        $return['reason'] = StringUtil::trim($firstLine[2]);
        foreach ($ares as $line) {
            $temp = explode(':', $line, 2);
            if (isset($temp[0]) and isset($temp[1])) {
                $return[strtolower(StringUtil::trim($temp[0]))] = StringUtil::trim($temp[1]);
            }
        }
        $return['_response'] = $response;
        $return['_request'] = $request;

        return $return;
    }
    
    /**
     * Returns the real response part of the response.
     *
     * @param array $responseArray
     *
     * @return array $return
     */
    public static function getResponse(array $responseArray) {
        $return = array();
        $response = $responseArray['_response'];
        $res = str_replace("\r\n", "\n", $response);
        $res = str_replace("\r", "\n", $res);
        $res = str_replace("\t", ' ', $res);
        $tmpArray = explode("\n", $res);
        array_shift($tmpArray);
        foreach ($tmpArray as $line) {
            if (empty($line) || strpos($line, ':') || $line == '0') continue;
            $return[] = $line;
        }
        array_shift($return);
        return $return;
    }
}
