<?php
/* @var $this UmraController */
/* @var $model UmraTrips */
?>

<h3>Update Umra Trip</h3>
<ul class="nav nav-tabs small">
</ul>

<?php $this->renderPartial('_umraTripForm', [
  'model' => $model,
]); ?>