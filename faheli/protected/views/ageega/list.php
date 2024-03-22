<?php
/* @var $this UmraController */
/* @var $ageegaDp CActiveDataProvider */
?>
<style>
  .grid-view {
    padding: 10px 0 0
  }
</style>
<h3>
    <span style="color:green">
    <?=$this->person->{H::tf('full_name_dhivehi')} . ' (' . $this->person->id_no
    . ') - '?> <?=H::t('ageega', 'ageegaServices')?></span>
</h3>
<ul class="nav nav-tabs small">

</ul>

<div class="tab-content">
  <div class="form">

    <div class="panel panel-success">
      <div class="panel-heading"><?=H::t('ageega', 'servicesTaken')?></div>
      <div class="panel-body">
        <div class="col-md-12" style="margin-bottom:10px">
          <?=CHtml::link('<span class="btn btn-primary btn-sm">'.
            H::t('ageega','registerForAgeega').'</span>', Yii::app()
            ->createUrl('ageega/register'))?>
        </div>
        <?php if (sizeof($ageegaDp->getData()) > 0) { ?>
          <div class="col-md-12" style="margin-bottom:10px">
            <?php
            $this->widget('zii.widgets.grid.CGridView', [
              'id' => 'available-umra-grid', 'template' => '{items}',
              'dataProvider' => $ageegaDp, 'columns' => [
                [
                  'header' => H::t('act','date'), 'type' => 'raw',
                  'value' => 'Yii::app()->language=="dv"?Helpers::mvDate
                  ($data->full_payment_date_time):(new DateTime($data->full_payment_date_time))->format("d F
                  Y")',
                  'htmlOptions' => ['style' => H::t('act','txtAlign').';
                  width:130px'],
                ], [
                  'header' => H::t('ageega', 'sheepQty'), 'type' => 'raw',
                  'value' => '$data->sheepCount',
                  'htmlOptions' => ['style' => H::t('act', 'txtAlign')],
                ], [
                  'header' => H::t('ageega', 'details'), 'type' => 'raw',
                  'value' => '$data->childrenNames?:$data->ageegaReason->{H::tf("name_dhivehi")}',
                  'htmlOptions' => ['style' => H::t('act', 'txtAlign')],
                ], [
                  'header' => H::t('ageega', 'paidAmount'), 'type' => 'raw',
                  'value' => 'Helpers::currency($data->totalPaid)',
                  'htmlOptions' => ['style' => H::t('act', 'txtAlign')],
                ],[
                  'header' => H::t('site', 'documents'), 'type' => 'raw',
                  'value' => '$data->ageega_form?CHtml::link(\'<span class="glyphicon glyphicon-file"></span>\',
                  Helpers::sysUrl(Constants::UPLOADS).$data->ageega_form,[\'target\' => \'_blank\']):""',
                  'htmlOptions' => ['style' => H::t('act', 'txtAlign')],
                ],
              ],
            ]);
            ?>
          </div>
        <?php } ?>
      </div>
    </div>

  </div><!-- form -->
</div>
