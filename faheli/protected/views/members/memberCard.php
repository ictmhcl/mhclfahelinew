<?php
/* @var $this MembersController */
/* @var $model Members */
/* @var $form CActiveForm */
?>
<?php if (0) { ?>
  <script type="text/javascript">
    window.onload = function() {
      window.print();
      window.location.href = "<?php echo $this->createUrl('members/view', ['id' => $member->id]) ?>";
    };
  </script>
<?php } ?>
<div style="-webkit-print-color-adjust:exact;position:absolute;">
  <img src="<?php echo Helpers::sysUrl(Constants::IMAGES) . 'cardFinal.png' ?>" style="position:absolute;top:0;left:0;width:8.5cm;z-index:1" />
  <img src="<?php echo Helpers::sysUrl(Constants::UPLOADS) . $member->applicationForm->passport_photo; ?>" style="position:absolute;top:0.15in;left:0.2in;width:1in;z-index:0" />
  <div style="font-family:OCRA;color:white;font-weight: normal;z-index:2;position:absolute;top:1.18in;left:0.2in;width:2.945in;border:0px solid black;text-align: right">
    <p style="margin:0;padding:0"><span style="font-size:0.125in;"><?= $member->person->id_no ?></span></p>
    <p style="margin-top:-0.055in;margin-right:-0.01in;margin-bottom:0;padding:0"><span style="font-size:0.175in;"><?= $member->MHC_ID ?></span><br></p>
    <p style="margin-top:-0.005in;margin-right:-0.01in;margin-bottom:0;padding:0"><span style="font-size:0.125in;"><?= strtoupper($member->person->full_name_english) ?></span><br></p>
    <p style="margin-top:-0.025in;margin-right:-0.01in;padding:0"><span style="font-family:FarumaWeb,Faruma;font-size:0.145in;font-weight:normal"><?= $member->person->full_name_dhivehi ?></span><br></p>
  </div>
</div>
