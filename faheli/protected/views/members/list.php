<?php
/* @var $this MembersController */
/* @var $model Members */
?>

<h3><?php echo (!empty($title) ? $title : 'Members') ?></h3>

<ul class="nav nav-tabs small">
  <?php if (isset($_GET['q'])) { ?>
    <li class="active"><a>Search Results</a></li>
  <?php } ?>
</ul>

<div class="tab-content">
  <div class="form">
    <?php
    $form = $this->beginWidget('CActiveForm', [
      'id' => 'members-form',
      'method' => 'get',
      'action' => 'search',
      // Please note: When you enable ajax validation, make sure the corresponding
      // controller action is handling ajax validation correctly.
      // There is a call to performAjaxValidation() commented in generated controller code.
      // See class documentation of CActiveForm for details on this.
      'enableAjaxValidation' => false,
    ]);
    ?>

    <div class="panel panel-success">
      <div class="panel-body small">
        <div class="col-md-8">
          <?php $this->renderPartial('_search'); ?>
        </div>
      </div>
    </div>
    <?php $this->endWidget() ?>
    <div class="panel panel-success">
      <div class="panel-heading small">Members List</div>
      <div class="panel-body" style="margin-bottom:5px">
        <style>.grid-view table tbody tr{cursor: pointer;}</style>

        <?php
        $this->widget('zii.widgets.grid.CGridView', [
            'id' => 'members-grid',
            'selectableRows' => 1,
            'selectionChanged' => 'function(id){ location.href = "' . $this->createUrl('view') . '/id/"+$.fn.yiiGridView.getSelection(id);}',
            'dataProvider' => $dataProvider,
            'columns' => [
                //'id',
                //'state_id',
                [
                    'header' => 'MHC No',
                    'value' => '$data->MHC_ID',
                    'htmlOptions' => [
                        'style' => 'width:120px;text-align:left',
                    ],
                ],
                [
                    'header' => 'Name',
                    'value' => '$data->person->full_name_english',
                    'htmlOptions' => [
                        'style' => 'width:100px;text-align:left',
                    ],
                ],
                [
                    'header' => 'Permanent Address',
                    'value' => '$data->person->permAddressText',
                    'htmlOptions' => [
                        'style' => 'width:150px;text-align:left',
                    ],
                ],
                [
                    'header' => 'Current Address',
                    'value' => '$data->current_address_english',
                    'htmlOptions' => [
                        'style' => 'width:150px;text-align:left',
                    ],
                ],
                [
                    'header' => 'Phone',
                    'value' => '$data->phone_number_1',
                    'htmlOptions' => [
                        'style' => 'width:50px;text-align:left',
                    ],
                ],
                [
                    'header' => 'Balance',
                    'value' => 'Helpers::currency($data->accountBalance)',
                    'htmlOptions' => [
                        'style' => 'color: blue; width:70px;text-align:right',
                    ],
                    'headerHtmlOptions' => [
                        'style' => ' text-align:right',
                    ],
                ],
                [
                    'class' => 'CButtonColumn',
                    'header' => 'Actions',
                    'template' => '{View}&nbsp;&nbsp;{Deposits}<br>{Statement}&nbsp;&nbsp;{message}',
                    'headerHtmlOptions' => [
                        'style' => 'width:70px;text-align:center',
                    ],
                    'buttons' => [
                        'View' => [
                            'options' => ['title' => 'Member Details', 'style' => 'color:orange'],
                            'label' => '<span class="glyphicon glyphicon-user"></span>',
                            'url' => 'Yii::app()->createUrl("members/view",array(
                        "id"=>$data->id))',
                        ],
                        'Deposits' => [
                            'options' => ['title' => 'Deposit', 'style' => 'color:green'],
                            'label' => '<span class="glyphicon glyphicon-import"></span>',
                            'url' => 'Yii::app()->createUrl("members/deposit",array(
                        "id"=>$data->id))',
                        ],
                        'Statement' => [
                            'options' => ['title' => 'Statement', 'style' => 'color:#1aa695'],
                            'label' => '<span class="glyphicon glyphicon-list"></span>',
                            'url' => 'Yii::app()->createUrl("members/statement",array(
                        "id"=>$data->id))',
                        ],
                        'message' => [
                            'label' => '<icon class="glyphicon glyphicon-envelope" style="color:green"></icon>',
                            'options' => ['style' => 'cursor: pointer;', 'title' => 'Send Message'],
                            'url' => 'Yii::app()->createUrl("messaging/index",array("recipient"=>$data->id))',
                        ],
                    ],
                    'htmlOptions' => [
                        'style' => 'width:70px;text-align:center; vertical-align:middle',
                    ],
                ],
            ],
        ]);
        ?>
      </div>
    </div>

  </div>
</div>
