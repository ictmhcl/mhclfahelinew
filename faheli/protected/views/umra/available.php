<?php
/* @var $this UmraController */
/* @var $availableDp CActiveDataProvider*/
?>

<h3>
    <span style="color:green">
    <?=$this->person->{H::tf('full_name_dhivehi')} . ' (' . $this->person->id_no
    . ')'?></span><br/>
</h3>
<ul class="nav nav-tabs small">

</ul>

<div class="tab-content">
  <div class="form">

    <div class="panel panel-success">
      <div class="panel-heading"><?=H::t('umra', 'plannedTrips')?></div>
      <div class="panel-body">
        <div class="col-md-12" style="margin-bottom:10px">
          <?php
          $this->widget('zii.widgets.grid.CGridView', [
            'id' => 'available-umra-grid',
//            'rowCssClassExpression' => '($data->is_cancelled==1)?"cancelled-transaction":""',
            'template' => '{items}', 'dataProvider' => $availableDp,
            'columns' => [
              [
                'header' => H::t('act','year'), 'type' => 'raw',
                'value' => '$data->year',
                'htmlOptions' => ['style' => H::t('act','txtAlign'),'class' => 'hidden-xs'],
                'headerHtmlOptions' => ['class' => 'hidden-xs'],
              ], [
                'header' => H::t('act', 'month'), 'value' =>
                  Yii::app()->language == 'dv'? 'ZDhivehiMonths::model()->findByPk($data->month)->name_dhivehi;'
                : '(new DateTime("2000-".$data->month."-01"))->format("F")',
                'htmlOptions' => ['style' => H::t('act', 'txtAlign'),'class' => 'hidden-xs'],
                'headerHtmlOptions' => ['class' => 'hidden-xs'],
              ], [
                'header' => H::t('act','tripName'),
                'type' => 'raw',
                'value' => '$data->{H::tf("name_dhivehi")} . " <a style=\'font-size: 14px\'
                  class=\'btn btn-".(!empty($data->currentPilgrim)?"success":"primary")." btn-xs pull-left\'
                  href=\'".Yii::app()->createUrl("umra/umraDetails",["id" =>$data->id])."\'>".
                  H::t("act","details")
                  ."</a>"',
                'htmlOptions' => ['style' => H::t('act', 'txtAlign')],
              ], [
                'header' => H::t('act', 'deadline'),
                'type' => 'raw',
                'value' => 'empty($data->deadline_date)?"":
                  (Yii::app()->language=="dv"?Helpers::mvDate
                  ($data->deadline_date):(new DateTime($data->deadline_date))
                  ->format("d M Y"))',
                'htmlOptions' => ['style' => H::t('act', 'txtAlign').';width:120px'],
              ], [
                'header' => H::t('act','mvrPrice'), 'value' => 'Helpers::currency
          ($data->price)',
                'htmlOptions' => ['style' => 'width:40px;text-align:right'],
                'headerHtmlOptions' => ['style' => 'width:130px;
                text-align:right'],
              ],[
                'header' => H::t('act', 'tripState'), 'type' => 'raw',
                'value' =>
                  '$data->closed?
                    "<span style=\'font-size: 14px; font-weight: 600\'
                    class=\'text-danger\'>".H::t("act","closed")."</span>":
                    "<span style=\'font-size: 14px; font-weight: 600\'
                    class=\'text-success\'>".H::t("act","open")."
                    </span>"',
                'headerHtmlOptions' => ['style' => 'text-align: center'],
                'htmlOptions' => ['style' => 'text-align: center'],
              ],[
                'header' => H::t('act', 'registration'), 'type' => 'raw',
                'value' =>
                  '!empty($data->currentPilgrim)?
                  "<a style=\'font-size: 14px\' class=\'btn btn-success btn-xs\'
                  href=\'".
                  Yii::app()->createUrl("umra/umraDetails",["id" =>$data->id])."\'>".H::t("act","registered")."</a>":
                  ($data->closed?"":"<a style=\'font-size: 14px\'
                  href=\"".Yii::app()->createUrl(\'umra/createUmraPilgrim\',[\'umraTripId\' => $data->id])."\"
                  class=\'btn btn-primary btn-xs\'>".H::t("act","registerHere")."</a>")',
                'headerHtmlOptions' => ['style' => 'text-align: center'],
                'htmlOptions' => ['style' => 'text-align: center'],
              ],
            ],
          ]);
          ?>
        </div>
      </div>
    </div>

  </div><!-- form -->
</div>
