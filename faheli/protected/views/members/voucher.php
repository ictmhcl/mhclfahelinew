<?php
/* @var $this MembersController */
/* @var $model Members */
/* @var $form CActiveForm */
//$this->menu = StaticMenus::$memberMenus;
Yii::import('ext.CurrencyText');
//$i = 1;
//$copies = (!empty(Helpers::config('receiptCopies')))?Helpers::config('receiptCopies'):$i;
//if ($view)
//    $copies = 1;

?>
<?php if (!$view) { ?>
<script type="text/javascript">
    window.onload=function(){
        window.print();
    window.location.href = "<?php echo $this->createUrl('statement',['id'=>$model->id])?>";
    };
</script>
<?php } ?>
<div style="-webkit-print-color-adjust:exact;border:1px black solid; margin: 5px; padding:10px; width: 7in; height: 4.5in; font-family: Arial">
    <div style = "float: right; font-family:FarumaWeb,Faruma; font-size: 18px">މޯލްޑިވްސް ޙައްޖު ކޯޕަރޭޝަން ލިމިޓެޑް
        <?php echo CHtml::image(Helpers::sysUrl(Constants::IMAGES) . 'logo-only-small.png', null, ['style' => 'vertical-align:middle;padding:2px']); ?>
    </div>
  <div style = "float: left; direction: rtl"><?php echo Helpers::refundVoucherNumber($memberTransaction) ?></div>&nbsp;&nbsp;&nbsp;
    <div style = "float: left; direction: rtl"><span style="font-family: FarumaWeb,Faruma">ނަމްބަރު:&nbsp;&nbsp;</span></div><br>
    <div style = "float: left; direction: rtl"><span style="font-family: FarumaWeb,Faruma"><?php echo Helpers::mvDate($memberTransaction->transaction_time).'، '.date('H:i',strtotime($memberTransaction->transaction_time)) ?></span></div>&nbsp;&nbsp;&nbsp;
    <div style = "float: left; direction: rtl"><span style="font-family: FarumaWeb,Faruma">ތާރީޚް / ގަޑި:&nbsp;</span></div><br>
	<br>
	<div style = "text-align:center; width:7in;font-size: 25px; font-family: FarumaWeb, Faruma">ރީފަންޑް ވައުޗަރ</div><br>
  <?php
  if ($memberTransaction->is_cancelled || $memberTransaction->revised) {
    ?>
    <div style="text-align:center; width:7in;font-size: 12px; font-family:
    	FarumaWeb, Faruma; margin: -18px 0">
      (<?=$memberTransaction->is_cancelled ? "ކެންސަލް ކުރެވިފައި" : "އިސްލާހު ކުރެވިފައި"?>)
    </div><br>
    <?php
  }
  ?>
  <hr>
    <table style="font-family: FarumaWeb,Faruma; font-size: 14px">
        <tr>
            <td style="width: 1.5in; text-align: right">
                <span style="font-family: Arial;width:100%; border-bottom: 1px dotted grey"><?php echo $model->MHC_ID?></span>
            </td>
            <td style="width: 1in; text-align: right; direction: rtl">މެމްބަރ ނަމްބަރ:</td>
            <td style="width: 2.5in; text-align: right; direction: rtl">
                <span style="width:100%; border-bottom: 1px dotted grey"><?php echo $model->person->full_name_dhivehi . '<span style="font-family:Arial"> ('.$memberTransaction->member->person->id_no.')</span>' ?></span>
            </td>
            <td style="width: 2in; text-align: right">:މެމްބަރުގެ ނަން</td>
        </tr>
        <tr>
            <td style="width: 5in; text-align: right" colspan="3">
                <span style="width:100%; border-bottom: 1px dotted grey"><?php echo $model->person->getPermAddressTextDhivehi()?></span>
            </td>
            <td style="width: 2in; text-align: right">:ދާއިމީ އެޑްރެސް</td>
        </tr>
    </table>
    <hr>
    <table style="font-family: FarumaWeb,Faruma; font-size: 14px">
        <tr>
            
            <td style="width: 5in; direction: rtl;text-align: right; background: #ddd;">
                <span style="width:100%;  ">&nbsp;<?php echo $memberTransaction->description_dhivehi?></span>
            </td>
            <td style="width: 2in; text-align: right">:ތަފްޞީލް</td>
        </tr>
        <tr>
            <td style="width: in; direction: rtl;text-align: right; background: #ddd;">
                <span style="width:100%; ; direction: rtl"><?php echo '<span style="font-family:Arial;">'.Helpers::currency(abs($memberTransaction->amount)).'</span>' ?></span>
                <span style="width:100%; ; direction: rtl">ރުފިޔާ</span>
            </td>
            <td style="width: 2in; text-align: right">:ރައްދުކުރި ޢަދަދު</td>
        </tr>
        <tr>
            <td style="width: 5in; direction: rtl;text-align: right; background: #ddd;">
                <span style="width:100%; ; direction: rtl"><?php echo '('.CurrencyText::parse(abs($memberTransaction->amount),'dv').')' ?>
                </span>
            </td>
            <td style="width: 2in; text-align: right"></td>
        </tr>
        <tr>
            <td style="width: 5in; direction: rtl;text-align: right; background: #ddd;">
                <span style="width:100%; ; direction: rtl"><?php echo $memberTransaction->transactionMedium->name_dhivehi ?>
                </span>
            </td>
            <td style="width: 2in; text-align: right">:ފައިސާ ރައްދުކުރި ގޮތް</td>
        </tr>
    </table>
    <hr>
    <table style="font-family: FarumaWeb,Faruma; height: 0.5in; font-size: 14px">
        <tr style="color:#333">
            <td style="width: 1.5in; text-align: right;direction: rtl">
                <span style="width:100%;">&nbsp;</span>
            </td>
            <td style="width: 1.5in; text-align: right; direction: rtl">ސޮއި:</td>
            <td style="width: 2.5in; text-align: right; direction: rtl">
                <span style="width:100%;">&nbsp;<?php echo $memberTransaction->user->person->full_name_dhivehi
                        . ' (އައިޑީ ' . $memberTransaction->user->person->id_no . ') '?>&nbsp;</span>
            </td>
            <td style="width: 1.5in; text-align: right; direction: rtl">ފައިސާ ދޫކުރި ފަރާތ:</td>
        </tr>
    </table>
    <hr>
    <table style="font-family: FarumaWeb,Faruma; height: 0.8in; font-size: 14px">
        <tr style="color:#000">
            <td style="width: 1in; color: #aaa; text-align: center;direction: rtl">
                <span style="width:100%;">............................</span>
            </td>
            <td style="width: 0.3in; text-align: left; direction: rtl">ސޮއި:</td>
            <td style="width: 1in; color: #aaa; text-align: center;direction: rtl">
                <span style="width:100%;">............................</span>
            </td>
            <td style="width: 1in; text-align: left; direction: rtl">އައިޑީ ނަންބަރ:</td>
            <td style="width: 1.7in; color: #aaa; text-align: center;direction: rtl">
                <span style="width:100%;">..........................................</span>
            </td>
            <td style="width: 0.5in; text-align: left; direction: rtl">ނަން:</td>
            <td style="width: 1.5in; text-align: right; direction: rtl">ފައިސާ ހަވާލުވި ފަރާތް:</td>
        </tr>
    </table>
    

</div>

