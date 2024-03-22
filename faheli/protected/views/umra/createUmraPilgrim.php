<?php
/* @var $this UmraController */
/* @var $model UmraPilgrims */
/* @var $trip UmraTrips */
/* @var $docsArray array */

?>
<h3>
    <span style="color:green">
    <?=$this->person->{H::tf('full_name_dhivehi')} . ' (' . $this->person->id_no
    . ')'?></span><br/><br/><span class="small"><?=H::t('umra',
      'registerFor {umra}', ['umra' => $trip->{H::tf("name_dhivehi")}])?></span>
</h3>

<div class="tabs">
  <ul class="nav nav-tabs small">
  </ul>
</div>

<div class="tab-content">
  <?php $this->renderPartial('_umraPilgrimForm', ['model' => $model,'trip' => $trip,'docsArray' => $docsArray]); ?>
</div>