<?php
/* @var $this RegistrationController */
/* @var $model ApplicationForms */

?>

<h3><span style="color:green">
    <?=$this->person->{H::tf('full_name_dhivehi')} . ' (' .
    $this->person->id_no . ') - ' . $formTitle?></span></h3>

<div class="tabs">
  <ul class="nav nav-tabs small">
  </ul>
</div>

<div class="tab-content">
  <?php $this->renderPartial('_form', ['appForm' => $appForm]); ?>
</div>