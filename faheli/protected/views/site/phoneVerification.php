<?php
/* @var $this SiteController */
/* @var $model CodeVerificationForm */
/* @var $form CActiveForm */

$this->pageTitle = Yii::app()->name . ' - Phone Verification';
?>
<div class="form">
  <?php
  $form = $this->beginWidget('CActiveForm', [
    'id' => 'phone-verification-form', 'enableClientValidation' => true,
    'clientOptions' => [
      'validateOnSubmit' => true,
    ],
  ]);
  ?>
  <div class="panel panel-success"
       style="max-width: 400px;margin-left:auto;margin-right:auto;">
    <div class="panel-heading"><?=H::t('site', 'codeVerification')?></div>
    <div class="panel-body">
      <div class="form-group row">
        <?=$form->errorSummary($model, '', null, [
          'class' => 'alert alert-danger', 'style' => 'margin: 5px;'
        ]);?>
        <?php echo $form->hiddenField($model, 'id'); ?>
        <?php echo $form->labelEx($model, 'code', [
          'class' => 'control-label
				col-md-4'
        ]); ?>
        <div class="col-md-6">
          <?php echo $form->textField($model, 'code',
            ['class' => 'form-control', 'style' => 'direction:ltr;
            text-align: right', 'autocomplete' => 'off']); ?>
        </div>
      </div>
      <div class="form-group row">

        <div class="col-md-6 col-md-offset-4">
          <button type="submit" class="btn btn-sm btn-primary btn-default"
                  style="margin-bottom: 5px">
            <icon
              class="glyphicon glyphicon-user" style="color:orange"></icon>
            <?=H::t('site', 'submit')?>
          </button>
          <button type="button"
                  style="margin-bottom: 5px" class="btn btn-sm btn-warning btn-default"
                  onclick="window.location.href='<?=Yii::app()
                    ->createUrl('site/sendCodeAgain', ['id' => $model->id])?>'">
            <icon
              class="glyphicon glyphicon-check" style="color:white"></icon>
            <?=H::t('site', 'sendCodeAgain')?>
          </button>
        </div>
      </div>
    </div>
  </div>


  <?php $this->endWidget(); ?>
</div><!-- form -->
