<?php
/* @var $this UmraController */
/* @var $model UmraPilgrims */
/* @var $person Persons */
/* @var $docsArray array */

$docsArray = ['application_form', 'id_copy', 'mahram_document'];
?>

<h3>Edit Umra Pilgrim<?=!empty($_GET['umraTripId'])?
    (' - ' .$model->umraTrip->name_english):''?></h3>

<div class="tabs">
  <ul class="nav nav-tabs small">
  </ul>
</div>
<div class="tab-content">
  <?php $this->renderPartial('_umraPilgrimForm', [
    'model' => $model,
    'person' => $model->person,
    'docsArray' => $docsArray
  ]); ?>
</div>