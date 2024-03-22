<?php
/* @var $this RegistrationController */
/* @var $appForm ApplicationForms */
/* @var $verifyModel ApplicationFormVerifications */
/* @var $form CActiveForm */

$clientScriptUrl = Yii::app()->request->baseUrl . "/js/jtk-4.2.1.js";
$clientScript = Yii::app()->clientScript;
$clientScript->registerScriptFile($clientScriptUrl, CClientScript::POS_HEAD);
?>

<div class="form small verifyForm">

  <?php
  $form = $this->beginWidget('CActiveForm', [
    'id' => 'application-forms-form',
    'enableAjaxValidation' => false,
  ]);
  ?>
  <div class="panel panel-default">
    <div class="panel-body">
      <div class="col-md-6"
           style="padding-right:0px; padding-left:0px;padding-bottom:5px">
        <div class="row" style="margin-left:-7px">
          <div class="form-group">
            <?php echo $form->labelEx($verifyModel, 'applicant_verified',
              ['class' => 'col-md-4', 'style' => 'text-align:right;']); ?>
            <div class="col-md-1">
              <?php echo $form->checkBox($verifyModel, 'applicant_verified', [
                'class' => '', 'autofocus' => 'autofocus'
              ]); ?>
            </div>
            <div class="col-md-7">
              <?php echo $form->textArea($verifyModel, 'applicant_comment', ['class' => 'form-control']); ?>
            </div>
          </div>
        </div>
        <div>
          <?php
          $registerer = (!empty($appForm->operationLog->modifiedUser) ?
            $appForm->operationLog->modifiedUser->person->full_name_english :
            $appForm->operationLog->createdUser->person->full_name_english);
          echo '<div style="color: red; font-style:italic; width: 400px;">Record entered/last updated By: ' . $registerer . '</div>';
          ?>
        </div>
      </div>
      <div class="col-md-6"
           style="padding-right:0px; padding-left:0px;padding-bottom:5px">
        <div class="pull-right" style="text-align: right">
          <div class="badge badge-important" style="margin-bottom: 2px">
            Application
            Status: <?php echo $appForm->state->name_english; ?></div>
          <br>

        </div>
      </div>
    </div>

    <?php echo $form->errorSummary($appForm); ?>

    <div class="panel panel-success">
      <div class="panel-heading">Personal Details</div>
      <div class="panel-body">
        <div class="col-md-6">
          <div class="input-group">
            <div class="input-group-addon input-group-addon-label">
              <?php echo $form->labelEx($appForm, 'applicant_full_name_english', ['class' => 'control-label']); ?>
            </div>
            <?php echo $form->textField($appForm, 'applicant_full_name_english', ['readonly' => 'readonly', 'class' => 'form-control']); ?>
          </div>
        </div>
        <div class="col-md-6">
          <div class="input-group">
            <?php echo $form->textField($appForm, 'applicant_full_name_dhivehi', ['readonly' => 'readonly', 'class' => 'form-control thaana']); ?>
            <div class="input-group-addon input-group-addon-label">
              <?php echo $form->labelEx($appForm, 'applicant_full_name_dhivehi', ['class' => 'control-label thaanaLabel']); ?>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="input-group">
            <div class="input-group-addon input-group-addon-label">
              <?php echo $form->labelEx($appForm, 'id_no', ['class' => 'control-label']); ?>
            </div>
            <?php echo $form->textField($appForm, 'id_no', ['readonly' => 'readonly', 'class' => 'form-control']); ?>
          </div>
        </div>
        <div class="col-md-3">
          <div class="input-group">
            <div class="input-group-addon input-group-addon-label">
              <?php echo $form->labelEx($appForm, 'd_o_b', ['class' => 'control-label']); ?>
            </div>
            <?php echo $form->textField($appForm, 'd_o_b', ['readonly' => 'readonly', 'class' => 'form-control']); ?>
          </div>
        </div>
        <div class="col-md-3">
          <div class="input-group">
            <div class="input-group-addon input-group-addon-label">
              <?php echo $form->labelEx($appForm, 'applicant_gender_id', ['class' => 'control-label']); ?>
            </div>
            <?php echo $form->textField($appForm->applicantGender, 'name_english', ['readonly' => 'readonly', 'class' => 'form-control']); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="panel panel-success">
      <div class="panel-heading">Permanent Address & Contact Details</div>
      <div class="panel-body">
        <div class="col-md-6">
          <div class="input-group">
            <div class="input-group-addon input-group-addon-label">
              <?php echo CHtml::label('Address', '', ['class' => 'control-label']); ?>
            </div>
            <?php
            echo CHtml::textField('', (!empty($appForm->perm_address_island_id) ?
                ($appForm->permAddressIsland->atoll->abbreviation_english . ". "
                  . $appForm->permAddressIsland->name_english . ', ') : "")
              . $appForm->perm_address_english . ', ' . $appForm->permCountry->name,
              ['readonly' => 'readonly', 'class' => 'form-control',
                'style' => 'height: 50px'])
            ?>
          </div>
        </div>
        <div class="col-md-6">
          <div class="input-group">
            <?php
            echo CHtml::textField('', (!empty($appForm->perm_address_island_id) ?
                ($appForm->permAddressIsland->atoll->abbreviation_dhivehi . ". "
                  . $appForm->permAddressIsland->name_dhivehi . '، ') : "")
              . $appForm->perm_address_dhivehi . '، ' .
              $appForm->permCountry->name_dhivehi,
              ['readonly' => 'readonly', 'class' => 'form-control thaana',
                'style' => 'height: 50px'])
            ?>
            <div class="input-group-addon input-group-addon-label">
              <?php echo $form->labelEx($appForm, 'perm_address_dhivehi', ['class' => 'control-label thaanaLabel']); ?>
            </div>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="col-md-4">
          <div class="input-group">
            <div class="input-group-addon input-group-addon-label">
              <?php echo $form->labelEx($appForm, 'phone_number_1', ['class' => 'control-label']); ?>
            </div>
            <?php echo $form->textField($appForm, 'phone_number_1', ['readonly' => 'readonly', 'class' => 'form-control']); ?>
          </div>
        </div>
        <div class="col-md-4">
          <div class="input-group">
            <div class="input-group-addon input-group-addon-label">
              <?php echo $form->labelEx($appForm, 'email_address', ['class' => 'control-label']); ?>
            </div>
            <?php echo $form->textField($appForm, 'email_address', ['readonly' => 'readonly', 'class' => 'form-control']); ?>
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
        <div class="col-md-4">
          <div class="input-group">
            <div class="input-group-addon input-group-addon-label">
              <?php echo $form->labelEx($appForm, 'family_contact_no',
                ['class' => 'control-label']); ?>
            </div>
            <?php echo $form->textField($appForm, 'family_contact_no',
              ['readonly' => 'readonly', 'class' => 'form-control']); ?>
          </div>
        </div>
        <div class="col-md-6">
          <div class="input-group">
            <div class="input-group-addon input-group-addon-label">
              <?php echo $form->labelEx($appForm, 'emergency_contact_full_name_english',
                ['class' => 'control-label']); ?>
            </div>
            <?php echo $form->textField($appForm, 'emergency_contact_full_name_english',
              ['readonly' => 'readonly', 'class' => 'form-control', 'style'
              => 'width: 60%']); ?>
            <?php echo $form->textField($appForm, 'family_contact_no',
              ['readonly' => 'readonly', 'class' => 'form-control', 'style'
              => 'width: 40%']); ?>
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel-success">
      <div class="panel-heading">Hajj Information</div>
      <div class="panel-body">
        <div class="row" style="margin-left:0px">
          <div class="col-md-3">
            <div class="input-group">
              <div class="input-group-addon input-group-addon-label">
                <?php echo $form->labelEx($appForm, 'badhal_hajj', ['class' => 'control-label']); ?>
              </div>
              <?php echo CHtml::textField('badhal_hajj', ($appForm->badhal_hajj ? 'Yes' : 'No'), ['readonly' => 'readonly', 'class' => 'form-control']); ?>
            </div>
          </div>
          <div class="col-md-2">
            <div class="input-group">
              <div class="input-group-addon input-group-addon-label">
                <?php echo $form->labelEx($appForm, 'preferred_year_for_hajj',
                  ['class' => 'control-label']); ?>
              </div>
              <?php echo $form->textField($appForm, 'preferred_year_for_hajj',
                ['readonly' => 'readonly', 'class' => 'form-control']); ?>
            </div>
          </div>
          <div class="col-md-4">
            <div class="input-group">
              <div class="input-group-addon input-group-addon-label">
                <?php echo $form->labelEx($appForm, 'group_name',
                  ['class' => 'control-label']); ?>
              </div>
              <?php echo $form->textField($appForm, 'group_name',
                ['readonly' => 'readonly', 'class' => 'form-control']); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel-success">
      <div class="panel-heading">Documents</div>
      <div class="panel-body">

        <?php
        $docsArray = ['application_form', 'id_copy', 'mahram_document'];
        foreach ($docsArray as $doc) {
          ?>
          <div style="margin-left:0px">
            <div class="col-md-5">
              <div class="input-group">
                <div class="input-group-addon input-group-addon-label"
                     style="width:40%;">
                  <?php echo $form->labelEx($appForm, $doc, ['class' => 'control-label']); ?>
                </div>
                <?php
                echo '<span class="form-control">' .
                  ((!empty($appForm->$doc)) ? CHtml::link('View', Helpers::sysUrl(Constants::UPLOADS) . $appForm->$doc, ['target' => '_blank']) : '') .
                  '</span>';
                ?>
              </div>
            </div>
          </div>
          <?php
        }
        ?>
      </div>
    </div>
    <?php echo $form->hiddenField($appForm, 'id'); ?>

    <div class="panel panel-success">
      <div class="panel-footer">
        <div class="form-group">
          <div class="col-md-12" style="float:none;text-align: right">
            <a class="btn btn-sm btn-warning"
               href="<?php echo $this->createUrl('markIncomplete', ['id' => $appForm->id]); ?>">
              <icon class="glyphicon glyphicon-pencil"></icon>
              Change to INCOMPLETE</a>
            <button class="btn btn-sm btn-primary">
              <icon class="glyphicon glyphicon-save"></icon>
              Save & Register
            </button>
          </div>
        </div>
      </div>
    </div>


    <?php $this->endWidget(); ?>

  </div><!-- form -->