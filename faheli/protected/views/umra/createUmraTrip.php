<?php
/* @var $this UmraController */
/* @var $model UmraPilgrims */
/* @var $person Persons */
?>

<h3>New Umra Trip</h3>
<ul class="nav nav-tabs small">
</ul>

<?php $this->renderPartial('_umraTripForm', [
  'model' => $model,
]); ?>
