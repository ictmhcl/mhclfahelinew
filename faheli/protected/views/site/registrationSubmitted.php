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
          <?php echo $form->labelEx($onlineForm, 'full_name_dhivehi', ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-4 formDisplay">
            <?=$onlineForm->full_name_dhivehi?>
          </div>
        </div>
        <div class="form-group">
          <?php echo $form->labelEx($onlineForm, 'id_no', ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-2 formDisplay">
            <?=$onlineForm->id_no?>
          </div>
          <?php echo $form->labelEx($onlineForm, 'd_o_b', ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-2 formDisplay">
            <?=Helpers::mvDate($onlineForm->d_o_b)?>
          </div>
          <?php echo $form->labelEx($onlineForm, 'gender_id', ['class' => 'col-md-1 control-label']); ?>
          <div class="col-md-2 formDisplay">
            <?=$onlineForm->gender->name_dhivehi?>
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel-success">
      <div class="panel-heading"><?=H::t('site', 'addressContactInfo')?></div>
      <div class="panel-body">
        <div class="form-group row">
          <?php echo $form->labelEx($onlineForm, 'country_id', ['class' => 'control-label col-md-2']); ?>
          <div class="col-md-2 formDisplay">
            <?=$onlineForm->country->name_dhivehi?>
          </div>
          <div class="permIsland"
               style="display:<?=$onlineForm->country_id ==
               Constants::MALDIVES_COUNTRY_ID ? 'block' : 'none'?>">
            <?php echo $form->labelEx($onlineForm, 'perm_address_island_id', [
              'class' => 'control-label col-md-1'
            ]); ?>

            <div class="col-md-4 formDisplay">
              <?=$onlineForm->atollIslandDhivehi?>
            </div>
          </div>
        </div>
        <div class="form-group">
          <?php echo $form->labelEx($onlineForm, 'perm_address_english', ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-4 formDisplay">
            <?=$onlineForm->perm_address_english?>
          </div>
          <?php echo $form->labelEx($onlineForm, 'perm_address_dhivehi', ['class' => 'control-label col-md-2']); ?>
          <div class="col-md-4 formDisplay">
            <?=$onlineForm->perm_address_dhivehi?>
          </div>
        </div>
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
    <div class="panel panel-success">
      <div class="panel-heading"><?=H::t('site', 'documents')?></div>
      <div class="panel-body">

        <div class="form-group">
          <?php
          foreach ($docsArray as $doc) {
            $labelSize = $doc == "id_card_copy" ? 'col-md-2' : 'col-md-2';
            $viewSize = $doc == "id_card_copy" ? 'col-md-2' : 'col-md-1';
            echo $form->labelEx($onlineForm, $doc, [
              'class' => $labelSize . ' control-label'
            ]);
            echo CHtml::link(H::t('site','view'), Helpers::sysUrl
              (Constants::UPLOADS) .
              $onlineForm->$doc, [
              'target' => '_blank', 'class' => 'formDisplay'
            ]);

          }
          ?>
        </div>
      </div>
    </div>
    <?php $this->endWidget(); ?>

  </div><!-- form -->
</div>