<?php
/* @var $this UmraController */
/* @var $trip UmraTrips */
/* @var $discountsDp CActiveDataProvider */

$processPayment = $this->processPayments;

$pilgrim = $trip->currentPilgrim;
$paymentOptions = [];
if ($pilgrim) {
  $toFullPayment =
    $trip->price - $pilgrim->discountAmount - $pilgrim->account_balance;

  foreach ([500, 1000, 1500, 2000, 3000, 5000] as $amount) {
    if ($amount < $toFullPayment)
      $paymentOptions[$amount] = Helpers::currency($amount)
        . H::t('hajj', 'confirmSlot');
  }
  if ($toFullPayment > 0) {
    $paymentOptions += ['full' => Helpers::currency($toFullPayment)];
  }
  $paymentOptions += ['0' => H::t('hajj', 'anotherAmount')];
}
?>
<style>
  #statement-grid {
    padding-bottom: 0;
  }
</style>
<h3>
    <span style="color:green">
    <?=$this->person->{H::tf('full_name_dhivehi')} . ' (' . $this->person->id_no
    . ')'?></span><br/><br/><span
    class="small"><?=$trip->{H::tf("name_dhivehi")}?></span>
</h3>

<ul class="nav nav-tabs small">
</ul>

<div class="tab-content">
  <div class="form">

    <?php
    ?>
    <div class="panel panel-success">
      <div class="panel-heading"><?=$trip->{H::tf('name_dhivehi')}?>
        <?php if (!empty($pilgrim)) {?>
        <span
          class="badge badge-default" style="height:22px">
          <span style="font-size: 14px; font-weight: 100">
            <?=H::t('act','registered')?>
          </span>
        </span>
        <?php } ?>
        <span style="background-color: #0066cc; height:22px"
              class="badge badge-default" >
          <span style="font-size: 14px; font-weight: 100">
            <?=Helpers::currency($trip->price)?>
          </span>
        </span>
      </div>
      <?php if (!empty($pilgrim)) { ?>
        <div class="panel-body">
          <div class="form-group">
            <div class="col-md-12" style="margin-bottom: 5px"><?=H::t('umra',
                'registeredInfo {fullName} {id_no} {tripName}', [
                  'fullName' => $this->person->{H::tf('full_name_dhivehi')},
                  'id_no' => $this->person->id_no,
                  'tripName' => $trip->{H::tf('name_dhivehi')}
                ])?>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-5">
              <div class="input-group" style="width: 100%; margin-bottom: 5px">
                <div class="input-group-addon" style="width: 40%">
                  <?=H::t('hajj','balance')?>
                </div>
              <span class="form-control">
                <?=Helpers::currency($pilgrim->account_balance)?>
                <span class="btn btn-xs
                  btn-primary hidden-xs" onclick="javascript:$
                  ('.umraStatement').slideToggle()"><?=H::t('act',
                    'accountDetails')?></span>

              </span>
              </div>

            </div>
            <div class="col-md-2 visible-xs" style="margin-bottom: 5px">
              <span class="btn btn-sm btn-primary" onclick="javascript:$
                  ('.umraStatement').slideToggle()"><?=H::t('act', 'accountDetails')?></span>
            </div>
            <div class="col-md-5">
              <div class="input-group" style="width: 100%; margin-bottom: 5px">
                <div class="input-group-addon" style="width: 40%">
                  <?=H::t('act','pendingAmount')?>
                </div>
              <span class="form-control">
                <?php
                if (!empty($pilgrim->due))
                  echo Helpers::currency($pilgrim->due);
                else
                  echo "<strong style='color: green;' style='hidden-xs'>"
                    .H::t('act','fullyPaid')."</strong>"
                ?>
              </span>
              </div>

            </div>
          </div>
        </div>
         <div class="panel-body" style="padding: 0 10px 10px;">
          <?php if (!empty($pilgrim->umraTripDiscount)) { ?>
            <div class="form-group">
              <div class="col-md-4 ">
                <div class="input-group"
                     style="margin-bottom: 5px; width: 100%">
                  <div class="input-group-addon" style="width: 40%">
                    <?=H::t('act','discountReceived')?>
                  </div>
              <span class="form-control">
                <?=Helpers::currency($pilgrim->umraTripDiscount->discount_amount)?>

              </span>
                </div>


              </div>
              <div class="col-md-4 ">
                <div class="input-group"
                     style="margin-bottom: 5px; width: 100%">
                  <div class="input-group-addon" style="width: 40%">
                    <?=H::t('act', 'discountType')?>
                  </div>
              <span class="form-control">
                <?=$pilgrim->umraTripDiscount->{H::tf('name_dhivehi')}?>

              </span>
                </div>


              </div>
            </div>
          <?php } ?>
        </div>      

		<div class="panel-body" style="padding-top: 0">
          <div class="form-group">
            <div class="col-md-4">
            <span class="btn btn-success btn-sm onlinPayBtn"
              <?=$this->processPayments
                ? 'onclick="javascript:$(\'.onlinePay\').slideToggle()"' : ''?>>
              <?=H::t('hajj', 'payOnline')?> <?=!$this->processPayments
                ? H::t('hajj', 'temporarilyDown') : ''?>
            </span>
            </div>

          </div>
        </div>
        <?php if ($this->processPayments) {


          $maximumAmount = $pilgrim->due;

          if ($pilgrim->due > 0) {
            $additionalPaymentOptions['due'] =
              Helpers::currency($pilgrim->due) .
              H::t('hajj','fullPayment');
            $amountValues['due'] = $pilgrim->due;
            $defaultAmount = 'due';
          }

          $payload = CJSON::encode([
            'umra_pilgrim_id' => $pilgrim->id
          ]);

          $paymentTypeId = Constants::ONLINE_PAYMENT_UMRA;

          include_once(Yii::getPathOfAlias('webroot')
            . "/js/process-payments.php");

          ?>


        <?php } ?>


        <div class="panel-heading umraStatement" style="display: none;">
          <?=H::t('act','accountDetails')?>
        </div>
        <div class="panel-body umraStatement"
             style="display: none; padding-bottom: 0">
          <div class="col-md-12">
            <?php
            $this->widget('zii.widgets.grid.CGridView', [
              'id' => 'statement-grid',
              'rowCssClassExpression' => '($data->is_cancelled==1)?"cancelled-transaction":""',
              'template' => '{items}',
              'dataProvider' => new CActiveDataProvider('UmraTransactions', [
                'criteria' => [
                  'condition' => 'is_cancelled = 0 and umra_pilgrim_id = '
                    . (int)$pilgrim->id, 'order' => 'transaction_time asc',
                ], 'pagination' => false,
              ]), 'columns' => [
                [
                  'header' => H::t('act', 'date'), 'type' => 'raw', 'value' => 'Yii::app()->language=="dv"?Helpers::mvDate((new DateTime
                ($data->transaction_time))
                  ->format("d M Y H:i:s"),true):(new DateTime
                ($data->transaction_time))
                  ->format("d F Y H:i:s")',
                  'htmlOptions' => ['style' => H::t('act', 'txtAlign')],
                ], [
                  'header' => H::t('act', 'details'),
                  'cssClassExpression' => '"hidden-xs"',
                  'value' => '$data->{H::tf("description_dhivehi")}',
                  'htmlOptions' => ['style' => H::t('act', 'txtAlign')],
                  'headerHtmlOptions' => ['class' => 'hidden-xs'],
                ], [
                  'header' => H::t('act', 'medium'),
                  'value' => '$data->transactionMedium->{H::tf("name_dhivehi")}',
                  'htmlOptions' => ['style' => H::t('act', 'txtAlign')],
                ], [
                  'header' => H::t('act', 'debit'), 'value' => '(
                  ($data->amount < 0)?
                  Helpers::currency(-$data->amount) : "")',
                  'htmlOptions' => ['style' => 'width:40px;text-align:right'],
                  'headerHtmlOptions' => ['style' => 'width:80px;text-align:right'],
                ], [
                  'header' => H::t('act', 'credit'), 'value' => '(
                  ($data->amount > 0)?
                  Helpers::currency($data->amount) : "")',
                  'htmlOptions' => ['style' => 'text-align:right'],
                  'headerHtmlOptions' => ['style' => 'width:80px;text-align:right'],
                ], [
                  'header' => H::t('act', 'balance'),
                  'value' => 'Helpers::currency($data->balance)',
                  'htmlOptions' => ['style' => 'text-align:right'],
                  'headerHtmlOptions' => ['style' => 'width:80px;text-align:right'],
                ],
              ],
            ]);
            ?>
          </div>

        </div>


      <?php }
      else { ?>
        <div class="panel-body">
          <div class="form-group">
            <div class="col-md-5">
              <?=$trip->closed
                ? "<span style='font-weight: 800;font-size: 18px;color:red'>"
                  .H::t('umra','pastDeadline')."</span>"
                : CHtml::link(H::t('act','registerHere'), Yii::app()
                  ->createUrl('umra/createUmraPilgrim', ['umraTripId' => $trip->id]), ['class' => 'btn btn-primary'])?>
            </div>

          </div>
        </div>
      <?php } ?>

    </div>
    <div class="panel panel-success">
      <div class="panel-heading"><?=$trip->{H::tf('name_dhivehi')}?> <?=H::t('site','info')?></div>
      <div class="panel-body">
        <div class="col-md-12" style="margin-bottom: 15px">
          <?=$trip->{H::tf('description_dhivehi')}?>
        </div>
        <div class="col-md-4">
          <div class="input-group" style="margin-bottom: 5px">
            <div class="input-group-addon">
              <?=H::t('umra','umraPrice')?>
            </div>
            <span class="form-control balance">
              <?=Helpers::currency($trip->price)?>
            </span>
          </div>
        </div>

      </div>
    </div>
    <div class="panel panel-success">
      <div class="panel-heading"><?=$trip->{H::tf('name_dhivehi')}?>
        <?=H::t('umra','dates')?></div>
      <div class="panel-body">
        <div class="col-md-4">
          <div class="input-group" style="margin-bottom: 5px; width: 100%">
            <div class="input-group-addon" style="width: 40%">
              <?=H::t('umra', 'departureDate')?>
            </div>
              <span class="form-control balance">
                <?=$trip->tripDate?>
              </span>
          </div>
        </div>
        <?php if (!empty($trip->return_date)) { ?>
          <div class="col-md-4">
            <div class="input-group" style="margin-bottom: 5px; width: 100%">
              <div class="input-group-addon" style="width: 40%">
                <?=H::t('umra','arrivalDate')?>
              </div>
              <span class="form-control balance">
                <?=Yii::app()->language == 'dv'
                  ? Helpers::mvDate($trip->return_date)
                  : (new DateTime($trip->return_date))->format('d F Y')?>
              </span>
            </div>
          </div>
        <?php } ?>
      </div>
      <div class="panel-body">
        <div class="col-md-4">
          <div class="input-group" style="margin-bottom: 5px; width: 100%">
            <div class="input-group-addon" style="width: 40%">
              <?=H::t('umra', 'deadlineDate')?>
            </div>
              <span class="form-control balance">
                <?=empty($trip->deadline_date) ? "-"
                  : (Yii::app()->language == 'dv'
                    ? Helpers::mvDate($trip->deadline_date)
                    : (new DateTime($trip->deadline_date))->format('d F Y'))?>
              </span>
          </div>
        </div>

      </div>
    </div>
   


  </div><!-- form -->
</div>
