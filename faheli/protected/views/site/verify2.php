<?php
/* @var $this SiteController */
/* @var $model PhoneVerifyForm */
/* @var $form CActiveForm */

$this->pageTitle = Yii::app()->name . ' - Verify';
?>
<div class="form">
  <?php
  $form = $this->beginWidget('CActiveForm', [
    'id' => 'verify-form', 'enableClientValidation' => true,
    'clientOptions' => [
      'validateOnSubmit' => true,
    ],
  ]);
  ?>
  <div class="panel panel-success"
       style="max-width: 400px;margin-left:auto;margin-right:auto;">
    <div class="panel-heading">ކޮންބޭފުޅެއްކަން ޔަގީން ކޮށްދެއްވާ</div>
    <div class="panel-body">
      <div class="form-group row">
        <?=$form->errorSummary($model,'ހުށަހެޅުއްވި މަޢުލޫމާތު ރަނގަޅެއްނޫން',
          null,['class' => 'alert alert-danger', 'style' => 'margin: 5px;']);?>
        <?php echo $form->labelEx($model, 'idCard', [
          'class' => 'control-label col-md-4',
        ]); ?>
        <div class="col-md-6">
          <?php echo $form->textField($model, 'idCard', [
            'class' => 'form-control', 'autofocus' => 'autofocus',
            'style' => 'direction: ltr; text-align: right', 'autocomplete' =>
              'off'
          ]); ?>
        </div>
      </div>

      <div class="form-group row">
        <?php echo $form->labelEx($model, 'mobile', [
          'class' => 'control-label
				col-md-4'
        ]); ?>
        <div class="col-md-6">
          <?php echo $form->textField($model, 'mobile',
            ['class' => 'form-control', 'style' => 'direction: ltr;
            text-align: right', 'autocomplete' => 'off']); ?>
        </div>
      </div>
      <div class="form-group row">

        <div class="col-md-6 col-md-offset-4">
          <button type="submit" class="btn btn-sm btn-primary"
          style="margin-bottom: 5px">
            <icon
              class="glyphicon glyphicon-user" style="color:orange"></icon>
            މޯބައިލަށް ވެރިފިކޭޝަން ކޯޑު ފޮނުވާ
          </button>
          <a class="btn btn-sm btn-default" style="margin-bottom: 5px"
             href="<?=Yii::app()->createUrl
          ('site/register')?>">
            <icon
              class="glyphicon glyphicon-pencil" style="color:darkslategray"></icon>
            ޕޯޓަލްގައި ރަޖިސްޓަރ ކުރުން
          </a>
          <?php //echo CHtml::submitButton('Login'); ?>
        </div>
      </div>
    </div>
  </div>


  <?php $this->endWidget(); ?>
</div><!-- form -->
