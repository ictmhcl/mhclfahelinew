<?php
/* @var $this RegistrationController */
/* @var $model ApplicationForms */

?>

<h3><span style="color:green">
    <?=$this->person->{H::tf('full_name_dhivehi')} . ' ('
    . $this->person->id_no . ') - ' . H::t('hajj', 'registerForHajj')
    ?></span></h3>

<div class="tabs">
  <ul class="nav nav-tabs small">
  </ul>
</div>

<div class="tab-content">
  <div class="form small">

    <?php
    $form = $this->beginWidget('CActiveForm', [
      'id' => 'application-forms-form', 'enableAjaxValidation' => false,
      'htmlOptions' => [
        'class' => 'form-horizontal', 'enctype' => 'multipart/form-data'
      ]
    ]);
    ?>
    <div class="alert alert-info alert-flash" style="margin: 15px">
      <?=$this->person->{H::tf('full_name_dhivehi')} . H::t('hajj',
        'registrationSubmittedAlert')?>
    </div>
    <?php $this->endWidget() ?>
</div>