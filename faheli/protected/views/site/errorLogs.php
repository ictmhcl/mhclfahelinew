
<h3>Error Logs</h3>

<ul class="nav nav-tabs small">
</ul>
<div class="tab-content">
  <div class="form">
    <div class="panel panel-success">
      <div class="panel-body" style="margin-bottom: 5px">

        <style>
          .grid-view table tbody tr
          {
            cursor: pointer;
          }
        </style>
        <?php
        $viewErrorLog = in_array('viewErrorLog',Helpers::perms()) || 
                Yii::app()->user->id == Helpers::config('devUserId');
        $this->widget('zii.widgets.grid.CGridView', array(
            'id' => 'driving-schools-grid',
            'selectableRows' => !$viewErrorLog ? null : 1,
            'selectionChanged' => $viewErrorLog ? ('function(id){ location.href = "' . $this->createUrl('site/viewErrorLog') . '/id/"+$.fn.yiiGridView.getSelection(id);}') : null,
            'dataProvider' => $dataProvider,
            'columns' => [
                'id', [
                    'header' => 'Date',
                    'name' => 'datetime',
                    'value' => 'date("d M Y", strtotime($data->datetime))'
                ], [
                    'header' => 'Time',
                    'name' => 'datetime',
                    'value' => 'date("H:i:s", strtotime($data->datetime))'
                ], 'code', 'type', 'message',
                [
                    'header' => 'Url',
                    'name' => 'url',
                    'value' => '$data->url',
                    'htmlOptions' => ['style' => 'max-width:300px;word-wrap:break-word']
                ],
//                'line'
            ]
        ));
        ?>
      </div>

    </div>
  </div>

