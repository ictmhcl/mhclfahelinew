<?php
/* @var $this MembersController */
/* @var $appForm ApplicationFormsHelper */
$clientScriptUrl = Yii::app()->request->baseUrl . "/js/jtk-4.2.1.js";
$clientScript = Yii::app()->clientScript;
$clientScript->registerScriptFile($clientScriptUrl, CClientScript::POS_HEAD);
?>

<h3><span style="color:green">Member Update : </span>
  <?= $member->person->full_name_english .
  ' <small><strong>' . $member->MHC_ID . '</strong></small>' ?>
</h3>
<div class="tabs">
  <ul class="nav nav-tabs small">
  </ul>
</div>

<div class="tab-content">
  <div class="form small">
    <?php
    $form = $this->beginWidget('CActiveForm', [
      'id' => 'application-forms-form',
      'enableAjaxValidation' => false,
      'htmlOptions' => ['class' => 'form-horizontal'],
    ]);
    ?>

    <div class="panel panel-default">
      <div class="panel-body">
        <div class="col-md-8"
             style="padding-right:0px; padding-left:0px;padding-bottom:5px">
          <?php
          $verificationComment =
            $appForm->applicationFormVerification->applicant_comment;
          $verifier = (empty($appForm->applicationFormVerification->operationLog)?"":
            (!empty($appForm->applicationFormVerification->operationLog->modifiedUser) ?
            $appForm->applicationFormVerification->operationLog->modifiedUser->person->full_name_english :
            $appForm->applicationFormVerification->operationLog->createdUser->person->full_name_english));

          $registerer = (empty($appForm->operationLog) ? "" :
            $appForm->operationLog->createdUser->person->full_name_english);
          $memberRecorder = (empty($member->operationLog) ? "" :
            $member->operationLog->createdUser->person->full_name_english);
          $updateAudits = ClientAudit::auditLogDataProvider($member, ClientAudit::AUDIT_ACTION_EDIT)->getData();

          $memberUpdater = (!empty($updateAudits) ?
            $updateAudits[0]->Users->full_name :
            "Not Updated!");
          if (!empty($verificationComment)) {
            ?>
            <div class="input-group">
              <div class="input-group-addon" style="width:30%">Verification
                Comment
              </div>
              <span class="form-control"
                    style="height:42px"><?php echo $verificationComment; ?></span>
            </div>
            <?php
          }
          ?>
          <div class="col-md-6">
            <div class="input-group">
              <div class="input-group-addon" style="width:30%">Verifier</div>
              <span class="form-control"><?php echo $verifier; ?></span>
            </div>
            <div class="input-group">
              <div class="input-group-addon" style="width:30%">Form Entry by
              </div>
              <span class="form-control"><?php echo $registerer; ?></span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="input-group">
              <div class="input-group-addon" style="width:30%">Member Entry by
              </div>
              <span class="form-control"><?php echo $memberRecorder; ?></span>
            </div>
            <div class="input-group">
              <div class="input-group-addon" style="width:30%">Member Updated
                by
              </div>
              <span class="form-control"><?php echo $memberUpdater; ?></span>
            </div>
          </div>
        </div>
        <div class="col-md-4"
             style="padding-right:0px; padding-left:0px;padding-bottom:5px">
        </div>
      </div>
    </div>
    <?php echo $form->errorSummary($appForm, null, null, ['style' => 'margin: 5px;', 'class' => 'alert alert-danger']); ?>

    <div class="panel panel-success">
      <div class="panel-heading">Personal Details</div>
      <div class="panel-body">
        <div class="form-group">
          <?php echo $form->labelEx($appForm, 'applicant_full_name_english', ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-4">
            <?php echo $form->textField($appForm, 'applicant_full_name_english', ['size' => 30, 'maxlength' => 255, 'class' => 'form-control']); ?>
          </div>
          <div class="col-md-4">
            <?php echo $form->textField($appForm, 'applicant_full_name_dhivehi', ['class' => 'thaanaKeyboardInput form-control', 'size' => 30, 'maxlength' => 255]); ?>
          </div>
          <?php echo $form->labelEx($appForm, 'applicant_full_name_dhivehi', ['class' => 'col-md-2 control-label pull-left thaanaLabel']); ?>
        </div>
        <div class="form-group">
          <?php echo $form->labelEx($appForm, 'id_no', ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-2">
            <?php echo $form->textField($appForm, 'id_no', ['size' => 7, 'maxlength' => 7, 'class' => 'form-control']); ?>
          </div>
          <?php echo $form->labelEx($appForm, 'd_o_b', ['class' => 'col-md-1 control-label']); ?>
          <div class="col-md-2">
            <?php
            $form->widget('zii.widgets.jui.CJuiDatePicker', [
              'model' => $appForm,
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
          <?php echo $form->labelEx($appForm, 'applicant_gender_id', ['class' => 'col-md-1 control-label']); ?>
          <div class="col-md-2">
            <?php
            echo $form->dropDownList($appForm, 'applicant_gender_id', CHtml::listData(ZGender::model()->findAll(), 'gender_id', 'name_english'), ['prompt' => '', 'class' => 'form-control']);
            ?>
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel-success">
      <div class="panel-heading">Permanent Address & Contact</div>
      <div class="panel-body">
        <div class="form-group row">
          <?php echo $form->labelEx($appForm, 'country_id',
            ['class' => 'control-label col-md-2']); ?>
          <div class="col-md-2">
            <?php
            $countryList = CHtml::listData(ZCountry::model()->findAll(), 'id', 'name');
            echo CHtml::activeDropDownList($appForm, 'country_id', $countryList, [
              'class' => 'form-control',
              (!empty($appForm->country_id) ? '' : 'prompt') => '',
              'onChange' => 'javascript:{
                  if ($(this).val() != ' . Constants::MALDIVES_COUNTRY_ID . ') {
                    $("#permAtollId").val("");
                    $("#permAtollId").trigger("change");
                    $(".permIsland").hide();
                  } else
                    $(".permIsland").show();
                }',
            ]);
            ?>
          </div>
          <?php
          $showPermIsland = $appForm->country_id !=
          Constants::MALDIVES_COUNTRY_ID ? "none" : "block";
          ?>
          <div class="permIsland"
               style="display:<?= $showPermIsland ?>">
            <?php echo $form->labelEx($appForm, 'perm_address_island_id', [
              'class' => 'control-label col-md-1'
            ]); ?>

            <div class="col-md-2">
              <?php
              if (!empty($appForm->perm_address_island_id)) {
                $island = ZIslands::model()->findByPk($appForm->perm_address_island_id);
                $atoll_id = $island->atoll_id;
                $criteria = new CDbCriteria();
                $criteria->order = 'name_english asc';
                $islandList = CHtml::listData(ZIslands::model()->findAllByAttributes(['atoll_id' => $atoll_id, 'is_inhibited' => true], $criteria), 'island_id', 'name_english');
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
                      'model' => get_class($appForm),
                      'attribute' => 'perm_address_island_id'
                    ],
                    'replace' => '#' . Helpers::resolveID($appForm, 'perm_address_island_id'),
                  ]),
                  'class' => 'form-control',
                  'id' => 'permAtollId'
                ]
              );
              ?>
            </div>
            <div class="col-md-3">
              <?php echo CHtml::activeDropDownList($appForm, 'perm_address_island_id',
                $islandList, ['class' => 'form-control', (!empty($atoll_id) ?
                  '' : 'prompt') => '<- Select Atoll first']); ?>
            </div>
          </div>
        </div>
        <div class="form-group">
          <?php echo $form->labelEx($appForm, 'perm_address_english', ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-4">
            <?php echo $form->textField($appForm, 'perm_address_english', ['size' => 30, 'maxlength' => 255, 'class' => 'form-control']); ?>
          </div>
          <div class="col-md-4">
            <?php echo $form->textField($appForm, 'perm_address_dhivehi', ['class' => 'thaanaKeyboardInput form-control', 'size' => 30, 'maxlength' => 255]); ?>
          </div>
          <?php echo $form->labelEx($appForm, 'perm_address_dhivehi', ['class' => 'control-label thaanaLabel col-md-2']); ?>
        </div>
        <div class="form-group">
          <?php echo $form->labelEx($appForm, 'phone_number_1', ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-3">
            <?php echo $form->textField($appForm, 'phone_number_1', ['size' => 30, 'maxlength' => 255, 'class' => 'form-control']); ?>
          </div>
          <?php echo $form->labelEx($appForm, 'email_address', ['class' => 'control-label col-md-2']); ?>
          <div class="col-md-3">
            <?php echo $form->textField($appForm, 'email_address', ['class' => 'form-control', 'size' => 30, 'maxlength' => 255]); ?>
          </div>
        </div>

      </div>
    </div>
    <div class="panel panel-success">
      <div class="panel-heading" style="padding: 3px 0">
        <div class="row">
          <div class="col-md-4">
            Vaarutha / Family phone
          </div>
          <div class="col-md-6">
            Emergency Contact
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="form-group">
          <?php echo $form->labelEx($appForm, 'family_contact_no',
            ['class' => 'control-label col-md-2']); ?>
          <div class="col-md-2">
            <?php echo $form->textField($appForm, 'family_contact_no',
              ['class' => 'form-control', 'size' => 30]); ?>
          </div>
          <?php echo $form->labelEx($appForm, 'emergency_contact_full_name_english',
            ['class' => 'col-md-1 control-label']); ?>
          <div class="col-md-3">
            <?php echo $form->textField($appForm, 'emergency_contact_full_name_english',
              ['size' => 30, 'maxlength' => 255, 'class' => 'form-control']); ?>
          </div>
          <div class="col-md-2">
            <?php echo $form->textField($appForm, 'emergency_contact_phone_no',
              ['class' => 'form-control', 'size' => 30, 'placeholder' =>
                'Emergency Phone']); ?>
          </div>
        </div>
      </div>
      <div class="panel-heading" style="padding: 3px 0">
        <div class="row">

          <div class="col-md-5">

            Hajj Information
          </div>
          <div class="col-md-6">

            <!--              Product Preferences-->
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="form-group row">
          <?php echo $form->labelEx($appForm, 'badhal_hajj',
            ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-1">
            <?php echo $form->checkBox($appForm, 'badhal_hajj', []); ?>
          </div>
          <?php echo $form->labelEx($appForm, 'preferred_year_for_hajj',
            ['class' => 'col-md-1 control-label']); ?>
          <div class="col-md-2">
            <?php echo $form->textField($appForm, 'preferred_year_for_hajj',
              ['class' => 'form-control',
                'placeholder' => 'E.g. "1440" or blank']); ?>
          </div>
          <?php echo $form->labelEx($appForm, 'group_name',
            ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-2">
            <?php echo $form->textField($appForm, 'group_name',
              ['class' => 'form-control']); ?>
          </div>
        </div>
      </div>
    </div>

    <?php echo (!$appForm->isNewRecord) ? $form->hiddenField($appForm, 'id') : ''; ?>

    <div class="panel panel-success">
      <div class="panel-footer">
        <div class="form-group">
          <div class="col-md-10 col-md-offset-2">
            <a class="btn btn-danger btn-sm"
               href="<?php echo $this->createUrl('view', ['id' => $member->id]); ?>">
              <icon class="glyphicon glyphicon-ban-circle"></icon>
              Cancel</a>
            <button class="btn btn-primary btn-sm">
              <icon class="glyphicon glyphicon-pencil"></icon>
              Update Member Information
            </button>
          </div>
        </div>
      </div>
    </div>
    <?php include_once(Yii::getPathOfAlias('webroot')."/js/registration-helper.php"); ?>

    <?php $this->endWidget(); ?>
  </div><!-- form -->
</div>
