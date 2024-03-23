<?php

class DnrAPI {

  private $_publicUrl = 'http://216.183.222.200:3434/mhc_/index.php/helper/fetchDNRIdRecords';

  public function checkDNR($id_no, $name) {
    $getUrl = $this->_publicUrl . '?name='.trim($name).'&idNo='.trim($id_no);
    $ch = curl_init($getUrl);

    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_TIMEOUT,10);
    $response = curl_exec($ch);
    $data = json_decode($response);
    curl_close($ch);

    if (empty($data))
      return false;

    return $data[0];
    

  }
}