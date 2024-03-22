<?php
/* @var $this MembersController */
/* @var $model Members */
$model = $memberTransaction->member;
?>

<h3><span style="color:green">First Payment </span>: <?php echo ucwords($model->person->full_name_english); ?> <small><strong><?php echo $model->MHC_ID ?></strong></small></h3>
<ul class="nav nav-tabs small">
  <li class="active"><a></a></li>
</ul>

<div class="tab-content">
  <div class="form small">

    <?php
    $form = $this->beginWidget('CActiveForm', [
        'id' => 'first-payment-form',
        'method' => 'post',
//        'action' => '',
        // Please note: When you enable ajax validation, make sure the corresponding
        // controller action is handling ajax validation correctly.
        // There is a call to performAjaxValidation() commented in generated controller code.
        // See class documentation of CActiveForm for details on this.
        'enableAjaxValidation' => false,
    ]);
    ?>
    <div class="panel panel-success">
      <div class="panel-body">
        <div class="col-md-3 pull-right">
          <div class="input-group">
            <div class="input-group-addon">
              Balance
            </div>
            <span class="form-control balance">
              <?=Helpers::currency($model->accountBalance,'MVR')?>
            </span>
          </div>
        </div>
      </div>
    </div>

    <div class="panel panel-success" style="margin-bottom:10px">
      <div class="panel-heading">Member Details<span class="pull-right">Deposit Details</span></div>
      <div class="panel-body">
        <div class="col-md-8">
          <div style="margin-bottom:10px">
            <?php $this->renderPartial('//members/_memberInfo', ['model' =>
              $model]); ?>
          </div>
        </div>

        <div class="col-md-4">
          <?php echo $form->errorSummary($memberTransaction, null, null, ['style' => '', 'class' => 'alert alert-danger']); ?>
          <div class="form-group row">
            <?php
            echo $form->label($memberTransaction, 'transaction_medium_id', [
                'class' => 'control-label col-md-6', 'style' => 'margin-top:5px']);
            ?>
            <div class="col-md-6">
              <?php
              echo $form->dropDownList($memberTransaction, 'transaction_medium_id', CHtml::listData(ZTransactionMediums::model()->findAll('id < 5'), 'id', 'name_english'), [
                  'class' => 'form-control'
              ]);
              ?>
            </div>
          </div>
          <div class="form-group row">
            <?php echo $form->label($memberTransaction, 'amount', ['class' => 'control-label col-md-6', 'style' => 'margin-top:5px']); ?>
            <div class="col-md-6">
              <?php
              echo $form->textField($memberTransaction, 'amount', [
                  'class' => 'form-control'
              ]);
              ?>
            </div>
          </div>
          <div class="form-group row">
            <?php echo CHtml::label('Payment Received', 'feeCollected', ['class' => 'control-label col-md-6', 'style' => 'margin-top:5px']); ?>
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
              <icon class="glyphicon glyphicon-circle-arrow-down"></icon> Collect First Payment</button>

          </div>
        </div>
      </div>
    </div>
    <?php $this->endWidget(); ?>

  </div><!-- form -->
</div>



