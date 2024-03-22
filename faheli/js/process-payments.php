<?php
$clientScriptUrl = Yii::app()->request->baseUrl . "/js/decimalRound.js";
$clientScript = Yii::app()->clientScript;
$clientScript->registerScriptFile($clientScriptUrl, CClientScript::POS_HEAD);

if (empty($fixedAmount)) {
  $paymentOptions = [];
  foreach ([500, 1000, 1500, 2000, 3000, 5000] as $amount) {
    if (empty($maximumAmount) || $amount <= $maximumAmount)
      $paymentOptions[$amount] = Helpers::currency($amount);
  }
  if (!empty($additionalPaymentOptions))
    $paymentOptions += $additionalPaymentOptions;
  $paymentOptions += ['0' => H::t('hajj','anotherAmount')];

  $amountValues = !empty($amountValues)?$amountValues:[];



  $defaultAmount = !empty($defaultAmount)? $defaultAmount:500;
} else
  $defaultAmount = $fixedAmount;

$paymentTypeID = empty($paymentTypeID)?Constants::ONLINE_PAYMENT_HAJJ:$paymentTypeID;
?>
<div class="panel-heading onlinePay" style="display: none;">
  <?=H::t('hajj','onlinePayment')?>
</div>
<div class="panel-body onlinePay" style="display: none;">
  <div class="form-group">
    <?=CHtml::label(H::t('hajj','depositAmount'), 'amount', [
      'class' => 'control-label
          col-md-2'
    ]);?>
    <div class="col-md-4" style="margin-bottom: 5px">
      <?=empty($fixedAmount)?CHtml::dropDownList('amount', $defaultAmount,
        $paymentOptions, [
        'class' => 'form-control', 'onchange' => '
                javascript:
                  if ($(this).val()==0) {
                    $(".otherAmount").slideDown();
                  } else {
                    $(".otherAmount").slideUp();
                  }
                  updateCharges();
              '
      ]):
        CHtml::textField('amount', Helpers::currency($fixedAmount), ['class' => 'form-control', 'disabled' => 'disabled']);
      ?></div>
    <?php if (empty($fixedAmount)) {?>
    <?=CHtml::label(H::t('hajj','enterAnotherAmount'), 'other_amount', [
      'class' => 'control-label
          col-md-2 otherAmount', 'style' => 'display:none'
    ]);?>
    <div class="col-md-2">
      <input name="other_amount" value="500" class="form-control
            otherAmount" style="display: none; direction: ltr" type="number"
             id="other_amount" onchange="updateCharges()">
      <?=CHtml::hiddenField('bank_charge', (round($defaultAmount, 0) / 100)
        . 'ރ', ['disabled' => 'disabled', 'class' => 'form-control'])?>
      <?=CHtml::hiddenField('card_amount', 0 . 'ރ', [
        'disabled' => 'disabled', 'class' => 'form-control ',
        'style' => 'font-weight: 800'
      ])?>
    </div>
    <?php } ?>
  </div>
</div>
<div class="panel-body onlinePay" style="display: none;">
  <div class="col-md-4">
          <span class="btn btn-primary btn-sm payNowBtn"
                data-toggle="modal" data-target="#confirm-payment"
                onclick="return false;">
            <?=H::t('hajj','payAboveAmount')?>
          </span>
  </div>

</div>
<div class="panel-body onlinePay" style="display: none;">
 
  <div class="col-md-12">
    <?php echo CHtml::image(Helpers::sysUrl(Constants::IMAGES) . 'payment.png'); ?>
	
  </div>
  <div class="col-md-12" style="margin-top: 5px">
    <div class="well"><?=H::t('hajj','paymentNote')?></div>
  </div>
</div>
<div class="modal fade" id="confirm-payment" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h3>
          <?=H::t('hajj','forwardedToBankModal')?>
        </h3></div>
      <div class="modal-body">
        <?=H::t('hajj','forwardedToBankInfo')?>
        <form method="post" action="<?=Yii::app()->createUrl('pay/payNow')?>"
              id="submitPayment">
          <input type="hidden" name="payment_type_id"
                 value="<?=$paymentTypeId?>">
          <input type="hidden" name="payload" value='<?=$payload?:''?>'>
          <input type="hidden" name="creditAmount" id="creditAmount">
          <input type="hidden" name="paymentAmount" id="paymentAmount">
          <input type="hidden" name="bankAmount" id="bankAmount">
          <input type="submit" style="visibility:hidden;"/>
        </form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">
          <?=H::t('hajj', 'cancelBtn')?>
        </button>
        <a class="btn btn-primary btn-ok"><?=H::t('hajj','bmlGateway')?></a>
      </div>
    </div>
  </div>
</div>

<script>
  function numberWithCommas(x) {
    x = x.toString();
    var pattern = /(-?\d+)(\d{3})/;
    while (pattern.test(x))
      x = x.replace(pattern, "$1,$2");
    return x.indexOf('.') !== -1 ? x : x + '.00';
  }
  var amount = 0;
  var bankCharge = 0;
  var fullAmount = 0;
  <?php if (empty($fixedAmount)) {?>
  amountValues = <?=CJSON::encode($amountValues)?>;
  updateCharges = function () {
    amount = $('#amount').val();
    if (isNaN(amount))
      amount = amountValues[amount]
    else if (amount == '0')
      amount = $('#other_amount').val() || 0;
    bankCharge = Math.round10(((100 * amount / 98.5) + 0.01) - amount, -2);
    $('#bank_charge').val(numberWithCommas(bankCharge) + 'ރ');
    fullAmount = Math.round10(amount * 1 + bankCharge * 1, -2);
    $('#card_amount').val(numberWithCommas(fullAmount) + 'ރ')
  }

  $(document).ready(function () {
    updateCharges();
  });

  <?php } else { ?>
  amount = <?=$fixedAmount?>;
  bankCharge = Math.round10(((100 * amount / 98.5) + 0.01) - amount, -2);
  $('#bank_charge').val(numberWithCommas(bankCharge) + 'ރ');
  fullAmount = Math.round10(amount * 1 + bankCharge * 1, -2);
  $('#card_amount').val(numberWithCommas(fullAmount) + 'ރ')
  <?php } ?>

  $('#confirm-payment').on('show.bs.modal', function (e) {

    if (amount == 0) {
      return false;
    }
    $(this).find('#paymentAmount').val(fullAmount);
    $(this).find('#creditAmount').val(amount);
    $(this).find('#bankAmount').val(bankCharge);

    $(this).find('.btn-ok').on('click', function () {
      $('#submitPayment').submit()
    });
  });
</script>