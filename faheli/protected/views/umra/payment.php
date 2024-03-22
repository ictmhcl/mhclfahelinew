<?php
/* @var $this UmraController */
/* @var $model UmraPilgrims */
/* @var $transaction UmraTransactions */
?>

<h3><span style="color:green">Umra Trip Payments </span>: <?php echo ucwords
  ($model->person->full_name_english); ?> </h3>
<ul class="nav nav-tabs small">
  <li class="active"><a></a></li>
</ul>

<div class="tab-content">
  <div class="form small">

    <?php
    $form = $this->beginWidget('CActiveForm', [
        'id' => 'umra-payment-form',
        'method' => 'post',
        'enableAjaxValidation' => false,
    ]);
    ?>
    <div class="panel panel-success">
      <div class="panel-body">
        <div class="col-md-3 pull-right">
          <div class="input-group">
            <div class="input-group-addon">
              Total Paid
            </div>
            <span class="form-control balance">
              <?php echo 'MVR ' . number_format($model->account_balance, '2', '.', ',')
              ; ?>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel-success">
      <div class="panel-heading">Pilgrim Details<span
          class="pull-right">Payment Details</span></div>
      <div class="panel-body">
        <div class="col-md-7">
          <div style="margin-bottom:10px">
            <?php $this->renderPartial('_pilgrimSummaryViewForPayment',
              ['model' => $model]); ?>
          </div>
        </div>
        <div class="col-md-4 col-md-offset-1" style="margin-bottom:5px">
          <?php echo $form->errorSummary($transaction, null, null, [
            'style' => '', 'class' => 'alert alert-danger'
          ]); ?>
          <div class="form-group row">
            <?php
            echo CHtml::label('Payment mode', '', [
                'class' => 'control-label col-md-6', 'style' => 'margin-top:5px']);
            ?>
            <div class="col-md-6">
              <?php
              echo $form->dropDownList($transaction, 'transaction_medium_id',
                CHtml::listData(ZTransactionMediums::model()->findAll('id < 5'), 'id', 'name_english'), [
                  'class' => 'form-control'
              ]);
              ?>
            </div>
          </div>
          <div class="form-group row">
            <?php echo $form->label($transaction, 'amount', [
              'class' => 'control-label col-md-6',
              'style' => 'margin-top:5px'
            ]); ?>
            <div class="col-md-6">
              <?php
              echo $form->textField($transaction, 'amount', [
                  'class' => 'form-control'
              ]);
              ?>
            </div>
          </div>
          <div class="form-group row">
            <?php echo CHtml::label('Payment Received', 'feeCollected', [
              'class' => 'control-label col-md-6', 'style' => 'margin-top:5px'
            ]); ?>
            <div class="col-md-6">
              <?php
              echo CHtml::checkBox('feeCollected', false, [
              ]);
              ?>
            </div>
          </div>
          <div class="pull-right">
            <button type="submit" class="btn btn-sm btn-primary btn-default"
              onClick='js:
                if ($("#feeCollected").is(":checked") == false) {
                  alert("You must confirm that you have collected the payment by marking the check box labelled \"Payment Received\"");
                  turnBlockOn = false;
                  return false;
                } else {
                  if (confirm("Are you sure that the values you are submitting are CORRECT?") == false) {
                    turnBlockOn = false;
                    return false;
                  } else {
                    $(this).attr("disabled","disabled");
                    submit();
                  }
                }'>
              <icon class="glyphicon glyphicon-circle-arrow-down"></icon> Collect Payment</button>

          </div>
        </div>
      </div>
    </div>
    <?php $this->endWidget(); ?>

  </div><!-- form -->
</div>



