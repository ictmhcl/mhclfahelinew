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
    'selectionChanged' => 'function(id){ location.href = "' . (Yii::app()->controller->action->id == 'incomplete' ? $this->createUrl('create') : $this->createUrl('verifyForm')) . '/id/"+$.fn.yiiGridView.getSelection(id);}',
    'dataProvider' => $dataProvider,
    'columns' => [
        'id_no',
        'applicant_full_name_english',
        [
            'header' => 'State',
            'value' => '$data->state->name_english',
            'htmlOptions' => [
                'style' => 'width:170px;text-align:center',
            ],
      'headerHtmlOptions' => [
        'style' => 'width:170px;text-align:center',
      ],
        ],
        'application_date',
        [
            'header' => 'Entered/Updated by',
            'value' => 'empty($data->operationLog->modified_by_user_id)?
                ($data->operationLog->createdUser->person->full_name_english):
                ($data->operationLog->modifiedUser->person->full_name_english)',
            'cssClassExpression' => '
                (empty($data->operationLog->modified_by_user_id)?
                ($data->operationLog->createdUser->id):
                ($data->operationLog->modifiedUser->id))==Yii::app()->user->id?"error":""
            ',
            'htmlOptions' => [
                'style' => 'width:170px;text-align:center',
            ],
            'headerHtmlOptions' => [
                'style' => 'text-align:center',
            ]
        ], [
          'header' => 'Organization', 'value' => 'empty($data->operationLog->modified_by_user_id)?
                ($data->operationLog->createdUser->organization->membership_prefix):
                ($data->operationLog->modifiedUser->organization->membership_prefix)',
          'cssClassExpression' => '
                (empty($data->operationLog->modified_by_user_id)?
                ($data->operationLog->createdUser->id):
                ($data->operationLog->modifiedUser->id))==Yii::app()->user->id?"error":""
            ', 'htmlOptions' => [
          'style' => 'width:170px;text-align:center',
        ], 'headerHtmlOptions' => [
          'style' => 'text-align:center',
        ]
        ], [
            'class' => 'CButtonColumn',
            'header' => 'Actions',
            'template' => '{Edit} {Verify}',
            'buttons' => [
                'Edit' => [
                    'options' => ['title' => 'Edit', 'style' => 'color:blue;'],
                    'label' => '<span class="glyphicon glyphicon-forward"></span>',
                    'visible' => '$data->state_id==Constants::APPLICATION_INCOMPLETE',
                    'url' => 'Yii::app()->createUrl("registration/create",array(
                        "id"=>$data->id))',
                ],
                'Verify' => [
                    'options' => ['title' => 'Verify', 'style' => 'color:blue'],
                    'label' => '<span class="glyphicon glyphicon-forward"></span>',
                    'visible' => '$data->state_id==Constants::APPLICATION_PENDING_VERIFICATION',
                    'url' => 'Yii::app()->createUrl("registration/verifyForm",array(
                        "id"=>$data->id))',
                ],
            ],
            'htmlOptions' => [
                'style' => 'text-align:center; vertical-align:middle',
            ],
        ],
//        array(
//            'class' => 'CButtonColumn',
//        ),
    ],
]);
?>
