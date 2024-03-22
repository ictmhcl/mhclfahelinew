<?php
/* @var $this RegistrationController */
/* @var $model RegistrationHelper */
/* @var $form CActiveForm */

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . "/js/jtk-4.2.1.js", CClientScript::POS_HEAD);
?>

<div class="form small">

  <?php
  $form = $this->beginWidget('CActiveForm', [
    'id' => 'application-forms-form',
    // Please note: When you enable ajax validation, make sure the corresponding
    // controller action is handling ajax validation correctly.
    // There is a call to performAjaxValidation() commented in generated controller code.
    // See class documentation of CActiveForm for details on this.
    'enableAjaxValidation' => false,
//      'action' => $this->createUrl('create', array('tab' => $tab)),
    'htmlOptions' => array_merge(['class' => 'form-horizontal'],
      ($tab == 3 ? ['enctype' => 'multipart/form-data'] : []))
  ]);
  ?>

  <div class="panel panel-default">
    <div class="panel-body">
      <div class="col-md-6"
           style="padding-right:0px; padding-left:0px;padding-bottom:5px">
        <?php
        if (!empty($model->applicationFormVerification)) {
          echo '<div style="color: red; width: 400px;"><strong>'
            . $model->applicationFormVerification->{$tabs[$tab] . '_comment'}
            . '</strong></div>';
          $verifier =
            (!empty($model->applicationFormVerification->operationLog->modifiedUser) ?
              $model->applicationFormVerification->operationLog->modifiedUser
                ->person->full_name_english :
              $model->applicationFormVerification->operationLog->createdUser
                ->person->full_name_english);
          echo '<br><div style="color: red; font-style:italic; width: 400px;">'
            . 'Verifier: ' . $verifier . '</div>';
          $registerer = (!empty($model->operationLog->modifiedUser) ?
            $model->operationLog->modifiedUser->person->full_name_english :
            $model->operationLog->createdUser->person->full_name_english);
          echo '<div style="color: red; font-style:italic; width: 400px;">'
            . 'Record entered/last updated By: ' . $registerer . '</div>';
        } else {
          echo '<span class="note">Fields with <span class="required">*</span>'
            . ' are required.</span>';
          if (!$model->isNewRecord) {
            $registerer = (!empty($model->operationLog->modifiedUser) ?
              $model->operationLog->modifiedUser->person->full_name_english :
              $model->operationLog->createdUser->person->full_name_english);
            echo '<div style="color: red; font-style:italic; width: 400px;">'
              . 'Record entered/last updated By: ' . $registerer . '</div>';
          }
        }
        ?>
      </div>
      <div class="col-md-6"
           style="padding-right:0px; padding-left:0px;padding-bottom:5px">
        <?php if (!$model->isNewRecord) { ?>
          <div class="pull-right" style="text-align: right">
            <div class="badge badge-important">
              Status: <?php echo $model->state->name_english; ?></div>
            <br>

            <strong>Applicant Details</strong><br>
            <?php echo $model->full_name_english . ', ' . $model->id_pp_no; ?>
            <br>
            <?php echo !empty($model->perm_address_island_id) ?
              ($model->perm_address_english . ", "
                . $model->permAddressIsland->atoll->abbreviation_english
                . ". " . $model->permAddressIsland->name_english) :
              $model->perm_address_english; ?>

            <br>
          </div>
        <?php } ?>
      </div>
      <?php if ($tab == 1) {
        ?>
        <div class="col-md-3 pull-left">
          <span class="input-group">
            <input class="form-control" id="get_id"><span
              class="input-group-addon btn btn-sm btn-primaryy"
              onClick="getId()">Get from ID</span>
          </span>
        </div>
      <?php
      }
      ?>
    </div>
  </div>
  <?php echo $form->errorSummary($model, null, null,
    ['style' => 'margin: 5px;', 'class' => 'alert alert-danger']); ?>

  <?php
  switch ($tab) {
    case 1: // Applicant

      if (!is_null($getPerson)) {
        $model->full_name_english = $getPerson->full_name_english;
        $model->full_name_dhivehi = $getPerson->full_name_dhivehi;
        $model->d_o_b = $getPerson->d_o_b;
        $model->gender_id = $getPerson->gender_id;
        $model->id_pp_no = $getPerson->id_no;
        $model->perm_address_island_id = $getPerson->perm_address_island_id;
        $model->perm_address_english = $getPerson->perm_address_english;
        $model->perm_address_dhivehi = $getPerson->perm_address_dhivehi;
      }
      ?>
      <div class="panel panel-success">
        <div class="panel-heading">Personal Details</div>
        <div class="panel-body">
          <div class="form-group">
            <?php echo $form->labelEx($model, 'full_name_english',
              ['class' => 'col-md-2 control-label']); ?>
            <div class="col-md-4">
              <?php echo $form->textField($model, 'full_name_english',
                ['size' => 30, 'maxlength' => 255, 'class' => 'form-control']); ?>
            </div>
            <div class="col-md-4">
              <?php echo $form->textField($model, 'full_name_dhivehi',
                ['class' => 'thaanaKeyboardInput form-control', 'size' => 30,
                  'maxlength' => 255]); ?>
            </div>
            <?php echo $form->labelEx($model, 'full_name_dhivehi',
              ['class' => 'col-md-2 control-label pull-left thaanaLabel']); ?>
          </div>
          <div class="form-group">
            <?php echo $form->labelEx($model, 'id_pp_no',
              ['class' => 'col-md-2 control-label']); ?>
            <div class="col-md-2">
              <?php echo $form->textField($model, 'id_pp_no',
                ['size' => 30, 'maxlength' => 30, 'class' => 'form-control']); ?>
            </div>
            <?php echo $form->labelEx($model, 'd_o_b',
              ['class' => 'col-md-1 control-label']); ?>
            <div class="col-md-2">
              <?php
              $form->widget('zii.widgets.jui.CJuiDatePicker', [
                'model' => $model,
                'attribute' => 'd_o_b',
                'options' => [
                  'changeMonth' => true,
                  'changeYear' => true,
                  'yearRange' => '-100:+0',
                  'showAnim' => 'slide',
                  'maxDate' => '0',
                  'dateFormat' => Constants::DATE_DISPLAY_FORMAT,
                ],
                'htmlOptions' => [
//                      'readonly' => 'readonly',
                  'class' => 'form-control',
                  'placeholder' => 'E.g. "17 Feb 1954"'
                ],
              ]);
              ?>
            </div>
            <?php echo $form->labelEx($model, 'gender_id',
              ['class' => 'col-md-1 control-label']); ?>
            <div class="col-md-2">
              <?php
              echo $form->dropDownList($model, 'gender_id',
                CHtml::listData(ZGender::model()->findAll(),
                  'gender_id', 'name_english'),
                ['prompt' => '', 'class' => 'form-control']);
              ?>
            </div>
          </div>
        </div>
      </div>
      <div class="panel panel-success">
        <div class="panel-heading">Permanent Address</div>
        <div class="panel-body">
          <div class="form-group row">
            <?php echo $form->labelEx($model, 'country_id',
              ['class' => 'control-label col-md-2']); ?>
            <div class="col-md-3">
              <?php
              $countryList = CHtml::listData(ZCountry::model()->findAll(), 'id', 'name');
              echo CHtml::activeDropDownList($model, 'country_id', $countryList, [
                'class' => 'form-control',
                (!empty($model->country_id) ? '' : 'prompt') => '<- Select Atoll first',
                'onChange' => 'javascript:{
                  if ($(this).val() != ' . Constants::MALDIVES_COUNTRY_ID . ') {
                    $("#permAtollId").val("");
                    $("#permAtollId").trigger("change");
                    $(".permIsland").slideUp();
                  } else
                    $(".permIsland").slideDown();
                }',
              ]);
              ?>
            </div>
          </div>
          <?php
          $showPermIsland = $model->country_id !=
          Constants::MALDIVES_COUNTRY_ID ? "none" : "block";
          ?>
          <div class="form-group permIsland"
               style="display:<?= $showPermIsland ?>">
            <?php echo $form->labelEx($model, 'perm_island_id', [
              'class' => 'control-label col-md-2'
            ]); ?>

            <div class="col-md-3">
              <?php
              if (!empty($model->perm_island_id)) {
                $island = ZIslands::model()->findByPk($model->perm_island_id);
                $atoll_id = $island->atoll_id;
                $criteria = new CDbCriteria();
                $criteria->order = 'name_english asc';
                $islandList =
                  CHtml::listData(ZIslands::model()->findAllByAttributes([
                    'atoll_id' => $atoll_id, 'is_inhibited' => true
                  ], $criteria), 'island_id', 'name_english');
              } else {
                $atoll_id = '';
                $islandList = [];
              }

              echo CHtml::dropDownList('atoll_id', $atoll_id,
                CHtml::listData(ZAtolls::model()->findAll(), 'atoll_id', 'name_english'), [
                  'prompt' => 'Atoll',
                  'onChange' => CHtml::ajax(['type' => 'GET',
                    'url' => CController::createUrl('helper/atollIslands'),
                    'data' => [
                      'selected_atoll' => 'js: $(this).val()',
                      'model' => get_class($model),
                      'attribute' => 'perm_island_id'
                    ],
                    'replace' => '#' . Helpers::resolveID($model, 'perm_island_id'),
                  ]),
                  'class' => 'form-control',
                  'id' => 'permAtollId'
                ]
              );
              ?>
            </div>
            <div class="col-md-3">
              <?php echo CHtml::activeDropDownList($model, 'perm_island_id',
                $islandList, ['class' => 'form-control', (!empty($atoll_id) ?
                  '' : 'prompt') => '<- Select Atoll first']); ?>
            </div>
          </div>
          <div class="form-group">
            <?php echo $form->labelEx($model, 'perm_address_english',
              ['class' => 'col-md-2 control-label']); ?>
            <div class="col-md-4">
              <?php echo $form->textField($model, 'perm_address_english',
                ['size' => 30, 'maxlength' => 255, 'class' => 'form-control']); ?>
            </div>
            <div class="col-md-4">
              <?php echo $form->textField($model, 'perm_address_dhivehi',
                ['class' => 'thaanaKeyboardInput form-control', 'size' => 30,
                  'maxlength' => 255]); ?>
            </div>
            <?php echo $form->labelEx($model, 'perm_address_dhivehi',
              ['class' => 'control-label thaanaLabel col-md-2']); ?>
          </div>
        </div>
      </div>
      <div class="panel panel-success">
        <div class="panel-heading">Current Address<span
            style="cursor:pointer;font-weight: normal" class="pull-right badge"
            onClick="copyPermAddress()">Copy</span></div>
        <div class="panel-body">
          <div class="form-group">
            <?php echo $form->labelEx($model, 'current_island_id',
              ['class' => 'control-label col-md-2']); ?>
            <div class="col-md-3">
              <?php
              if (!empty($model->current_island_id)) {
                $island = ZIslands::model()->findByPk($model->current_island_id);
                $atoll_id = $island->atoll_id;
                $criteria = new CDbCriteria();
                $criteria->order = 'name_english asc';
                $islandList = CHtml::listData(
                  ZIslands::model()->findAllByAttributes([
                    'atoll_id' => $atoll_id, 'is_inhibited' => true
                  ], $criteria), 'island_id', 'name_english');
              } else {
                $atoll_id = '';
                $islandList = [];
              }

              echo CHtml::dropDownList('atoll_id', $atoll_id,
                CHtml::listData(ZAtolls::model()->findAll(), 'atoll_id', 'name_english'), [
                  'prompt' => 'Atoll',
                  'onChange' => CHtml::ajax(['type' => 'GET',
                    'url' => CController::createUrl('helper/atollIslands'),
                    'data' => [
                      'selected_atoll' => 'js: $(this).val()',
                      'model' => get_class($model),
                      'attribute' => 'current_island_id'
                    ],
                    'replace' => '#' . Helpers::resolveID($model, 'current_island_id'),
                  ]),
                  'class' => 'form-control',
                  'id' => 'curAtollId']
              );
              ?>
            </div>
            <div class="col-md-3">
              <?php echo CHtml::activeDropDownList($model, 'current_island_id',
                $islandList, ['class' => 'form-control', (!empty($atoll_id) ?
                  '' : 'prompt') => '<- Select Atoll first']); ?>
            </div>
          </div>
          <div class="form-group">
            <?php echo $form->labelEx($model, 'current_address_english',
              ['class' => 'col-md-2 control-label']); ?>
            <div class="col-md-4">
              <?php echo $form->textField($model, 'current_address_english',
                ['size' => 30, 'maxlength' => 255, 'class' => 'form-control']); ?>
            </div>
            <div class="col-md-4">
              <?php echo $form->textField($model, 'current_address_dhivehi',
                ['class' => 'thaanaKeyboardInput form-control', 'size' => 30, 'maxlength' => 255]); ?>
            </div>
            <?php echo $form->labelEx($model, 'current_address_dhivehi',
              ['class' => 'thaanaLabel control-label col-md-2']); ?>
          </div>
        </div>
      </div>
      <div class="panel panel-success">
        <div class="panel-heading">Contact Details</div>
        <div class="panel-body">
          <div class="form-group">
            <?php echo $form->labelEx($model, 'phone_number',
              ['class' => 'col-md-2 control-label']); ?>
            <div class="col-md-3">
              <?php echo $form->textField($model, 'phone_number',
                ['size' => 30, 'maxlength' => 255, 'class' => 'form-control']); ?>
            </div>
            <?php echo $form->labelEx($model, 'email_address',
              ['class' => 'control-label col-md-2']); ?>
            <div class="col-md-3">
              <?php echo $form->textField($model, 'email_address',
                ['class' => 'form-control', 'size' => 30, 'maxlength' => 255]); ?>
            </div>
          </div>
        </div>
      </div>
      <?php
      break;
    case 2: // MoreInfo
      ?>
      <div class="panel panel-success">
        <div class="panel-heading">Family</div>
        <div class="panel-body">
          <div class="form-group">
            <?php echo $form->labelEx($model, 'marital_status_id',
              ['class' => 'col-md-2 control-label']); ?>
            <div class="col-md-3">
              <?php echo $form->dropDownList($model, 'marital_status_id',
                CHtml::listData(ZMaritalStatuses::model()->findAll(),
                  'marital_status_id', 'name_english'), ['class' => 'form-control']); ?>
            </div>
            <?php echo $form->labelEx($model, 'family_contact_number',
              ['class' => 'control-label col-md-2']); ?>
            <div class="col-md-3">
              <?php echo $form->textField($model, 'family_contact_number',
                ['class' => 'form-control', 'size' => 30, 'maxlength' => 255]); ?>
            </div>
          </div>
        </div>
      </div>
      <div class="panel panel-success">
        <div class="panel-heading">Emergency Contact</div>
        <div class="panel-body">
          <div class="form-group">
            <?php echo $form->labelEx($model, 'emergency_full_name_english',
              ['class' => 'col-md-2 control-label']); ?>
            <div class="col-md-3">
              <?php echo $form->textField($model, 'emergency_full_name_english',
                ['size' => 30, 'maxlength' => 255, 'class' => 'form-control']); ?>
            </div>
            <?php echo $form->labelEx($model, 'emergency_phone_number',
              ['class' => 'control-label col-md-2']); ?>
            <div class="col-md-3">
              <?php echo $form->textField($model, 'emergency_phone_number',
                ['class' => 'form-control', 'size' => 30, 'maxlength' => 255]); ?>
            </div>
          </div>
        </div>
      </div>
      <div class="panel panel-success">
        <div class="panel-heading">Caretaker Contact</div>
        <div class="panel-body">
          <div class="form-group">
            <?php echo $form->labelEx($model, 'caretaker_full_name_english',
              ['class' => 'col-md-2 control-label']); ?>
            <div class="col-md-3">
              <?php echo $form->textField($model, 'caretaker_full_name_english',
                ['size' => 30, 'maxlength' => 255, 'class' => 'form-control']); ?>
            </div>
            <?php echo $form->labelEx($model, 'caretaker_phone_number',
              ['class' => 'control-label col-md-2']); ?>
            <div class="col-md-3">
              <?php echo $form->textField($model, 'caretaker_phone_number',
                ['class' => 'form-control', 'size' => 30, 'maxlength' => 255]); ?>
            </div>
          </div>
        </div>
      </div>
      <div class="panel panel-success">
        <div class="panel-heading" style="padding: 3px 0">
          <div class="row">

            <div class="col-md-5">

              Hajj Information & Product Preferences
            </div>
            <div class="col-md-6">

              Product Preferences
            </div>
          </div>
        </div>
        <div class="panel-body">
          <?php
          $productsCriteria = new CDbCriteria();
          $productsCriteria->condition = 'available_from_date IS NOT NULL AND '
            . 'available_to_date IS NOT NULL AND '
            . 'CURDATE() >= available_from_date AND CURDATE() <= available_to_date';
          $productsCriteria->addCondition('available_from_date IS NOT NULL AND '
            . 'available_to_date IS NULL AND CURDATE() >= available_from_date', 'OR');
          $productsCriteria->addCondition('available_from_date IS NULL AND '
            . 'available_to_date IS NOT NULL AND CURDATE() <= available_to_date', 'OR');
          $productsCriteria->addCondition('available_from_date IS NULL AND '
            . 'available_to_date IS NULL', 'OR');
          $productList = CHtml::ListData(Products::model()
            ->findAll($productsCriteria), 'id', 'name_english');
          ?>
          <div class="form-group row">
            <?php echo $form->labelEx($model, 'badhal_hajj',
              ['class' => 'col-md-2 control-label']); ?>
            <div class="col-md-3">
              <?php echo $form->checkBox($model, 'badhal_hajj', []); ?>
            </div>
            <?php echo $form->labelEx($model, 'preferred_product_1',
              ['class' => 'col-md-2 control-label']); ?>
            <div class="col-md-3">
              <?php
              echo $form->dropDownList($model, 'preferred_product_1',
                (['' => ''] + $productList), ['class' =>
                  'form-control']); ?>
            </div>
            <script>
              $('#RegistrationHelper_preferred_product_1').on('change', function
                () {
                if ($(this).val() == '') {
                  showFlash('error', 'If you do not select a preferred ' +
                  'product. Member payments will not be checked for maturity ' +
                  'and will not be any assigned to any Hajj/Umra trip lists!');
                } else {
                  showFlash('hide');
                }
              });
            </script>
          </div>
          <div class="form-group">
            <?php echo $form->labelEx($model, 'previous_hajj_year',
              ['class' => 'col-md-2 control-label']); ?>
            <div class="col-md-3">
              <?php echo $form->textField($model, 'previous_hajj_year',
                ['class' => 'form-control',
                  'placeholder' => 'E.g. "1999, 2004, 2005" or blank']); ?>
            </div>
            <?php echo $form->labelEx($model, 'preferred_product_2',
              ['class' => 'col-md-2 control-label']); ?>
            <div class="col-md-3">
              <?php echo $form->dropDownList($model, 'preferred_product_2',
                $productList, ['class' => 'form-control', 'prompt' => '']); ?>
            </div>
          </div>
          <div class="form-group">
            <?php echo $form->labelEx($model, 'preferred_product_3',
              ['class' => 'col-md-offset-5 col-md-2 control-label']); ?>
            <div class="col-md-3">
              <?php echo $form->dropDownList($model, 'preferred_product_3',
                $productList, ['class' => 'form-control', 'prompt' => '']); ?>
            </div>
          </div>
        </div>
      </div>

      <?php
      break;
    case 3:
      $model->scenario = 'documentsReq';
      $docsArray = [
        'application_form',
        'id_copy',
        'passport_photo',
        'mahram_document',
        'medical_document'
      ];
      ?>

      <div class="panel panel-success">
        <div class="panel-heading">Upload Documents</div>
        <div class="panel-body">

          <?php
          foreach ($docsArray as $doc) {
            ?>
            <div class="form-group">
              <?php echo $form->labelEx($model, $doc,
                ['class' => 'col-md-3 control-label']); ?>
              <script>turnBlockOn = false;</script>
              <div class="col-md-4">
                <div class="input-group">
                  <span class="input-group-btn">
                    <span class="btn btn-info btn-xs btn-file"
                          style="height: 24px"
                          onClick="js:turnBlockOn = false;">
                      Browse <?php echo $form->fileField($model, $doc); ?>
                    </span>
                  </span>
                  <input type="text" class="form-control" readonly="">
                </div>
              </div>
              <div class="col-md-3">
                <?php
                if (!empty($model->$doc)) {
                  $x = uniqid();
                  echo '<span id="' . $x . '">&nbsp;&nbsp;'
                    . CHtml::link('View current document',
                      Helpers::sysUrl(Constants::UPLOADS) . $model->$doc,
                      ['target' => '_blank']);
                  echo '&nbsp;' . CHtml::ajaxlink('<img src="'
                      . Helpers::sysUrl(Constants::IMAGES) . Constants::BTN_CROSS
                      . '" style="vertical-align:middle" />',
                      $this->createUrl('helper/deleteUploadedFile'), [
                        'type' => 'POST',
                        // 'dataType' => 'json',
                        'data' => "fn=" . $model->$doc . "&m=" . $model->id
                          . "&f=" . $doc,
                        'beforeSend' =>
                          'function(request){
                          if(!confirm("Are you sure you want to delete the current '
                          . $model->getAttributeLabel($doc) . '?")) {
                            turnBlockOn = false;
                            return false;
                          }
                        }',
                        'success' =>
                          'function(response){
                          if (response=="1") {
                              var el = document.getElementById("' . $x . '");
                              el.parentNode.removeChild( el );
                          } else {
                              alert("There has been an error. Could not remove file!");
                              turnBlockOn = false;
                          }
                        }',
                        'error' => 'function(jqXHR, textStatus, errorThrown){
                                   alert("Communication Error! Please try again.");
                                   turnBlockOn = false;
                            }',
                      ]
                    ) . '</span>';
                }
                ?>
              </div>
            </div>
          <?php
          }
          ?>
        </div>
      </div>

      <?php
      break;
  }
  ?>
  <?php echo CHtml::hiddenField('tab', $tab); ?>
  <?php echo (!$model->isNewRecord) ? $form->hiddenField($model, 'id') : ''; ?>

  <div class="panel panel-success">
    <div class="panel-footer">
      <div class="form-group">
        <div class="col-md-10 col-md-offset-2">
          <a class="btn btn-sm btn-danger"
             href="<?php echo $this->createUrl('incomplete'); ?>">
            <icon class="glyphicon glyphicon-ban-circle"></icon>
            Cancel</a>
          <?php if ($tab != 1) { ?>
            <a class="btn btn-sm btn-warning" href="<?php
            echo $this->createUrl('create', [
              'tab' => isset($tabs[$tab - 1]) ? $tab - 1 : $tab - 2,
              'id' => $model->id
            ]);
            ?>">
              <icon class="glyphicon glyphicon-arrow-left"></icon>
              Back</a>
          <?php } ?>
          <button class="btn btn-sm btn-primary">
            <icon
              class="glyphicon glyphicon-save"></icon> <?php echo($tab == 3 ?
              'Save Application' : 'Save & Next >>'); ?>
          </button>
        </div>
      </div>
    </div>
  </div>
  <?php include("/js/registration-helper.php"); ?>

  <?php $this->endWidget(); ?>

</div><!-- form -->