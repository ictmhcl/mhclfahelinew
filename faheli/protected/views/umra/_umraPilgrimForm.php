<?php
/* @var $this UmraController */
/* @var $model UmraPilgrims */
/* @var $trip UmraTrips */
/* @var $form CActiveForm */

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl
  . "/js/jtk-4.2.1.js", CClientScript::POS_HEAD);

$model->scenario = $this->person->scenario = 'display';
?>

<div class="form small">

  <?php
  $form = $this->beginWidget('CActiveForm', [
    'id' => 'umra-pilgrims-form', 'enableAjaxValidation' => false,
    'htmlOptions' => [
      'class' => 'form-horizontal', 'enctype' => 'multipart/form-data'
    ]
  ]);
  ?>

  <?php echo $form->errorSummary([$model, $this->person], H::t('site',
    'inputErrors'), null, [
    'style' => 'margin: 5px;', 'class' => 'alert alert-danger'
  ]); ?>
  <div>
    <?php
    foreach ($this->person->attributeNames() as $attributeName)
      echo $form->hiddenField($this->person, $attributeName);
  $departureDate = null;
  if (!empty($model->umra_trip_id)) {
    $departureDate =
      empty($model->umraTrip->departure_date) ? $model->umraTrip->year . '/'
        . $model->umraTrip->month . '/01'
        : (new DateTime($model->umraTrip->departure_date))->format('Y/m/d');
  }
  echo CHtml::hiddenField('departureDate', $departureDate);
  ?>
  </div>
  <!-- <div id='mahramPanel' class="panel panel-success"
       style="display: none">
    <div class="panel-heading"><?=H::t('umra', 'chooseMahram')?></div>
    <div class="panel-body">
      <div class="form-group row">
        <?php echo $form->labelEx($model, 'mahram_id', ['class' => 'control-label col-md-2']); ?>
        <div class="col-md-3">
          <?= $form->textField($model, 'mahram_id', [
            'class' => 'form-control', 'style' => 'direction: ltr'
          ]);
          ?>
        </div>
      </div>
      <div class="form-group row">
        <div class="col-md-11 col-md-offset-1 "><?=H::t('umra',
            'mahramInfo')?></div>
      </div>
    </div>
  </div> -->
  <div class="panel panel-success">
    <div class="panel-heading"><?=H::t('umra', 'currentAddress')?></div>
    <div class="panel-body">
      <div class="form-group">
        <?php echo $form->labelEx($model, 'current_island_id', ['class' => 'control-label col-md-2']); ?>
        <div class="col-md-3">
          <?php
          if (!empty($model->current_island_id)) {
            $island = ZIslands::model()->findByPk($model->current_island_id);
            $atoll_id = $island->atoll_id;
            $criteria = new CDbCriteria();
            $criteria->order = H::tf('name_english asc');
            $islandList =
              CHtml::listData(ZIslands::model()->findAllByAttributes([
                'atoll_id' => $atoll_id, 'is_inhibited' => true
              ], $criteria), 'island_id', H::tf('name_dhivehi'));
          }
          else {
            $atoll_id = '';
            $islandList = [];
          }

          echo CHtml::dropDownList('atoll_id', $atoll_id, CHtml::listData(ZAtolls::model()
            ->findAll(), 'atoll_id', H::tf('name_dhivehi')), [
              'prompt' => H::t('site','selectAtoll'), 'onChange' => CHtml::ajax([
                'type' => 'GET',
                'url' => CController::createUrl('helper/atollIslands'),
                'data' => [
                  'selected_atoll' => 'js: $(this).val()',
                  'model' => get_class($model),
                  'attribute' => 'current_island_id',
                  'dhivehi' => Yii::app()->language == 'dv'?1:0,
                  'prompt' => H::t('site','selectIsland'),
                ], 'replace' => '#'
                  . Helpers::resolveID($model, 'current_island_id'),
              ]), 'class' => 'form-control', 'id' => 'curAtollId','style' =>
              'margin-bottom: 5px'
            ]);
          ?>
        </div>
        <div class="col-md-3">
          <?php echo CHtml::activeDropDownList($model, 'current_island_id', $islandList, [
            'class' => 'form-control',
            (!empty($atoll_id) ? '' : 'prompt') => H::t('site', 'selectAtoll')
          ]); ?>
        </div>
      </div>
      <div class="form-group">
        <?php echo $form->labelEx($model, 'current_address_english', [
          'class' => 'col-md-2 control-label'
        ]); ?>
        <div class="col-md-4">
          <?php echo $form->textField($model, 'current_address_english', [
            'size' => 30, 'maxlength' => 255, 'class' => 'form-control',
            'style' => 'direction: ltr'
          ]); ?>
        </div>
        <?php echo $form->labelEx($model, 'current_address_dhivehi', [
          'class' => 'control-label col-md-2']); ?>
        <div class="col-md-4">
          <?php echo $form->textField($model, 'current_address_dhivehi', [
            'class' => 'thaanaKeyboardInput form-control', 'size' => 30,
            'maxlength' => 255
          ]); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="panel panel-success">
    <div class="panel-heading"><?=H::t('hajj','contactInfo')?></div>
    <div class="panel-body">
      <div class="form-group">
        <?php echo $form->labelEx($model, 'phone_number', ['class' => 'col-md-2 control-label']); ?>
        <div class="col-md-3">
          <?php echo $form->textField($model, 'phone_number', [
            'size' => 30, 'maxlength' => 255, 'class' => 'form-control'
          ]); ?>
        </div>
        <?php echo $form->labelEx($model, 'email_address', ['class' => 'control-label col-md-2']); ?>
        <div class="col-md-3">
          <?php echo $form->textField($model, 'email_address', [
            'class' => 'form-control', 'size' => 30, 'maxlength' => 255,
            'style' => 'direction: ltr'
          ]); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="panel panel-success">
    <div class="panel-heading"><?=H::t('umra', 'groupInfo')?></div>
    <div class="panel-body">
      <div class="form-group">
        <?php echo $form->labelEx($model, 'group_name', [
          'class' => 'col-md-3 control-label'
        ]); ?>
        <div class="col-md-3">
          <?php

          echo $form->textField($model, 'group_name', [
              'class' => 'form-control'
            ]);

          ?>
        </div>
      </div>
      <div class="form-group row">
        <div class="col-md-11 col-md-offset-1">
          <?=H::t('hajj','groupLeaderNote')?>
        </div>
      </div>

    </div>
  </div>
  
  
  <div class="panel panel-success">
    <div class="panel-heading"><?=H::t('umra', 'emergencyContact')?></div>
    <div class="panel-body">
      
    <div class="form-group">
        
        <?php echo $form->labelEx($model,
          'ec_full_name',
          ['class' => 'col-md-2 control-label']); ?>
        <div class="col-md-3">
          <?php echo $form->textField($model,
            H::tf('emergency_contact_full_name_dhivehi'),
            ['size' => 30, 'maxlength' => 255,
             'class' => 'form-control' . (Yii::app()->language == 'dv'?
                 ' thaanaKeyboardInput':''),
             'style' => 'margin-bottom:5px',
             ]); ?>
        </div>

        <?php echo $form->labelEx($model, 'ec_phone_number',
          ['class' => 'control-label col-md-2']); ?>
        <div class="col-md-2">
          <?php echo $form->textField($model, 'ec_phone_number',
            ['class' => 'form-control', 'size' => 30]); ?>
        </div>
        
      </div>

      <div class="form-group">
        <?php echo $form->labelEx($model, 'id_no',
          ['class' => 'control-label col-md-2']); ?>
        <div class="col-md-2">
          <?php echo $form->textField($model, 'ec_id_no',
            ['class' => 'form-control', 'size' => 30]); ?>
        </div>
        
      </div>
      <div class="form-group">
        <?php echo $form->labelEx($model, 'ec_address_english', [
          'class' => 'col-md-2 control-label'
        ]); ?>
        <div class="col-md-4">
          <?php echo $form->textField($model, 'ec_address_english', [
            'size' => 30, 'maxlength' => 255, 'class' => 'form-control',
            'style' => 'direction: ltr'
          ]); ?>
        </div>
        <?php echo $form->labelEx($model, 'ec_address_dhivehi', [
          'class' => 'control-label col-md-2']); ?>
        <div class="col-md-4">
          <?php echo $form->textField($model, 'ec_address_dhivehi', [
            'class' => 'thaanaKeyboardInput form-control', 'size' => 30,
            'maxlength' => 255
          ]); ?>
        </div>
      </div>

     
      
    </div>
  </div>


  <?php echo (!$model->isNewRecord) ? $form->hiddenField($model, 'id') : ''; ?>

  <div class="panel panel-success">
    <div class="panel-footer">
      <div class="form-group">
        <div class="col-md-10 col-md-offset-2">
          <button class="btn btn-sm btn-primary">
            <icon class="glyphicon glyphicon-save"></icon>
            <?=H::t('site','submit')?>
          </button>
        </div>
      </div>
    </div>
  </div>
  <?php $this->endWidget(); ?>

  <script>
    function copyPermAddress() {
      permAddressEnglish = $('#Persons_perm_address_english');
      permAddressDhivehi = $('#Persons_perm_address_dhivehi');
      curAddressEnglish = $('#UmraPilgrims_current_address_english');
      curAddressDhivehi = $('#UmraPilgrims_current_address_dhivehi');

      curAddressEnglish.val(permAddressEnglish.val());
      curAddressDhivehi.val(permAddressDhivehi.val());

      $('#curAtollId').val($('#permAtollId').val()).trigger('change');

      setTimeout(function () {
        $('#UmraPilgrims_current_island_id').val($('#Persons_perm_address_island_id').val());
      }, 1000);
      $('#UmraPilgrims_current_address_english').val($('#Persons_perm_address_english').val());
      $('#UmraPilgrims_current_address_dhivehi').val($('#Persons_perm_address_dhivehi').val());

    }
    function getPerson() {
      url = '<?php echo $this->createUrl('createUmraPilgrim',
      ['umraTripId'=>$model->umra_trip_id]); ?>&get_id=' + $('#get_id').val();
      location.href = url;
    }

    function mahramCheck() {
      if (!mahramRequired()) {
        $("#UmraPilgrims_mahram_id").val("");
        $("#mahramPanel").slideUp();
        $("#mahram_document_file").slideUp();
      } else {
        $("#mahramPanel").slideDown();
        $("#mahram_document_file").slideDown();
      }
    }

    $('#Persons_d_o_b, #Persons_gender_id').on('change', function () {
      mahramCheck();
    });

    function mahramRequired() {
      var dob = $('#Persons_d_o_b').val();
      var ageAtDate = $('#departureDate').val();
      if ($('#Persons_gender_id').val() == '2' &&
        gregorianAge(new Date(dob), new Date(ageAtDate == "" ? null : ageAtDate)) <
        <?=Helpers::config('noMahramAge')?>)
        return true;
      return false;
    }

    /**
     * Calculates human age in years given a birth day. Optionally ageAtDate
     * can be provided to calculate age at a specific date
     *
     * @param string|Date Object birthDate
     * @param string|Date Object ageAtDate optional
     * @returns integer Age between birthday and a given date or today
     */
    function gregorianAge(birthDate, ageAtDate) {
      // convert birthDate to date object if already not
      if (Object.prototype.toString.call(birthDate) !== '[object Date]')
        birthDate = new Date(birthDate);

      // use today's date if ageAtDate is not provided
      if (typeof ageAtDate == "undefined" || ageAtDate == "")
        ageAtDate = new Date();

      // convert ageAtDate to date object if already not
      else if (Object.prototype.toString.call(ageAtDate) !== '[object Date]')
        ageAtDate = new Date(ageAtDate);

      // if conversion to date object fails return null
      if (ageAtDate == null || birthDate == null)
        return null;


      var _m = ageAtDate.getMonth() - birthDate.getMonth();

      // answer: ageAt year minus birth year less one (1) if month and day of
      // ageAt year is before month and day of birth year
      return (ageAtDate.getFullYear()) - birthDate.getFullYear()
        - ((_m < 0 || (_m === 0 && ageAtDate.getDate() < birthDate.getDate()))
          ? 1 : 0)
    }


    $(document).ready(function () {
      mahramCheck();
    })
  </script>
  <?php include_once(Yii::getPathOfAlias('webroot')
    . "/js/registration-helper.php"); ?>

</div><!-- form -->