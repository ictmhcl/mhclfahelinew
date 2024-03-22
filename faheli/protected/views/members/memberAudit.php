<?php
/* @var $this MembersController */
/* @var $transaction MemberTransactions */
/* @var $form CActiveForm */
$model = $transaction->member;
$appForm = $model->applicationForm;
$formRecordedBy = $appForm->operationLog->createdUser->person;
$formRecordedTime = $appForm->operationLog->created_time;
$verifiedBy = (!empty($appForm->applicationFormVerification->operationLog->modifiedUser) ?
  $appForm->applicationFormVerification->operationLog->modifiedUser->person :
  $appForm->applicationFormVerification->operationLog->createdUser->person);
$verifiedTime = (!empty($appForm->applicationFormVerification->operationLog->modifiedUser) ?
  $appForm->applicationFormVerification->operationLog->modified_time :
  $appForm->applicationFormVerification->operationLog->created_time);
$updateAudits = ClientAudit::auditLogDataProvider($model, ClientAudit::AUDIT_ACTION_EDIT)->getData();
$memberUpdatedByName = !empty($updateAudits) ?
  $updateAudits[0]->Users->full_name: "-";
$memberUpdatedTime = !empty($updateAudits) ?
  $updateAudits[0]->dateTime: "-";

Yii::import('ext.CurrencyText');
//$i = 1;
//$copies = (!empty(Helpers::config('receiptCopies')))?Helpers::config('receiptCopies'):$i;
//if ($view)
//    $copies = 1;

?>
<?php if (!$view) { ?>
  <script type="text/javascript">
    window.onload = function () {
      window.print();
      window.location.href = "<?php echo $this->createUrl('statement',['id'=>$model->id])?>";
    };
  </script>
<?php } ?>
<div
  style="-webkit-print-color-adjust:exact;border:1px black solid; margin: 5px; padding:10px; width: 7in; height: 4.5in; font-family: Arial">
  <div style="float: right; font-family:FarumaWeb,Faruma; font-size: 18px">
    މޯލްޑިވްސް ޙައްޖު ކޯޕަރޭޝަން ލިމިޓެޑް
    <?php echo CHtml::image(Helpers::sysUrl(Constants::IMAGES) . 'logo-only-small.png', null, ['style' => 'vertical-align:middle;padding:2px']); ?>
  </div>
  <div style="float: left; direction: rtl">
<!--    --><?php //echo Helpers::receiptNumber($transaction) ?>
  </div>
  &nbsp;&nbsp;&nbsp;
  <div style="float: left; direction: rtl">
<!--    <span style="font-family: FarumaWeb,Faruma">ނަމްބަރު:&nbsp;&nbsp;</span>-->
  </div>
  <br>

  <div style="float: left; direction: rtl">
<!--    <span style="font-family: FarumaWeb,Faruma">--><?php //echo Helpers::mvDate($transaction->transaction_time) . '، ' . date('H:i', strtotime($transaction->transaction_time)) ?><!--</span>-->
  </div>
  &nbsp;&nbsp;&nbsp;
  <div style="float: left; direction: rtl">
<!--    <span style="font-family: FarumaWeb,Faruma">ތާރީޚް / ގަޑި:&nbsp;</span>-->
  </div>
  <br>
  <br>

  <div style="text-align:center; width:7in;font-size: 25px; font-family:
	FarumaWeb, Faruma">މެމްބަރޝިޕް މަޢުލޫމާތު
  </div>
  <br>
  <hr>
  <table style="font-family: FarumaWeb,Faruma; font-size: 14px">
    <tr>
      <td style="width: 1.5in; text-align: right">
        <span
          style="font-family: Arial;width:100%; border-bottom: 1px dotted grey"><?php echo $model->MHC_ID ?></span>
      </td>
      <td style="width: 1in; text-align: right; direction: rtl">މެމްބަރ
        ނަމްބަރ:
      </td>
      <td style="width: 2.5in; text-align: right; direction: rtl">
        <span
          style="width:100%; border-bottom: 1px dotted grey"><?php echo $model->person->full_name_dhivehi . '<span style="font-family:Arial"> (' . $transaction->member->person->id_no . ')</span>' ?></span>
      </td>
      <td style="width: 2in; text-align: right">:މެމްބަރުގެ ނަން</td>
    </tr>
    <tr>
      <td style="width: 5in; text-align: right" colspan="3">
        <span
          style="width:100%; border-bottom: 1px dotted grey"><?php echo $model->person->getPermAddressTextDhivehi() ?></span>
      </td>
      <td style="width: 2in; text-align: right">:ދާއިމީ އެޑްރެސް</td>
    </tr>
  </table>
  <hr>
  <table style="font-family: FarumaWeb,Faruma; font-size: 14px">
    <tr>
      <td
        style="width: 5.5in; direction: rtl;text-align: right; background:#eee;">
        <span style="width:100%;  ">

          <?=$formRecordedBy->full_name_dhivehi . " (" .
          $formRecordedBy->id_no . ") ގަޑި: " . Helpers::mvDate
          ($formRecordedTime, true)?>
        </span><br>ސޮއި:<br><br>
      </td>
      <td style="width: 1.5in; text-align: right">:ފޯމް ބަލައިގަތީ</td>
    </tr>
    <tr>
      <td
        style="width: 5.5in; direction: rtl;text-align: right; background:#eee;">
        <span style="width:100%;  ">
          <?=$verifiedBy->full_name_dhivehi . " (" .
          $verifiedBy->id_no . ") ގަޑި: " . Helpers::mvDate
          ($verifiedTime, true)?>
        </span><br>ސޮއި:<br><br>
      </td>
      <td style="width: 1.5in; text-align: right">:ފޯމް ވެރިފައިކުރީ</td>
    </tr>
    <tr>
      <td
        style="width: 5.5in; direction: rtl;text-align: right; background:#eee;">
        <span style="width:100%;  ">
          <?=
          $transaction->user->person->full_name_dhivehi . " (" .
          $transaction->user->person->id_no . ") ގަޑި: " . Helpers::mvDate
          ($verifiedTime, true)?>، ޢަދަދު: <?=Helpers::currency
          ($transaction->amount)?> ރުފިޔާ
        </span><br>ސޮއި:<br><br>
      </td>
      <td style="width: 1.5in; text-align: right">:ފައިސާ ބަލައިގަތީ</td>
    </tr>
  </table>


</div>

