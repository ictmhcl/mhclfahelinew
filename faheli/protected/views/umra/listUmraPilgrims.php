<?php
/* @var $this UmraController */
/* @var $umraTripPilgrimsDP CActiveDataProvider */
/* @var $model UmraTrips */

if (in_array("editPilgrim", Helpers::perms())) {
  $viewButtonIcon = "glyphicon-pencil";
  $viewbuttonTitle = "View/Edit Pilgrim";
} else {
  $viewButtonIcon = "glyphicon-zoom-in";
  $viewbuttonTitle = "View Pilgrim";
}
?>

<h3>Umra Trip Pilgrims - <?= $model->name_english ?></h3>



<?php

if (in_array("viewPilgrim", Helpers::perms())
  || in_array("editPilgrim", Helpers::perms()))
  echo "<style>.grid-view table tbody tr{cursor: pointer;}</style>";
$this->widget('zii.widgets.grid.CGridView', [
  'id' => 'umra-trips-grid',
  'selectableRows' => (in_array("viewPilgrim", Helpers::perms())
    || in_array("editPilgrim", Helpers::perms())),
  'selectionChanged' =>
    (in_array("viewPilgrim", Helpers::perms())
      || in_array("editPilgrim", Helpers::perms())) ?
      'function(id){
        location.href = "' . $this->createUrl('umra/viewPilgrim') . '/id/"+$.fn.yiiGridView.getSelection(id);
      }' : 'function() {}',
  'dataProvider' => $umraTripPilgrimsDP,
  'columns' => [
    'person.id_no',
    'person.full_name_english',
    'phone_number',
    [
      'header' => 'Paid Amount',
      'value' => '$data->account_balance',
      'name' => 't.account_balance'
    ],
    [
      'header' => 'Full Payment Date',
      'name' => 't.full_payment_date',
      'value' => '!empty($data->full_payment_date_time)?
          (new DateTime($data->full_payment_date_time))->format("d F Y H:m:s"):
          ""'
    ],
    'group_name',
    [
      'header' => 'Mahram',
      'name' => 'mahramPerson.full_name_english'
    ],
    [
      'class' => 'CButtonColumn',
      'header' => 'Actions',
      'template' => '{details} {statement} {payment}',
      'buttons' => [
        'details' => [
          'options' => ['title' => $viewbuttonTitle, 'style' => 'color:orange;'],
          'label' => "<span class='glyphicon $viewButtonIcon'></span>",
          'visible' => 'in_array("viewPilgrim", Helpers::perms())
                    || in_array("editPilgrim",Helpers::perms())',
          'url' => 'Yii::app()->createUrl("umra/viewPilgrim",["id"=>$data->id])'
        ],
        'statement' => [
          'options' => ['title' => 'Pilgrims', 'style' => 'color:darkgrey;'],
          'visible' => '0 && in_array("umraStatement", Helpers::perms())',
          'label' => '<span class="glyphicon glyphicon-list"></span>',
          'url' => 'Yii::app()->createUrl("umra/umraStatement",["id"=>$data->id])'
        ],
        'payment' => [
          'options' => ['title' => 'Collect Payment', 'style' => 'color:green;'],
          'visible' => '($data->account_balance < $data->umraTrip->price) &&
                      in_array("umraPayment", Helpers::perms())',
          'label' => '<span class="glyphicon glyphicon-import"></span>',
          'url' => 'Yii::app()->createUrl("umra/umraPayment",["id"=>$data->id])'
        ],
      ],
    ],
  ],
]);
?>
