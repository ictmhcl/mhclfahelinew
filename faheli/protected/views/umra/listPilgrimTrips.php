<?php
/* @var $this UmraController */
/* @var $dp CArrayDataProvider */
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
if (in_array("umraStatement", Helpers::perms()))
  echo "<style>.grid-view table tbody tr{cursor: pointer;}</style>";
$this->widget('zii.widgets.grid.CGridView', [
  'id' => 'umra-trips-grid',
  'selectableRows' => in_array("listUmraPilgrims", Helpers::perms()),
  'selectionChanged' =>
    in_array("umraStatement", Helpers::perms()) ?
      'function(id){
        location.href = "' . $this->createUrl('umra/umraStatement') .
      '/id/"+$.fn.yiiGridView.getSelection(id);
      }' : 'function() {}',
  'dataProvider' => $dp,
  'columns' => [
    [
      'header' => 'Umra Trip',
      'name' => 'name_english',
    ],
    [
      'name' => 'price',
      'value' => 'Helpers::currency($data->price)'
    ],
    [
      'header' => 'Paid Amount',
      'value' => 'Helpers::currency($data->paid)',
      'htmlOptions' => ['style' => 'text-align:right'],
      'headerHtmlOptions' => ['style' => 'text-align:right'],
    ],
    [
      'header' => 'Pending Amount',
      'value' => 'empty($data->price - $data->paid)?"":
        Helpers::currency($data->price - $data->paid)',
      'htmlOptions' => ['style' => 'color:red; text-align:right'],
      'headerHtmlOptions' => ['style' => 'text-align:right'],
    ],
    [
      'header' => 'Last Payment',
      'value' => 'empty($data->last_payment_date)?"None":
        (new DateTime($data->last_payment_date))->format("d M Y h:i A")',
      'htmlOptions' => ['style' => 'text-align:center'],
      'headerHtmlOptions' => ['style' => 'text-align:center'],
    ],

    ['class' => 'CButtonColumn',
      'header' => 'Actions',
      'visible' => in_array("umraPayment", Helpers::perms()),
      'template' => '{payment}',
      'buttons' => [
        'payment' => [
          'options' => ['title' => 'Collect Payment', 'style' => 'color:green;'],
          'visible' => '$data->paid < $data->price',
          'label' => '<span class="glyphicon glyphicon-import"></span>',
          'url' => 'Yii::app()->createUrl("umra/umraPayment",["id"=>$data->id])'
        ],
      ],
    ],
  ],
]);
?>
