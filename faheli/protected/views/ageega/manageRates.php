<?php
/* @var $this AgeegaController */
/* @var $ageegaRatesDp CActiveDataProvider */
?>

<h3>Ageega Rates</h3>

<ul class="nav nav-tabs small">
</ul>
<div class="tab-content">
  <div class="form">
    <div class="panel panel-success">
      <div class="panel-heading small">
        Ageega rates list
      </div>
      <div class="panel-body" style="margin-bottom: 5px">

        <style>
          .grid-view table tbody tr {
            cursor: pointer;
          }
        </style>
        <?php
        echo (!in_array('createAgeegaRate', Helpers::perms())) ? '' :
          ('<a href="' . Yii::app()->createUrl
            ('ageega/createAgeegaRate') . '"id="newRateBtn" class="btn
            btn-success btn-sm pull-left" style="margin-bottom:2px;">New
            Ageega Rate Item <i class="glyphicon glyphicon-plus"></i></a>');

        $this->widget('zii.widgets.grid.CGridView', array(
          'id' => 'ageega-rates-grid',
          'selectableRows' => !in_array('editAgeegaRate',
            Helpers::perms()) ? null : 1,
          'selectionChanged' => in_array('editAgeegaRate',
            Helpers::perms()) ? ('function(id){ location.href = "' .
            $this->createUrl('editAgeegaRate') .
            '/id/"+$.fn.yiiGridView.getSelection(id);}') : '',
          'dataProvider' => $ageegaRatesDp,
          'columns' => [
            [
              'header' => 'Gender',
              'value' => '$data->gender->name_english',
            ],
            [
              'header' => 'Applicable From',
              'value' => '$data->from_date',
              'headerHtmlOptions' => [
                'style' => 'text-align:center',
              ],
              'htmlOptions' => [
                'style' => 'text-align:center',
              ],
            ],
            [
              'header' => 'Applicable To',
              'value' => '$data->till_date',
              'headerHtmlOptions' => [
                'style' => 'text-align:center',
              ],
              'htmlOptions' => [
                'style' => 'text-align:center',
              ],
            ],
            [
              'header' => 'Rate',
              'value' => '$data->rate',
              'headerHtmlOptions' => [
                'style' => 'text-align:right',
              ],
              'htmlOptions' => [
                'style' => 'text-align:right',
              ],
            ],
            [
              'class' => 'CButtonColumn',
              'header' => 'Actions',
              'visible' => in_array("editAgeegaRate", Helpers::perms()),
              'template' => '{update}',
              'buttons' => [
                'update' => [
                  'label' => '<span><icon class="glyphicon glyphicon-pencil"></icon></span>',
                  'visible' => 'in_array("editAgeegaRate",Helpers::perms())',
                  'options' => ['style' => 'cursor: pointer;', 'title' => 'Update'],
                  'imageUrl' => false,
                  'url' => 'Yii::app()->createUrl("ageega/editAgeegaRate",array
                                    ("id"=>$data->id))',
                ],
              ],
              'htmlOptions' => [
                'style' => 'text-align:center; vertical-align:middle;width: 120px',
              ],
            ],

          ],
        ));
        ?>
      </div>

    </div>
  </div>

