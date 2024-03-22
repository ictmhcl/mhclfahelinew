<?php
/* @var $this AgeegaController */
/* @var $ageegaRate AgeegaRates */
/* @var $form CActiveForm */ ?>

<h3><?= $ageegaRate->isNewRecord ? 'Create' : 'Update' ?> Ageega Rates
</h3>

<ul class="nav nav-tabs small">
  <li><a><?= $ageegaRate->isNewRecord ? 'New' : 'Update' ?> Ageega Rate</a></li>
</ul>

<div class="tab-content">

  <div class="form small">

    <?php
    $form = $this->beginWidget('CActiveForm', [
      'id' => 'ageega-rate-form',
      'enableAjaxValidation' => false,
    ]);
    ?>

    <div class="panel panel-success">
      <div class="panel-heading">Details</div>
      <div class="panel-body">
        <?php
        echo $form->errorSummary(array($ageegaRate), null, null,
          ['class' => 'alert alert-danger']); ?>
        <div class="form-group row">
          <?php echo $form->labelEx($ageegaRate, 'gender_id',
            ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-3">
            <?php echo $form->dropDownList($ageegaRate, 'gender_id',
              CHtml::listData(ZGender::model()->findAll(), 'gender_id', 'name_english'),
              ['class' => 'form-control']); ?>
          </div>
        </div>
        <div class="form-group row">
          <?php echo $form->labelEx($ageegaRate, 'from_date',
            ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-3">
            <?php
            $form->widget('zii.widgets.jui.CJuiDatePicker', [
              'model' => $ageegaRate,
              'attribute' => 'from_date',
              'options' => [
                'changeMonth' => true,
                'changeYear' => true,
                'showAnim' => 'slide',
                'minDate' => '0',
                'dateFormat' => Constants::DATE_DISPLAY_FORMAT,
              ],
              'htmlOptions' => [
//                      'readonly' => 'readonly',
                'class' => 'form-control',
                'placeholder' => 'E.g. "17 Feb 1954"'
              ],
            ]);
            ?>
          </div>
        </div>
        <div class="form-group row">
          <?php echo $form->labelEx($ageegaRate, 'till_date',
            ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-3">
            <?php
            $form->widget('zii.widgets.jui.CJuiDatePicker', [
              'model' => $ageegaRate,
              'attribute' => 'till_date',
              'options' => [
                'changeMonth' => true,
                'changeYear' => true,
                'showAnim' => 'slide',
                'minDate' => '0',
                'dateFormat' => Constants::DATE_DISPLAY_FORMAT,
              ],
              'htmlOptions' => [
//                      'readonly' => 'readonly',
                'class' => 'form-control',
                'placeholder' => 'E.g. "17 Feb 1954"'
              ],
            ]);
            ?>
          </div>
        </div>
        <div class="form-group row">
          <?php echo $form->labelEx($ageegaRate, 'rate',
            ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-3">
            <?php echo $form->textField($ageegaRate, 'rate',
              ['class' => 'form-control', 'type'=>'numeric', 'style' =>
                'text-align:right'])
              ; ?>
          </div>
        </div>
      </div>


      <div class="panel-footer">
        <div class="form-group row">
          <div class="col-md-offset-2 col-md-2">
            <button class="btn btn-primary btn-default btn-sm">
              <icon
                class="glyphicon glyphicon-save"></icon> <?= $ageegaRate->isNewRecord ? 'Save' : 'Update' ?>
            </button>
          </div>
        </div>
      </div>
    </div>


    <?php $this->endWidget(); ?>

  </div>
</div>