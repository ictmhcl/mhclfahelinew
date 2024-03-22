<?php

/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * PHP version 5
 *
 * Copyright (C) Maldives Hajj Corporation Limited - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @category   Pilgrims Management System
 * @package    Ooredoo Messaging Class
 * @author     Mohamed Nazim ict@mhcl.mv
 * @authored   1/9/2017 6:21 AM
 * @copyright  2017 Maldives Hajj Corporation Limited
 * @license    Closed Source.
 *
 * File name: uriduMsg.php
 */
class uriduMsg {
  private $url = 'https://o-papi1-lb01.ooredoo.mv/send_sms/V1.0';
  private $_token = '3dbb248b-e52f-3454-b9fc-1f77368c4706';
  private $username = 'hajjco';
  private $password = 's44NxG2G!]';
  private $messageLength = 500;

  public $numbers = null;
  public $message = null;

public function sendMessage() {
    if (empty($GLOBALS['cfg']['sendVerificationCode']))
      return true; // Assume message has been sent
    if (empty($this->numbers) || empty($this->message))
      throw new UrdMsgException("Number(s) and a Message must be provided");
    $this->message = $this->cleanupMessage($this->message);
    if (!$this->checkAscii($this->message))
      throw new UrdMsgException("Only ASCII Characters are acceptable for sending text messages");
    if (strlen($this->message) > $this->messageLength)
      throw new UrdMsgException("Message has " . strlen($this->message) .
        " characters. Maximum allowed is " . $this->messageLength .
        " characters.");
    //$this->message = $this->encodeMessage($this->message);
    if (!$this->checkValidMaldivianNumbers($this->numbers))
    {
      $numbersImploded = is_array($this->numbers)?implode(", ",
        $this->numbers):$this->numbers;

        throw new UrdMsgException("All numbers must be valid Maldivian phone numbers. Tried with (".$numbersImploded.")");
    }

    $recipient = $this->encodeNumbers($this->numbers);

    $ch = curl_init();
    try {
		
		curl_setopt_array($ch, array(
		  CURLOPT_URL => $this->url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => http_build_query([
			'user' => $this->username,
			'pass' => $this->password,
			'batch' => $recipient,
			'msg' => $this->message
		  ]),
		  CURLOPT_HTTPHEADER => array(
			'Accept: application/json',
			'Authorization: Bearer ' . $this->_token,
//			'Cookie: visid_incap_2388963=UCQNxON3R8SAGbTl0nBy/ieycV8AAAAAQUIPAAAAAADXIFcdRiQpR2GGf9fDsCB2; incap_ses_968_2388963=CQWcLMTYbEuwGcZQPAdvDb1ZMGEAAAAAL8tn2BIt2Kxufx0gv6gLBg==; visid_incap_2403927=3u8iSiA5RVWwx2b/dfXkEoNaMGEAAAAAQUIPAAAAAADwsG8ezTO8s3MDTRrB7qAx; incap_ses_1234_2403927=oMG5IPQpdVjebLTP3AwgEYNaMGEAAAAAzViTVAmemsAeR4UsgSgGTw=='
		  ),
//		  CURLOPT_USERAGENT => 'MHCL-Server'
		));
		
/*       curl_setopt($ch, CURLOPT_URL, $this->url);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $this->_token));
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'user' => $this->username,
        'pass' => $this->password,
        'batch' => $recipient,
        'msg' => $this->message
      ]));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_USERAGENT, 'MHCL-Server');
 */
      $response = curl_exec($ch);
      curl_close($ch);

      if (is_object($responseArr = json_decode($response))
        && isset($responseArr->status) && $responseArr->status == 100) {
        return true;
      }

      throw new UrdMsgException("Result not an array or error_occurred. Got: " .
        $response, 500);
    } catch (Exception $ex) {
      ErrorLog::exceptionLog(new CException($ex->getMessage(),
        $ex->getCode()));
      return false;
    }
  }
  private function checkValidMaldivianNumbers($numbers) {
    if (!is_array($numbers))
      $numbers = [$numbers];

    foreach($numbers as $number)
      if (!Helpers::validMaldivianPhoneNumber($number))
        return false;
    return true;
  }

  private function encodeMessage($message) {
    return urlencode(urldecode($message));
  }

  private function encodeNumbers($numbers) {
    return '960' . urlencode(implode(' 960',$numbers));
  }

  private function cleanupMessage($message) {
    $message = str_replace(chr(ord("`")), "'", $message);
    $message = str_replace(chr(ord("´")), "'", $message);
    $message = str_replace(chr(ord("„")), ",", $message);
    $message = str_replace(chr(ord("`")), "'", $message);
    $message = str_replace(chr(ord("´")), "'", $message);
    $message = str_replace(chr(ord("“")), "\"", $message);
    $message = str_replace(chr(ord("”")), "\"", $message);
    $message = str_replace(chr(ord("´")), "'", $message);

    return $message;
  }

  private function checkAscii($message) {
    $x = 0;
    while ($x < strlen($message)) {
      if (ord(substr($message,$x, 1)) > 127) {
        return false;
      }
      $x++;
    }
    return true;
  }

}