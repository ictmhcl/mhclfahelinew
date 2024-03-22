<?php
/* @var $this MembersController */
/* @var $member Members */
/* @var $dataProvider CActiveDataProvider */
$member = $this->person->member;


?>

  <h3>
    <span style="color:green">
    <?=$this->person->{H::tf('full_name_dhivehi')} . ' (' . $this->person->id_no
    . ') - ' . H::t('hajj', 'hajjAccountDetails')?></span><br/><br/>
    <small><strong><?=$member->MHC_ID?></strong></small>
  </h3>
  <ul class="nav nav-tabs small">

  </ul>

  <div class="tab-content">
    <div class="form">

      <div class="panel panel-success">
        <div class="panel-body">
          <div class="col-md-4">
            <div class="input-group" style="margin-bottom: 5px">
              <div class="input-group-addon"><?=H::t('hajj','balance')?></div>
            <span class="form-control balance">
              <?=Helpers::currency($member->accountBalance)?>
            </span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="input-group" style="margin-bottom: 5px">
              <div class="input-group-addon">
                <?=H::t('hajj','hajjPrice')?>
              </div>
            <span class="form-control balance">
              <?=Helpers::currency(Constants::FULL_AMOUNT)?>
            </span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="input-group" style="margin-bottom: 5px">
              <div class="input-group-addon">
                <?=H::t('hajj','position')?>
              </div>
            <span class="form-control balance <?=!empty($member->hajjPlacement)?'':'alert-danger'?>">
              <?=(!empty($member->hajjPlacement)
                ? H::t('hajj', 'position {position} {year}', $member->hajjPlacement)
                : H::t('hajj', 'placementInfo {amt}',
                  ['amt' => Helpers::currency(Constants::MATURITY_VALUE)]))?>
            </span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="col-md-4"><?php $this->processPayments; ?>
            <span class="btn btn-success btn-sm onlinPayBtn"
              <?=$this->processPayments?
                'onclick="javascript:$(\'.onlinePay\').slideToggle()"' : ''?>>
              <?=H::t('hajj','payOnline')?> <?=!$this->processPayments ?
                H::t('hajj','temporarilyDown') : ''?>
            </span>
            </div>
          </div>
        </div>

        <?php if ($this->processPayments) {

          $toFullPayment = Constants::FULL_AMOUNT - $member->accountBalance;
          $toMature = Constants::MATURITY_VALUE - $member->accountBalance;

          if ($toMature > 0) {
            $additionalPaymentOptions['mature'] =
              Helpers::currency($toMature) . H::t('hajj', 'confirmSlot');
            $amountValues['mature'] = $toMature;
          }
          if ($toFullPayment > 0) {
            $additionalPaymentOptions['full'] =
              Helpers::currency($toFullPayment)
              . H::t('hajj','fullPayment');
            $amountValues['full'] = $toFullPayment;
          }

          $defaultAmount =
            ($toMature > 0) ? 'mature' : ($toFullPayment > 0 ? 'full'
              : 500);
          $paymentTypeId = Constants::ONLINE_PAYMENT_HAJJ;
          $payload = null; $paymentType = Constants::ONLINE_PAYMENT_HAJJ;

          include_once(Yii::getPathOfAlias('webroot')
                  . "/js/process-payments.php");
        } ?>
        <div class="panel-heading"><?=H::t('act', 'accountDetails')?></div>
        <div class="panel-body">
          <div class="col-md-12" style="margin-bottom:10px">
            <?php
            $this->widget('zii.widgets.grid.CGridView', [
              'id' => 'members-grid',
              'rowCssClassExpression' => '($data->is_cancelled==1)?"cancelled-transaction":""',
              'template' => '{items}', 'dataProvider' => $dataProvider,
              'columns' => [
                [
                  'header' => H::t('act','date'), 'type' => 'raw', 'value' =>
                  'Yii::app()->language=="dv"?Helpers::mvDate((new DateTime
                ($data->transaction_time))
                  ->format("d M Y H:i:s"),true):(new DateTime
                ($data->transaction_time))
                  ->format("d F Y H:i:s")',
                  'htmlOptions' => ['style' => H::t('act','txtAlign')],
                ], [
                  'header' => H::t('act', 'details'), 'cssClassExpression' =>
                    '"hidden-xs"',
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
      </div>

    </div><!-- form -->
  </div>
