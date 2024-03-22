<?php
/* @var $this UmraController */
/* @var $model UmraTrips */
/* @var $form CActiveForm */

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . "/js/jtk-4.2.1.js", CClientScript::POS_HEAD);

Yii::app()->clientScript->scriptMap = [
  'jquery-ui-i18n.min.js' => false,
];
?>
<div class="tab-content">
  <div class="form small">

    <?php
    $form = $this->beginWidget('CActiveForm', [
      'id' => 'umra-form',
      // Please note: When you enable ajax validation, make sure the corresponding
      // controller action is handling ajax validation correctly.
      // There is a call to performAjaxValidation() commented in generated controller code.
      // See class documentation of CActiveForm for details on this.
      'enableAjaxValidation' => false,
    ]);
    ?>
    <div class="panel panel-success">
      <div class="panel-heading">Enter Details</div>
      <div class="panel-body">
        <?php echo $form->errorSummary($model, null, null, ['class' => 'alert alert-danger']); ?>

        <div class="row form-group">
          <?php echo $form->labelEx($model, 'name_english', ['class' =>
            'col-md-1 control-label']); ?>
          <div class="col-md-5">
            <?php echo $form->textField($model, 'name_english', ['size' => 30, 'maxlength' => 255, 'class' => 'form-control']); ?>
          </div>
          <div class="col-md-5">
            <?php echo $form->textField($model, 'name_dhivehi', ['class' => 'thaanaKeyboardInput form-control', 'size' => 30, 'maxlength' => 255]); ?>
          </div>
          <?php echo $form->labelEx($model, 'name_dhivehi', ['class' =>
            'control-label thaanaLabel col-md-1']); ?>
        </div>
      </div>
      <div class="panel-heading">Enter Details</div>
      <div class="panel-body">

        <div class="row form-group">
          <?php echo $form->labelEx($model, 'month', ['class' => 'col-md-1
          control-label']); ?>
          <div class="col-md-2">
            <?php echo $form->dropDownList($model, 'month',
              [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'],
              ['class' => 'form-control']); ?>
          </div>
          <?php echo $form->labelEx($model, 'year', ['class' =>
            'control-label col-md-1']); ?>
          <div class="col-md-2">
            <?php
            $years = range(date('Y'), (((int)date('Y')) + 20));
            echo $form->dropDownList($model, 'year', array_combine($years, $years),
              ['class' => 'form-control']); ?>
          </div>
          <?php echo $form->labelEx($model, 'price', ['class' => 'col-md-1
          control-label']); ?>
          <div class="col-md-2">
            <?php echo $form->textField($model, 'price', ['size' => 7, 'maxlength' => 7, 'class' => 'form-control']); ?>
          </div>
        </div>
      </div>

    </div>
    <div class="panel-footer">
      <div class="form-group row">
        <div class="col-md-offset-1 col-md-2">
          <button
            class="btn btn-primary btn-default btn-sm"><?php echo($model->isNewRecord ? 'Create' : 'Update'); ?></button>
        </div>

      </div>

    </div>

    <?php $this->endWidget(); ?>

  </div><!-- form -->
</div>