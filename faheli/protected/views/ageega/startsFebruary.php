<h3><?=$this->person->full_name_dhivehi?>، އަޤީޤާ އަށް ރަޖިސްޓްރީ ކުރުން</h3>

<div class="tabs">
  <ul class="nav nav-tabs small"></ul>
</div>

<div class="tab-content">

  <div class="form small">

    <?php
    $form = $this->beginWidget('CActiveForm', [
      'id' => 'ageega-registration-form', 'enableAjaxValidation' => false,
      'htmlOptions' => [
        'class' => 'form-horizontal', 'enctype' => 'multipart/form-data'
      ]
    ]);
    ?>

    <div class="panel panel-success">
      <div class="panel-heading">އަޤީޤއަށް ރަޖިސްޓްރީ ކުރުމާއި ފައިސާ
        ދެއްކެވުން</div>
      <div class="panel-body">
        <div class="col-md-4">
          <span class="btn btn-success btn-sm onlinPayBtn">
<!--          <span class="btn btn-success btn-sm onlinPayBtn"-->
            <!--                onclick="javascript:$('.onlinePay').slideToggle()">-->
              އޮންލައިންކޮށް އަޤީޤާއަށް ފައިސާ ޖަމާ ކުރުން (ފެށެނީ ފެބްރުވަރީ
            2017 ގައި)
          </span>
        </div>

      </div>
    </div>
    <?php $this->endWidget() ?>
  </div>
</div>