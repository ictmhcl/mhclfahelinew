<?php

/*
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from ... in writing.
 */

/**
 * Description of mhcPoll
 *
 * @author nazim
 */
class MhcPoll {

  const POLLING_ENABLED = true;
  const KEY = "lkdys87sdojfsdgp948t396ns-pget0w47i30268096pofs7g-sd89et7";

  // paths
  const MEMBER_UPDATE_URL = "polling/member/";
  const TRANSACTION_UPDATE_URL = "polling/transaction/";
  const MAX_MEMBER_ID_URL = "polling/maxMemberId/";
  const MAX_TRANSACTION_ID_URL = "polling/maxTransactionId/";
  const MEMBER_ID_LIST = "polling/memberIdList/";
  const TRANSACTION_ID_LIST = "polling/transactionIdList/";
  const NEW_RECEIPT_NO = "polling/issueReceiptNo";
  const ISSUE_MEMBER_ID = "polling/IssueMemberID";
  const NEW_MEMBER_IDS = "polling/newMemberIds";
  const NEW_MEMBER_DATA = "polling/newMemberData";
  const NEW_TRANSACTION_DATA = "polling/newTransactionData";

  // Products
  const PRODUCT_HAJJ = 1;

  private static $umraProducts = [
    1 => 2, 2 => 3, 3 => 4, 4 => 5, 5 => 6, 6 => 7, 7 => 8, 8 => 9, 9 => 10,
    10 => 11, 11 => 12
  ];

  public static function updateMember($id) {


    /** @var Members $member */
    $member = Members::model()->with('person')->findByPk($id);

    $memberData = new stdClass();
    $memberData->id = $member->id;
    $memberData->mhc_no = $member->mhc_no;
    $memberData->full_name = $member->person->full_name_english;
    $memberData->full_name_dhivehi = $member->person->full_name_dhivehi;
    $memberData->id_no = $member->person->id_no;
    $memberData->gender = $member->person->gender->name_english;
    $memberData->perm_address = $member->person->permAddressText;
    $memberData->cur_address = $member->current_address_english;
    $memberData->phone = $member->phone_number_1;
    $memberData->email = $member->email_address;
    $memberData->family_info = $member->familyInfoText;
    $memberData->emergency_contact_info = $member->emergencyContactInfoText;
    $memberData->job_info = $member->jobInfoText;
    $memberData->hajj_info = $member->hajjInfoText;
    $memberData->mahram_info = $member->mahramInfoText;
    $memberData->deposit_info = $member->depositsInfoText;
    $memberData->replacement_info = $member->replacementInfoText;
    $memberData->caretaker_info = $member->caretakerInfoText;
    $memberData->membership_date = $member->membership_date;
    $memberArray = (array)$memberData;

    return self::pollData(self::MEMBER_UPDATE_URL, ['member' => $memberArray]);
  }

  public static function updateTransaction($id) {
    $transaction = MemberTransactions::model()->findByPk($id);

    return self::pollData(self::TRANSACTION_UPDATE_URL, [
      'transaction' => $transaction->attributes
    ]);
  }

  public static function pollMaxMemberId() {
    return self::pollData(self::MAX_MEMBER_ID_URL, [], true);
  }

  public static function pollNewReceiptNo($type = 'hajj', $id = null) {
    switch ($type) {
      case 'hajj':
        return self::pollData(self::NEW_RECEIPT_NO,
          ['id' => self::PRODUCT_HAJJ], true);
        break;
      case 'umra':
        return self::pollData(self::NEW_RECEIPT_NO,
          ['id' => self::$umraProducts[$id]], true);
        break;
    }
  }

  public static function pollNewMemberID($id, $type = 'hajj') {
    switch ($type) {
      case 'hajj':
        return self::pollData(self::ISSUE_MEMBER_ID,
          ['id' => self::PRODUCT_HAJJ], true);
        break;
      case 'umra':
        return self::pollData(self::ISSUE_MEMBER_ID,
          ['id' => self::$umraProducts[$id]], true);
        break;
    }
  }

  private static function umraProduct($id) {
    return $id + 1;
  }

  public static function pollMemberIdList() {
    return self::pollData(self::MEMBER_ID_LIST, [], true);
  }

  public static function pollMaxTransactionId() {
    return self::pollData(self::MAX_MEMBER_ID_URL, [], true);
  }

  public static function pollNewMemberIds() {
    return self::pollData(self::NEW_MEMBER_IDS, [], true);
  }

  public static function pollNewOnlineMemberDetails(array $ids) {
    return self::pollData(self::NEW_MEMBER_DATA, ['ids' => implode(',', $ids)],
      true);
  }

  public static function pollNewOnlineTransactionDetails(array $ids) {
    return self::pollData(self::NEW_TRANSACTION_DATA, ['ids' => implode(',',
      $ids)], true);
  }

  public static function pollTransactionIdList() {
    return self::pollData(self::TRANSACTION_ID_LIST, [], true);
  }

  /**
   * @param            $url
   * @param array      $data
   * @param bool|false $returnData
   *
   * @return bool | array
   * @throws CException
   */
  private static function pollData($url, array $data, $returnData = false) {

    if (!self::POLLING_ENABLED)
      return false;

    Yii::import('ext.CHttpClient');
    $target = Constants::MHC_DATA_APP_SERVER . $url . '?'
      . http_build_query(['key' => self::KEY] + $data);

    try {
      $response = Yii::createComponent('CHttpClient')->fetch($target);
    } catch (Exception $ex) {
      return false;
    }

    if ($response->status == 200) {
      $result = CJSON::decode($response->body);
      if ($result['status'] == 'success')
        if ($returnData)
          return !isset($result['data']) ? false : $result['data'];
        else
          return true;

      if (Yii::app() instanceof CConsoleApplication)
      {
        $url = rawurldecode($target);
        print "\n Error from: $url\n" . json_encode
          ($result['data'],
            JSON_PRETTY_PRINT);
      }
      Helpers::error(["Error from: $target", $result['data']]);

      return false;

    } else
      return false;
  }

}
