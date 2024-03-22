<?php
/* @var $this RegistrationController */
/* @var $appForm ApplicationForms */
/* @var $form CActiveForm */

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl .
  "/js/jtk-4.2.1.js", CClientScript::POS_HEAD);
?>

<div class="form small">

  <?php
  $form = $this->beginWidget('CActiveForm', [
    'id' => 'application-forms-form',
    'enableAjaxValidation' => false,
    'htmlOptions' =>
      ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']
  ]);
  ?>

  <?php
  echo $form->errorSummary($appForm, H::t('site', 'inputErrors'), null,
    ['style'=> 'margin: 5px;',
    'class' => 'alert alert-danger']);

  $docsArray = [
    'application_form',
    'mahram_document',
  ];

  ?>
  <div class="panel panel-success">
    <div class="panel-heading"><?=H::t('hajj', 'contactInfo')?></div>
    <div class="panel-body">
      <div class="form-group">
        <?php echo $form->labelEx($appForm, 'phone_number_1', ['class' => 'col-md-2 control-label']); ?>
        <div class="col-md-3">
          <?php echo $form->textField($appForm, 'phone_number_1', ['size' => 30, 'maxlength' => 255, 'class' => 'form-control']); ?>
        </div>
        <?php echo $form->labelEx($appForm, 'email_address', ['class' => 'control-label col-md-2']); ?>
        <div class="col-md-4">
          <?php echo $form->textField($appForm, 'email_address', ['class' =>
                                                                    'form-control', 'style'=> 'direction: ltr' , 'size' => 30, 'maxlength' => 255]); ?>
        </div>
      </div>

    </div>
  </div>
  <div class="panel panel-success">
    <div class="panel-heading" style="padding: 3px 0">
      <div class="row">
        <div class="col-md-4">
          &nbsp;&nbsp;&nbsp;<?=H::t('hajj','nokFamilyPhone')?>
        </div>
        <div class="col-md-6">
          &nbsp;&nbsp;&nbsp;<?=H::t('hajj', 'emergencyContactInfo')?>
        </div>
      </div>
    </div>
    <div class="panel-body">
      <div class="form-group">
        <?php echo $form->labelEx($appForm, 'family_contact_no',
          ['class' => 'control-label col-md-2']); ?>
        <div class="col-md-2">
          <?php echo $form->textField($appForm, 'family_contact_no',
            ['class' => 'form-control', 'size' => 30]); ?>
        </div>
        <?php echo $form->labelEx($appForm,
          'emergency_contact_phone_no',
          ['class' => 'col-md-2 control-label']); ?>
        <div class="col-md-3">
          <?php echo $form->textField($appForm,
            H::tf('emergency_contact_full_name_dhivehi'),
            ['size' => 30, 'maxlength' => 255,
             'class' => 'form-control' . (Yii::app()->language == 'dv'?
                 ' thaanaKeyboardInput':''),
             'style' => 'margin-bottom:5px',
             'placeholder' => H::t('hajj', 'emergencyNamePlaceHolder')]); ?>
        </div>
        <div class="col-md-2">
          <?php echo $form->textField($appForm, 'emergency_contact_phone_no',
            ['class' => 'form-control', 'size' => 30, 'placeholder' =>
              H::t('hajj','emergencyPhonePlaceHolder')]); ?>
        </div>
      </div>
    </div>
    <div class="panel-heading"><?=H::t('hajj','hajjInfo')?></div>
    <div class="panel-body">
      <div class="form-group row">
        <?php echo $form->labelEx($appForm, 'badhal_hajj',
          ['class' => 'col-md-2 control-label']); ?>
        <div class="col-md-1">
          <?php echo $form->checkBox($appForm, 'badhal_hajj', []); ?>
        </div>
        <?php echo $form->labelEx($appForm, 'group_name',
          ['class' => 'col-md-2 control-label']); ?>
        <div class="col-md-2">
          <?php echo $form->textField($appForm, 'group_name',
            ['class' => 'form-control']); ?>

        </div>
      </div>
      <div class="form-group row">
        <div class="col-md-6 col-md-offset-3"><?=H::t('hajj',
            'groupLeaderNote')?></div>
      </div>
    </div>
    <div class="panel-heading"><?=H::t('site', 'documents')?></div>
    <div class="panel-body">

      <div class="form-group">
        <?php
        foreach ($docsArray as $doc) {
        $labelSize = $doc=='mahram_document'?'col-md-2':'col-md-3';
        echo $form->labelEx($appForm, $doc,
          ['class' => $labelSize . ' control-label']); ?>
        <script>turnBlockOn = false;</script>
        <div class="col-md-3">
          <div class="input-group">
                  <span class="input-group-btn">
                    <span class="btn btn-info btn-xs btn-file"
                          style="height: 34px"
                          onClick="js:turnBlockOn = false;">
                      <?=H::t('site', 'fileSelect')?> <?php echo
                      $form->fileField($appForm,
                        $doc); ?>
                    </span>
                  </span>
            <input type="text" class="form-control" readonly="">
          </div>
        </div>
        <?php
        }
        ?>
      </div>
      <div class="form-group row">
        <div class="col-md-6"><?=H::t('hajj', 'downloadFormInfo')?></div>
        <div class="col-md-6"><?=H::t('hajj', 'mahramInfo')?></div>
      </div>

    </div>
  </div>
  <?php echo (!$appForm->isNewRecord) ? $form->hiddenField($appForm, 'id') : ''; ?>

  <div class="panel panel-success">
    <div class="panel-footer">
      <div class="form-group">
        <div class="col-md-10 col-md-offset-2">
          <button class="btn btn-sm btn-primary">
            <icon class="glyphicon glyphicon-save"></icon>
            <?=H::t('site', 'submit')?>
          </button>
        </div>
      </div>
    </div>
  </div>
  <?php include_once(Yii::getPathOfAlias('webroot')."/js/registration-helper.php"); ?>

  <?php $this->endWidget(); ?>

</div><!-- form -->
