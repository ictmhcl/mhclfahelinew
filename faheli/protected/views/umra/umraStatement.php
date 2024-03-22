<?php
/* @var $this UmraController */
/* @var $pilgrim UmraPilgrims */
?>

<h3><span style="color:green;"><?=$pilgrim->person->full_name_dhivehi?></span>
  (<?= $pilgrim->umraTrip->name_dhivehi . ' ސްޓޭޓްމަންޓް'?>)
  <br><br><small><strong><?= $pilgrim->getUMRA_PILGRIM_ID() ?></strong></small>
</h3>

<ul class="nav nav-tabs small">
</ul>

<div class="tab-content">
  <div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', [
      'id' => 'umra-pilgrim-statement-form',
      'method' => 'get',
      'action' => 'search',
      'enableAjaxValidation' => false,
    ]);
    ?>
    <div class="panel panel-success small">
      <div class="panel-body">
        <div class="col-md-4 pull-left">
          <div class="input-group" style="margin-bottom: 5px">
            <div class="input-group-addon">
              ޖަމާކުރެވިފައިވާ ޢަދަދު
            </div>
            <span class="form-control balance">
              <?=Helpers::currency($pilgrim->account_balance, 'ރ', 2, 'rtl')?>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel-success">
      <div class="panel-heading">އެކައުންޓް ތަފްޞީލް</div>
      <div class="panel-body">
        <div class="col-md-12" style="margin-bottom:10px">
          <?php
          $this->widget('zii.widgets.grid.CGridView', [
            'id' => 'members-grid',
            'rowCssClassExpression' => '($data->is_cancelled==1)?"cancelled-transaction":""',
            'template' => '{items}', 'dataProvider' => $dataProvider,
            'columns' => [
              [
                'header' => 'ތާރީޚް', 'type' => 'raw', 'value' => 'Helpers::mvDate((new DateTime
                ($data->transaction_time))
                  ->format("d M Y H:i:s"),true)',
                'htmlOptions' => ['style' => 'text-align:right'],
              ], [
                'header' => 'ތަފްޞީލް', 'value' => '$data->description_dhivehi',
                'htmlOptions' => ['style' => 'text-align:right'],
              ], [
                'header' => 'މާއްދާ',
                'value' => '$data->transactionMedium->name_dhivehi',
                'htmlOptions' => ['style' => 'text-align:right'],
              ], [
                'header' => 'މަދުވި', 'value' => '(($data->amount < 0)?
                  Helpers::currency(-$data->amount) : "")',
                'htmlOptions' => ['style' => 'width:40px;text-align:right'],
                'headerHtmlOptions' => ['style' => 'width:80px;text-align:right'],
              ], [
                'header' => 'އިތުރުވި', 'value' => '(($data->amount > 0)?
                  Helpers::currency($data->amount) : "")',
                'htmlOptions' => ['style' => 'text-align:right'],
                'headerHtmlOptions' => ['style' => 'width:80px;text-align:right'],
              ],
            ],
          ]);
          ?>
        </div>
      </div>
    </div>
    <?php $this->endWidget(); ?>

  </div><!-- form -->
</div>
