<?php
/* @var $this MembersController */
/* @var $model Members */

class Mfilter extends CFormModel {

  public $maturedFrom;
  public $maturedBy;

}
?>

<h3><?php echo (!empty($title) ? $title : 'Members Filter') ?></h3>

<ul class="nav nav-tabs small">
  <?php if (isset($_GET['q'])) { ?>
    <li class="active"><a>Search Results</a></li>
  <?php } ?>
</ul>
<div class="tab-content">
  <div class="form ">
    <?php
    $form = $this->beginWidget('CActiveForm', [
        'id' => 'memberFilter',
        'method' => 'get',
        'action' => $this->createUrl(''),
        // Please note: When you enable ajax validation, make sure the corresponding
        // controller action is handling ajax validation correctly.
        // There is a call to performAjaxValidation() commented in generated controller code.
        // See class documentation of CActiveForm for details on this.
        'enableAjaxValidation' => false,
    ]);
    ?>

    <div class="panel panel-success small">
      <div class="panel-heading">Filter Settings</div>
      <div class="panel-body">
        <div class="col-md-7">
          <div class="input-group">
            <span class="input-group-addon">Matured?</span>
            <?php echo CHtml::dropDownList('matured', $matured, [0 => 'All', 1 => 'Yes', 2 => 'No'], ['class' => 'form-control', 'style' => 'width:80px']); ?>
            <span class="input-group-addon" style="border-left:none;border-right:none">Value</span>
            <?php echo CHtml::textField('maturityValue', $maturityValue, ['class' => 'form-control', 'style' => 'width:80px']); ?>


            <?php
            //
            $matDate = new Mfilter();
            $matDate->maturedFrom = $maturedFrom;
            $matDate->maturedBy = $maturedBy;
            ?>

            <span class="input-group-addon" style="border-left:none;border-right:none;">Mat. from</span>
            <?php Yii::import('ext.CJuiDateTimePicker.CJuiDateTimePicker'); ?>
            <?php
            $this->widget('CJuiDateTimePicker', [
                'model' => $matDate,
                'attribute' => 'maturedFrom',
                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                'options' => [
                    'dateFormat' => Constants::DATE_DISPLAY_FORMAT,
                ], // jquery plugin options
                'language' => '',
                'htmlOptions' => [
                    'class' => 'form-control',
                    'placeholder' => 'E.g. "' . date('d M Y', time() - (30 * 24 * 60 * 60)) . '"',
                    'z-index' => '1000',
                    'style' => 'width:130px',
                ]
            ]);
            ?>
            <span class="input-group-addon" style="border-left:none;border-right:none;">Mat. by</span>
            <?php
            $this->widget('CJuiDateTimePicker', [
                'model' => $matDate,
                'attribute' => 'maturedBy',
                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                'options' => [
                    'dateFormat' => Constants::DATE_DISPLAY_FORMAT,
                ], // jquery plugin options
                'language' => '',
                'htmlOptions' => [
                    'class' => 'form-control',
                    'placeholder' => 'E.g. "' . date('d M Y', time() - (30 * 24 * 60 * 60)) . '"',
                    'z-index' => '1000',
                    'style' => 'width:130px',
                ]
            ]);
            ?>
          </div>
        </div>
        <div style="width:12%;float:left;margin-right:7px">
          <div class="input-group">
            <span class="input-group-addon">Age</span>
            <?php
            echo CHtml::dropDownList('age', $age, [0 => 'All', 1 => '60+Above', 2 => 'Below 60'], ['class' => 'form-control', 'style' => 'padding-left:5px']);
            ?>
          </div>
        </div>
        <div style="width:12.5%;float:left;margin-right:7px">
          <div class="input-group">
            <span class="input-group-addon">Gender</span>
            <?php
            echo CHtml::dropDownList('gender', $gender, [0 => 'All', 1 => 'Male', 2 => 'Female'], ['class' => 'form-control', 'style' => 'padding-left:5px']);
            ?>
          </div>
        </div>
        <?php echo CHtml::hiddenField('export', '0', ['id' => 'export']); ?>
        <?php echo CHtml::hiddenField('chart', '0', ['id' => 'chart']); ?>
        <?php echo CHtml::hiddenField('csv', '0', ['id' => 'csv']); ?>
        <?php echo CHtml::hiddenField('chartIslands', '0', ['id' => 'chartIslands']); ?>
        <button style="height: 24px" class="btn btn-xs btn-primary btn-default" 
                onClick="js:$('#export').val('0');
                    $('#chart').val('0');
                    $('#csv').val('0');
                    ">
          <icon class ="glyphicon glyphicon-filter"></icon> Filter</button>
        <button style="height: 24px" class="btn btn-xs btn-warning"
                onClick="js:$('#csv').val('1');
                    $('#export').val('0');
                    $('#chart').val('0');
                    turnBlockOn = false">
          CSV</button>
        <button style="height: 24px" class="btn btn-xs btn-warning"
                onClick="js:$('#export').val('1');
                    $('#chart').val('0');
                    $('#csv').val('0');
                    turnBlockOn = false">
          XL</button>
        <button style="height: 24px" class="btn btn-xs btn-danger"
                onClick="js:$('#export').val('0');
                    $('#chart').val('1')
                    $('#csv').val('0')
                    ">
          <icon class="glyphicon glyphicon-stats"></icon> Pie</button>
        <div class="form-group row">
          <div class="col-md-3">
            <div class="input-group">
              <span class="input-group-addon">Applied for Badhal Hajj?</span>
              <?php echo CHtml::dropDownList('badhalHajj', $badhalHajj, [0 => 'All', 1 => 'Yes', 2 => 'No'], ['class' => 'form-control']); ?>
            </div>
          </div>
          <div class="col-md-4">
            <div class="input-group">
              <span class="input-group-addon">Location</span>
              <?php
              if (!empty($island_id)) {
                $island = ZIslands::model()->findByPk($island_id);
                $atoll_id = $island->atoll_id;
              }
              if (!empty($atoll_id)) {
                $criteria = new CDbCriteria();
                $criteria->order = 'name_english asc';
                $islandList = [0=>'All islands'] + CHtml::listData
                  (ZIslands::model()
                  ->findAllByAttributes(['atoll_id' => $atoll_id, 'is_inhibited' => true], $criteria), 'island_id', 'name_english');
              } else {
                $islandList = [0=>'All islands'];
              }
              $atolls = CHtml::listData(ZAtolls::model()->findAll(),
              'atoll_id', 'abbreviation_english');
              array_unshift($atolls,'All atolls');

              echo CHtml::dropDownList('atoll_id', nz($atoll_id,0), $atolls,[
                  'onChange' => CHtml::ajax(['type' => 'GET',
                    'url' => CController::createUrl('helper/atollIslands'),
                    'data' => [
                      'selected_atoll' => 'js: $(this).val()',
                      'model' => '',
                      'attribute' => 'island_id',
                      'prompt' => 'All islands',
                      'style' => 'width: 65%; border-left: none'
                    ],
                    'replace' => '#island_id',
                  ]),
                  'class' => 'form-control',
                  'style' => 'width: 35%'
              ]);
              echo CHtml::dropDownList('island_id', (empty($island_id)?0:$island_id),
                $islandList, [
                  'class' => 'form-control',
                  'style' => 'width: 65%; border-left: none'
                ]); ?>
            </div>
          </div>
        </div>
      </div>
      <div class="panel-heading" style="display:<?= $chart ? 'none' : 'block' ?>">Columns</div>
      <div class="panel-body" style="display:<?= $chart ? 'none' : 'block' ?>">
        <div class="col-md-3">
          <div class="input-group">
            <span class="input-group-addon">
              <?php echo Chtml::checkBox('showAge', $showAge); ?>
            </span>
            <span class="form-control">Age</span>
          </div>
          <div class="input-group">
            <span class="input-group-addon">
              <?php echo Chtml::checkBox('showAtollIsland', $showAtollIsland); ?>
            </span>
            <span class="form-control">Atoll & Island</span>
          </div>
        </div>
        <div class="col-md-3">
          <div class="input-group">
            <span class="input-group-addon">
              <?php echo Chtml::checkBox('showFamily', $showFamily); ?>
            </span>
            <span class="form-control">Family</span>
          </div>
          <div class="input-group">
            <span class="input-group-addon">
              <?php echo Chtml::checkBox('showEmergencyContact', $showEmergencyContact); ?>
            </span>
            <span class="form-control">Emergency Contact</span>
          </div>
        </div>
        <div class="col-md-3">
          <div class="input-group">
            <span class="input-group-addon">
              <?php echo Chtml::checkBox('showHajj', $showHajj); ?>
            </span>
            <span class="form-control">Hajj Info</span>
          </div>
        </div>
        <div class="col-md-3">
          <div class="input-group">
            <span class="input-group-addon">
              <?php echo Chtml::checkBox('showBalance', $showBalance); ?>
            </span>
            <span class="form-control">Balance</span>
          </div>
          <div class="input-group">
            <span class="input-group-addon">
              <?php echo Chtml::checkBox('showBalanceDate', $showBalanceDate); ?>
            </span>
            <span class="form-control">Balance Date</span>
          </div>
        </div>
      </div>
    </div>

    <?php
    if ($chart) {
      if (!$chartIslands) {
        ?>
        <div class="panel panel-success">
          <div class="panel-heading small">Geographical Distribution
            <button style="height: 19px;padding-top:0px" class="btn btn-xs btn-warning pull-right"
                    onClick="js:$('#chartIslands').val('1');$('#chart').val('1');">
              Island Details</button>
          </div>
          <script type="text/javascript" src="https://www.google.com/jsapi"></script>
          <script type="text/javascript">
                      google.load("visualization", "1", {packages: ["corechart"]});
                      google.setOnLoadCallback(drawChart);
                      function drawChart() {
                        var data = google.visualization.arrayToDataTable(<?= $pieData; ?>);

                        var options = {
                          title: 'Atoll Distribution',
                          pieSliceText: 'value'
                        };

                        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
                        chart.draw(data, options);
                      }
          </script>
          <div id="piechart" style="width: 900px; height: 500px;"></div>
        </div>

        <?php
      } else {
        ?>
        <div class="panel panel-success">
          <div class="panel-heading small">Geographical Distribution
            <button style="height: 19px;padding-top:0px" class="btn btn-xs btn-warning pull-right"
                    onClick="js:$('#chartIslands').val('0');$('#chart').val('1');">
              Atolls Summary</button>
          </div>
          <div class="panel-body">
            <script type="text/javascript" src="https://www.google.com/jsapi"></script>
            <script type="text/javascript">
                      google.load("visualization", "1", {packages: ["corechart"]});
            </script>

            <?php
            //CVarDumper::dump($pieData,10,1);exit;
            $drawFunction = "function drawCharts() { ";

            foreach ($pieData as $k => $atollData) {
              $drawFunction .= 'drawChart' . $k . '(); ';
              $atollName = $atollData['AtollName'];
              $total = $atollData['count'];
              $chartData = [];
              foreach ($atollData['islands'] as $l => $island) {
                $chartData[] = [$l, $island];
              }
              array_unshift($chartData, ['Island', 'Count']);
              $chartData = json_encode($chartData);
              ?>
              <div class="col-md-6">
                <script type="text/javascript">
                  //              google.load("visualization", "1", {packages: ["corechart"]});
                  function drawChart<?= $k; ?>() {
                    var data<?= $k; ?> = google.visualization.arrayToDataTable(<?= $chartData; ?>);

                    var options<?= $k; ?> = {
                      title: '<?= $atollName . " (" . $total . ")"; ?>',
                      pieSliceText: 'value'
                    };

                    var chart<?= $k; ?> = new google.visualization.PieChart(document.getElementById('piechart_<?= $k; ?>'));
                    chart<?= $k; ?>.draw(data<?= $k; ?>, options<?= $k; ?>);
                  }
                </script>
                <div id="piechart_<?= $k; ?>" style="height: 350px;"></div>
              </div>
              <?php
            }
            $drawFunction .= '}';
            ?>
            <script type="text/javascript">
    <?php echo $drawFunction ?>
              google.setOnLoadCallback(drawCharts);
            </script>
          </div>
        </div>
        <?php
      }
    } else {
      ?>

      <div class="panel panel-success">
        <div class="panel-heading small">Members List</div>
        <div class="panel-body" style="margin-bottom:5px">
          <style>
            .grid-view table tbody tr
            {
              cursor: pointer;
            }
          </style>

          <?php
          $this->widget('zii.widgets.grid.CGridView', [
              'id' => 'members-grid',
              'selectableRows' => 1,
              'selectionChanged' => 'function(id){ location.href = "' . $this->createUrl('view') . '/id/"+$.fn.yiiGridView.getSelection(id);}',
              'dataProvider' => $dataProvider,
              'columns' => $columns,
          ]);
          ?>
        </div>
      </div>
      <?php
    }
    ?>
    <?php $this->endWidget(); ?>

  </div>
</div>
