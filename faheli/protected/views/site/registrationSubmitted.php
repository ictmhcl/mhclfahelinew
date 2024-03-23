<?php
/* @var $this RegistrationController */
/* @var $model ApplicationForms */

?>
<style>
  @media screen and (min-width: 992px) {
    .formDisplay {
      margin-top: 5px;
    }
  }

  .formDisplay {
    font-size: 16px;
    color: blue;
  }
</style>
<h3><span style="color:green"><?=H::t('site', 'registerOnPortal')?></span></h3>

<div class="tabs">
  <ul class="nav nav-tabs small">
  </ul>
</div>

<div class="tab-content">
  <?php
  /* @var $this RegistrationController */
  /* @var $onlineForm OnlineRegistrationForm */
  /* @var $form CActiveForm */

  ?>

  <div class="form small">

    <?php
    $form = $this->beginWidget('CActiveForm', [
      'id' => 'online-registration-form', 'enableAjaxValidation' => false,
      'htmlOptions' => [
        'class' => 'form-horizontal', 'enctype' => 'multipart/form-data'
      ]
    ]);
    ?>

    <?php
    $docsArray = ['id_card_copy'];

    ?>
    <div class="panel panel-success">
      <div class="panel-heading"><?=H::t('site', 'identityInfo')?></div>
      <div class="panel-body">
        <div class="form-group">
          <?php echo $form->labelEx($onlineForm, 'full_name_english', ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-4 formDisplay">
            <?=$onlineForm->full_name_english?>
          </div>
        </div>
        <div class="form-group">
          <?php echo $form->labelEx($onlineForm, 'id_no', ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-2 formDisplay">
            <?=$onlineForm->id_no?>
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel-success">
      <div class="panel-heading"><?=H::t('site', 'contactInfo')?></div>
      <div class="panel-body">
        <div class="form-group">
          <?php echo $form->labelEx($onlineForm, 'phone_number_1', ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-3 formDisplay">
            <?=$onlineForm->phone_number_1?>
          </div>
          <?php echo $form->labelEx($onlineForm, 'email_address', ['class' => 'control-label col-md-2']); ?>
          <div class="col-md-3 formDisplay">
            <?=$onlineForm->email_address?>
          </div>
        </div>

      </div>
    </div>

    <?php $this->endWidget(); ?>

  </div><!-- form -->
</div>