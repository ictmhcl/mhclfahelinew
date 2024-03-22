<?php
/* @var $this UmraController */
/* @var $umarTripDP CActiveDataProvider */
?>

<h3>Manage Umra Trips</h3>

<?php
$form = $this->beginWidget('CActiveForm', [
  'id' => 'members-statement-form',
  'method' => 'get',
  'action' => 'searchPilgrim',
  'enableAjaxValidation' => false,
]);

$this->renderPartial('//umra/_search');
$this->endWidget();
?>


<?php
if (in_array("listUmraPilgrims",Helpers::perms()))
  echo "<style>.grid-view table tbody tr{cursor: pointer;}</style>";
$this->widget('zii.widgets.grid.CGridView', [
  'id' => 'umra-trips-grid',
  'selectableRows' => in_array("listUmraPilgrims",Helpers::perms()),
  'rowCssClassExpression' => '$data->completed?"danger":""',
  'selectionChanged' =>
    in_array("listUmraPilgrims",Helpers::perms()) ?
      'function(id){
        location.href = "' . $this->createUrl('umra/listUmraPilgrims') . '/umraTripId/"+$.fn.yiiGridView.getSelection(id);
      }' : 'function() {}',
  'dataProvider' => $umraTripsDP,
  'columns' => [
    'name_english',
    [
      'header' => 'Trip Time',
      'value' => '$data->year . " " . $data->monthText',
      'name' => 'tripTime'
    ],
    [
      'name' => 'price',
      'value' => 'Helpers::currency($data->price)'
    ],
    'registered', 'fullyPaid','completed',
    [
      'header' => 'All receipts',
      'value' => 'Helpers::currency($data->receipts)',
      'headerHtmlOptions' => ['style' => 'text-align: right'],
      'htmlOptions' => ['style' => 'text-align: right'],
    ],
    ['class' => 'CButtonColumn',
    'header' => 'Actions',
    'template' => '{edit} {lists} {newPilgrim}',
    'buttons' => [
      'edit' => [
        'options' => ['title' => 'Edit', 'style' => 'color:blue;'],
        'label' => '<span class="glyphicon glyphicon-pencil"></span>',
        'visible' => 'in_array("updateUmra",Helpers::perms())',
        'url' => 'Yii::app()->createUrl("umra/updateUmra",array(
                        "id"=>$data->id))',
      ],
      'lists' => [
        'options' => ['title' => 'Pilgrims', 'style' =>
          'color:#1aa695;'],
        'label' => '<span class="glyphicon glyphicon-list"></span>',
        'visible' => 'in_array("listUmraPilgrims",Helpers::perms())',
        'url' => 'Yii::app()->createUrl("umra/listUmraPilgrims",
                    array("umraTripId"=>$data->id))',
      ],
      'newPilgrim' => [
        'options' => ['title' => 'Pilgrim', 'style' =>
          'color:orange;'],
        'label' => '<span class="glyphicon glyphicon-plus"></span>',
        'url' => 'Yii::app()->createUrl("umra/createUmraPilgrim",
                    array("umraTripId"=>$data->id))',
      ],
    ],
  ],
],
]);
?>
