<?php
/* @var $this RegistrationController */
/* @var $onlineForm OnlineRegistrationForm */
/* @var $form CActiveForm */

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl
  . "/js/jtk-4.2.1.js", CClientScript::POS_HEAD);
?>

<h3><span style="color:green"><?=H::t('site','registerOnPortal')?></span></h3>

<div class="tabs">
  <ul class="nav nav-tabs small">
  </ul>
</div>

<div class="tab-content">

  <div class="form small">

    <?php
    $form = $this->beginWidget('CActiveForm', [
      'id' => 'online-registration-form', 'enableAjaxValidation' => false,
      'htmlOptions' => [
        'class' => 'form-horizontal', 'enctype' => 'multipart/form-data'
      ]
    ]);
    ?>

    <?php
    echo $form->errorSummary($onlineForm,H::t('site','inputErrors'), null, [
      'style' => 'margin: 5px;', 'class' => 'alert alert-danger'
    ]);

    $docsArray = ['id_card_copy'];

    ?>
    <div class="panel panel-success">
      <div class="panel-heading"><?=H::t('site', 'identityInfo')?></div>
      <div class="panel-body">
        <div class="form-group">
          <?php echo $form->labelEx($onlineForm, 'full_name_english', ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-4">
            <?php echo $form->textField($onlineForm, 'full_name_english', [
              'size' => 30, 'maxlength' => 255, 'class' => 'form-control',
              'id' => 'full_name_english', 'style' => 'margin-bottom: 5px;
              direction: ltr'
            ]); ?>
          </div>
          <?php echo $form->labelEx($onlineForm, 'full_name_dhivehi', ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-4">
            <?php echo $form->textField($onlineForm, 'full_name_dhivehi', [
              'class' => 'thaanaKeyboardInput form-control',
              'id' => 'full_name_dhivehi', 'size' => 30, 'maxlength' => 255,
              'style' => 'margin-bottom: 5px; direction: rtl'
            ]); ?>
          </div>
        </div>
        <div class="form-group">
          <?php echo $form->labelEx($onlineForm, 'id_no', ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-2">
            <?php echo $form->textField($onlineForm, 'id_no', [
              'size' => 7, 'maxlength' => 7, 'class' => 'form-control',
              'style' => 'direction: ltr'
            ]); ?>
          </div>
          <?php echo $form->labelEx($onlineForm, 'd_o_b',
            ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-2">
            <?php
            $form->widget('zii.widgets.jui.CJuiDatePicker', [
              'model' => $onlineForm, 'attribute' => 'd_o_b', 'options' => [
                'changeMonth' => true, 'changeYear' => true,
                'yearRange' => '-100:+0', 'showAnim' => 'slide',
                'maxDate' => '0',
                'dateFormat' => Constants::DATE_DISPLAY_FORMAT,
              ], 'htmlOptions' => [
                'readonly' => 'readonly',
                'language' => 'mv',
                'class' => 'form-control',
                'style' => 'direction:ltr; background-color: white'
              ],
            ]);
            ?>
          </div>
          <?php echo $form->labelEx($onlineForm, 'gender_id', ['class' => 'col-md-1 control-label']); ?>
          <div class="col-md-2">
            <?php
            echo $form->dropDownList($onlineForm, 'gender_id', CHtml::listData(ZGender::model()
              ->findAll(), 'gender_id', H::tf('name_dhivehi')), [
              'prompt' => '', 'class' => 'form-control'
            ]);
            ?>
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel-success">
      <div class="panel-heading"><?=H::t('site', 'addressContactInfo')?></div>
      <div class="panel-body">
        <div class="form-group row">
          <?php echo $form->labelEx($onlineForm, 'country_id', ['class' => 'control-label col-md-2']); ?>
          <div class="col-md-2">
            <?php
            $countryNameField = H::tf('name_dhivehi asc', $delete = '_dhivehi');
            $countryList =
              CHtml::listData(ZCountry::model()->findAll([
                'order' => H::tf('name_dhivehi asc',$delete = '_dhivehi')]),
                'id', H::tf('name_dhivehi', $delete = '_dhivehi'));
            echo CHtml::activeDropDownList($onlineForm, 'country_id', $countryList, [
              'class' => 'form-control',
              (!empty($onlineForm->country_id) ? '' : 'prompt') => '',
              'onChange' => 'javascript:{
                  if ($(this).val() != ' . Constants::MALDIVES_COUNTRY_ID . ') {
                    $("#permAtollId").val("");
                    $("#permAtollId").trigger("change");
                    $(".permIsland").hide();
                  } else
                    $(".permIsland").show();
                }', 'style' => 'margin-bottom: 5px'
            ]);
            ?>
          </div>
          <?php
          $showPermIsland = ($onlineForm->country_id !=
          Constants::MALDIVES_COUNTRY_ID ? "none" : "block");
          ?>
          <div class="permIsland"
               style="display:<?=$showPermIsland?>">
            <?php echo $form->labelEx($onlineForm, 'perm_address_island_id', [
              'class' => 'control-label col-md-1'
            ]); ?>

            <div class="col-md-3">
              <?php
              if (!empty($onlineForm->perm_address_island_id)) {
                $island =
                  ZIslands::model()->findByPk($onlineForm->perm_address_island_id);
                $atoll_id = $island->atoll_id;
                $criteria = new CDbCriteria();
                $criteria->order = H::tf('name_dhivehi asc');
                $islandList = CHtml::listData(ZIslands::model()
                  ->findAllByAttributes([
                    'atoll_id' => $atoll_id, 'is_inhibited' => true
                  ], $criteria), 'island_id', H::tf('name_dhivehi'));
              } else {
                $atoll_id = '';
                $islandList = [];
              }

              echo CHtml::dropDownList('atoll_id', $atoll_id, CHtml::listData(ZAtolls::model()
                ->findAll(), 'atoll_id', H::tf('name_dhivehi')), [
                  'prompt' => H::t('site','selectAtoll'), 'onChange' =>
                  CHtml::ajax([
                    'type' => 'GET',
                    'url' => Yii::app()->createUrl('helper/atollIslands'),
                    'data' => [
                      'selected_atoll' => 'js: $(this).val()',
                      'model' => get_class($onlineForm),
                      'attribute' => 'perm_address_island_id',
                      'prompt' => H::t('site','selectIsland'),'style'=>'',
                    ], 'replace' => '#' .
                      Helpers::resolveID($onlineForm, 'perm_address_island_id'),
                  ]), 'class' => 'form-control', 'id' => 'permAtollId',
                  'style' => 'margin-bottom: 5px'
                ]);
              ?>
            </div>
            <div class="col-md-3">
              <?php echo CHtml::activeDropDownList($onlineForm, 'perm_address_island_id', $islandList, [
                'class' => 'form-control',
                (!empty($atoll_id) ? '' : 'prompt') => ''
              ]); ?>
            </div>
          </div>
        </div>
        <div class="form-group">
          <?php echo $form->labelEx($onlineForm, 'perm_address_english', ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-4">
            <?php echo $form->textField($onlineForm, 'perm_address_english', [
              'size' => 30, 'maxlength' => 255, 'class' => 'form-control',
              'style' => 'margin-bottom: 5px; direction: ltr'
            ]); ?>
          </div>
          <?php echo $form->labelEx($onlineForm, 'perm_address_dhivehi', ['class' => 'control-label col-md-2']); ?>
          <div class="col-md-4">
            <?php echo $form->textField($onlineForm, 'perm_address_dhivehi', [
              'class' => 'thaanaKeyboardInput form-control', 'size' => 30,
              'maxlength' => 255, 'style' => 'margin-bottom: 5px; direction: rtl',


            ]); ?>
          </div>
        </div>
        <div class="form-group">
          <?php echo $form->labelEx($onlineForm, 'phone_number_1', ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-3">
            <?php echo $form->textField($onlineForm, 'phone_number_1', [
              'size' => 30, 'maxlength' => 15, 'class' => 'form-control',
              'style' => 'direction: ltr', 'placeholder' => ''
            ]); ?>
          </div>
          <?php echo $form->labelEx($onlineForm, 'email_address', ['class' => 'control-label col-md-2']); ?>
          <div class="col-md-3">
            <?php echo $form->textField($onlineForm, 'email_address', [
              'class' => 'form-control', 'size' => 30, 'maxlength' => 255,
              'style' => 'direction: ltr'
            ]); ?>
          </div>
        </div>

      </div>
    </div>
    <div class="panel panel-success">
      <div class="panel-heading"><?=H::t('site', 'documents')?></div>
      <div class="panel-body">

        <div class="form-group">
          <?php
          foreach ($docsArray as $doc) {
          $labelSize = $doc == "id_card_copy" ? 'col-md-2' : 'col-md-2';
          $viewSize = $doc == "id_card_copy" ? 'col-md-2' : 'col-md-1';
          echo $form->labelEx($onlineForm, $doc, [
              'class' => $labelSize . ' control-label'
            ]); ?>
          <script>turnBlockOn = false;</script>
          <div class="col-md-3">
            <div class="input-group">
                  <span class="input-group-btn">
                    <span class="btn btn-info btn-xs btn-file"
                          style="height: 34px; font-size: 16px"
                          onClick="js:turnBlockOn = false;">
                      <?=H::t('site','fileSelect')?> <?php echo $form->fileField($onlineForm, $doc)
                      ; ?>
                    </span>
                  </span>
              <input type="text" class="form-control" readonly="">
            </div>
          </div>
          <?php
          }
          ?>
        </div>
      </div>
    </div>
    <?php echo (!$onlineForm->isNewRecord) ? $form->hiddenField($onlineForm, 'id') : ''; ?>

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
    <?php include_once(Yii::getPathOfAlias('webroot') .
      "/js/registration-helper.php"); ?>

    <?php $this->endWidget(); ?>

  </div><!-- form -->
</div>
