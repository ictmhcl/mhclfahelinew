<?php
/* @var $this RegistrationController */
/* @var $model ApplicationForms */
?>

<h3><?php echo $formTitle ?></h3>

<style>
  .grid-view table tbody tr
  {
    cursor: pointer;
  }
</style>


<?php
$this->widget('zii.widgets.grid.CGridView', [
    'id' => 'application-forms-grid',
    'selectableRows' => 1,
    'selectionChanged' => 'function(id){ location.href = "' . $this->createUrl('regFee') . '/id/"+$.fn.yiiGridView.getSelection(id);}',
    'dataProvider' => $dataProvider,
    'columns' => [
        'person.id_no:text:ID Card',
        [
            'header' => 'Member No.',
            'value' => '$data->MHC_ID',
        ],
        'person.full_name_english:text:Name',
        'state.name_english:text:Status',
        [
            'header' => 'Application Date',
            'value' => 'date("d F Y",strtotime($data->applicationForm->application_date))',
        ],
        [
            'class' => 'CButtonColumn',
            'header' => 'Actions',
            'template' => '{Payment}',
            'buttons' => [
                'Payment' => [
                    'label' => '<icon class="glyphicon glyphicon-import"></icon>',
                    'options' => ['title' => 'Collect Payment', 'style' => 'cursor: pointer;'],
                    'url' => 'Yii::app()->createUrl("registration/regFee",array(
                        "id"=>$data->id))',
                ],
            ],
            'htmlOptions' => [
                'style' => 'text-align:center; vertical-align:middle',
            ],
        ],
    ],
]);
?>
