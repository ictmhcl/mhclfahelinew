<?php
/* @var $this RegistrationController */
/* @var $appForm ApplicationForms */
?>

<h3><span style="color:green">Verify
    <?=$appForm->applicant_full_name_english . ', ' . $appForm->id_no?></span>
</h3>
<div class="tabs">
  <ul class="nav nav-tabs small">
  </ul>
</div>
<div class="tab-content">
  <?php
  $this->renderPartial('_verifyForm',['appForm' => $appForm,'verifyModel' => $verifyModel]);
  ?>
</div>