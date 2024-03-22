<?php
/* @var $this UmraController */
/* @var $pilgrim UmraPilgrims */
/* @var $form CActiveForm */
/* @var $transaction UmraTransactions */
$pilgrim = $transaction->umraPilgrim;
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
      window.location.href = "<?php echo $this->createUrl('umraStatement',
    ['id'=>$pilgrim->id])?>";
    };
  </script>
<?php } ?>
<div
  style="-webkit-print-color-adjust:exact;border:1px black solid; margin: 5px; padding:10px; width: 7in; height: 4.5in; font-family: Arial">
  <div style="float: right; font-family:FarumaWeb,Faruma; font-size: 18px">
    މޯލްޑިވްސް ޙައްޖު ކޯޕަރޭޝަން ލިމިޓެޑް
    <?php echo CHtml::image(Helpers::sysUrl(Constants::IMAGES) . 'logo-only-small.png', null, ['style' => 'vertical-align:middle;padding:2px']); ?>
  </div>
  <div
    style="float: left; direction: rtl"><?php echo Helpers::umraReceiptNumber($transaction)
    ?></div>
  &nbsp;&nbsp;&nbsp;
  <div style="float: left; direction: rtl"><span
      style="font-family: FarumaWeb,Faruma">ނަމްބަރު:&nbsp;&nbsp;</span></div>
  <br>

  <div style="float: left; direction: rtl"><span
      style="font-family: FarumaWeb,Faruma"><?php echo Helpers::mvDate($transaction->transaction_time) . '، ' . date('H:i', strtotime($transaction->transaction_time)) ?></span>
  </div>
  &nbsp;&nbsp;&nbsp;
  <div style="float: left; direction: rtl"><span
      style="font-family: FarumaWeb,Faruma">ތާރީޚް / ގަޑި:&nbsp;</span></div>
  <br>
  <br>

  <div
    style="text-align:center; width:7in;font-size: 25px; font-family: FarumaWeb, Faruma">
    ފައިސާގެ ރަސީދު
  </div>
  <br>
  <?php
  if ($transaction->is_cancelled || $transaction->revised) {
    ?>
    <div style="text-align:center; width:7in;font-size: 12px; font-family:
    	FarumaWeb, Faruma; margin: -18px 0">
      (<?=$transaction->is_cancelled ? "ކެންސަލް ކުރެވިފައި" : "އިސްލާހު ކުރެވިފައި"?>)
    </div><br>
    <?php
  }
  ?>
  <hr>
  <table style="font-family: FarumaWeb,Faruma; font-size: 14px">
    <tr>
      <td style="width: 1.5in; text-align: right">
        <span
          style="font-family: Arial;width:100%; border-bottom: 1px dotted grey"><?php echo $pilgrim->getUMRA_PILGRIM_ID() ?></span>
      </td>
      <td style="width: 1in; text-align: right; direction: rtl">ޢުމްރާ
        ނަމްބަރ:
      </td>
      <td style="width: 2.5in; text-align: right; direction: rtl">
                <span style="width:100%; border-bottom: 1px dotted
                grey"><?php echo $pilgrim->person->full_name_dhivehi . '<span
                style="font-family:Arial"> ('
                    . $transaction->umraPilgrim->person->id_no . ')</span>' ?></span>
      </td>
      <td style="width: 2in; text-align: right">:މެމްބަރުގެ ނަން</td>
    </tr>
    <tr>
      <td style="width: 5in; text-align: right" colspan="3">
        <span
          style="width:100%; border-bottom: 1px dotted grey"><?php echo $pilgrim->person->getPermAddressTextDhivehi() ?></span>
      </td>
      <td style="width: 2in; text-align: right">:ދާއިމީ އެޑްރެސް</td>
    </tr>
  </table>
  <hr>
  <table style="font-family: FarumaWeb,Faruma; font-size: 14px">
    <tr>

      <td
        style="width: 5in; direction: rtl;text-align: right; background: #ddd;">
        <span
          style="width:100%;  ">&nbsp;<?php echo $transaction->description_dhivehi ?></span>
      </td>
      <td style="width: 2in; text-align: right">:ތަފްޞީލް</td>
    </tr>
    <tr>
      <td style="direction: rtl;text-align: right; background: #ddd;">
        <?php echo '<span style="font-family:Arial;">' . Helpers::currency($transaction->amount) . '</span>' ?>
        ރުފިޔާ
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(މިހާތަނަށް ޖުމްލަ
        ޖަމާކުރެވުނު
        ޢަދަދު: <?= '<span style="font-family:Arial;">' . Helpers::currency($transaction->balance) . '</span>' ?>
        ރުފިޔާ)
      </td>
      <td style="width: 2in; text-align: right">:ޖަމާކުރި ޢަދަދު</td>
    </tr>
    <tr>
      <td
        style="width: 5in; direction: rtl;text-align: right; background: #ddd;">
                <span
                  style="width:100%; ; direction: rtl"><?php echo '(' . CurrencyText::parse($transaction->amount, 'dv') . ')' ?>
                </span>
      </td>
      <td style="width: 2in; text-align: right"></td>
    </tr>
    <tr>
      <td
        style="width: 5in; direction: rtl;text-align: right; background: #ddd;">
                <span
                  style="width:100%; ; direction: rtl"><?php echo $transaction->transactionMedium->name_dhivehi ?>
                </span>
      </td>
      <td style="width: 2in; text-align: right">:ފައިސާ ދެއްކި ގޮތް</td>
    </tr>
  </table>
  <hr>
  <table style="font-family: FarumaWeb,Faruma; font-size: 12px">
    <tr style="color:#666">
      <td style="width: 1.5in; text-align: right;direction: rtl">
        <span style="width:100%;">&nbsp;</span>
      </td>
      <td style="width: 1.5in; text-align: right; direction: rtl"></td>
      <td style="width: 2.5in; text-align: right; direction: rtl">
                <span
                  style="width:100%;">&nbsp;<?php echo $transaction->user->person->full_name_dhivehi .
                    ' (އައިޑީ ' . $transaction->user->person->id_no . ')
                    ' ?>&nbsp;</span>
      </td>
      <td style="width: 1.5in; text-align: right; direction: rtl">ފައިސާ
        ބަލައިގަތީ:
      </td>
    </tr>
  </table>


</div>

