<?php
/* @var $this SiteController */
?>
<h3>Error Details</h3>

<ul class="nav nav-tabs small">
</ul>

<div class="tab-content">
  <div class="form ">
    <div class="panel panel-success">
      <div class="row">
        <div class="col-md-12">

          <?php
          $this->widget('zii.widgets.CDetailView', array(
              'data' => $data,
              'id' => 'candidate-detals',
              'attributes' =>
              [
                  'id', 'datetime',
                  [
                      'label' => 'Url',
                      'value' => $data->url,
                      'template' => "<tr class=\"{class}\">"
                      . "<th>{label}</th>"
                      . "<td style='max-width:300px;word-wrap:break-word'>{value}</td></tr>\n",
                  ], 'code', 'type', 'message', 'file', 'line',
                  [
                      'label' => 'Trace',
                      'type' => 'raw',
                      'value' => CHtml::tag('pre', [], $data->trace)
                  ],
                  'user_id',
                  ['label' => 'User Name',
                      'value' => empty($data->user_id) ? '' : Persons::model()->findByPk($data->user_id)->full_name_english]
              ],
          ));
          ?>                
        </div>
      </div>
    </div>
  </div>
</div>
