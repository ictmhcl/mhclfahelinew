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
        </div>
        <div class="form-group">
        <?php echo $form->labelEx($onlineForm, 'id_no', ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-2">
            <?php echo $form->textField($onlineForm, 'id_no', [
                'size' => 7, 'maxlength' => 7, 'class' => 'form-control',
                'style' => 'direction: ltr'
              ]); ?>

          </div>
          <?php echo CHtml::label(H::t('site','tickIfFardHajjPerformed'), "fard_hajj_performed", ['class' => 'col-md-offset-2 col-md-2 control-label']); ?>
          <div class="col-md-1" >
            <?php echo CHtml::checkbox('fard_hajj_performed', false, ['class' => 'form-control float-right']); ?>
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel-success">
      <div class="panel-heading"><?=H::t('site', 'contactInfo')?></div>
      <div class="panel-body">
      <div class="form-group">
          <?php echo $form->labelEx($onlineForm, 'phone_number_1', ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-3">
            <div class="input-group">
              <?php echo $form->textField($onlineForm, 'phone_number_1', [
                'size' => 30, 'maxlength' => 15, 'class' => 'form-control', 'id' => 'mobile_number',
                'style' => 'direction: ltr', 'placeholder' => ''
              ]); ?>
              <span class="input-group-btn">
                <button class="btn btn-danger" id="otp-button" type="button"><?=H::t('site','sendOtp')?></button>
              </span>
            </div><!-- /input-group -->
            <script>
              $('#otp-button').on('click', function() {
                mobileNumber = $('#mobile_number').val();
                if (/^[79]\d{6}$/.test(mobileNumber)) {
                  $.ajax({
                    url: '<?=Yii::app()->createUrl('helper/phoneOtp')?>',
                    data: {number: $('#mobile_number').val()},
                    dataType: 'json',
                    success: function (data) {
                      if (data.status == 'success') {
                        $('#otp-button').addClass('disabled');
                        showFlash('success', data.message);
                      } else {
                        $('#otp-group').slideUp();
                        showFlash('danger', data.message);
                      }
                    }
                  })
                } else {
                  showFlash('danger', '<?=H::t('site','incorrectMobileNumber')?>');
                  $('#otp').val('');
                }

              })
            </script>
          </div>
          <?php echo $form->labelEx($onlineForm, 'email_address', ['class' => 'control-label col-md-2']); ?>
          <div class="col-md-3">
            <?php echo $form->textField($onlineForm, 'email_address', [
              'class' => 'form-control', 'size' => 30, 'maxlength' => 255,
              'style' => 'direction: ltr'
            ]); ?>
          </div>
        </div>
        <div class="form-group" id="otp-group">
          <?php echo CHtml::label(H::t('site','otp'),'otp', ['class' => 'col-md-2 control-label']); ?>
          <div class="col-md-3">
              <?php echo CHtml::textfield('otp','', [
                'size' => 6, 'maxlength' => 6, 'class' => 'form-control', 'id' => 'otp',
                'style' => 'direction: ltr', 'placeholder' => ''
              ]); ?>
          </div>
          <script>
            // $('#otp').on('change', function() {
            //   if (/^\d{6}$/.test($(this).val())) {
            //     $('#submit-btn').removeClass('disabled')
            //   } else {
            //     $('#submit-btn').addClass('disabled')
            //   }
            // })
          </script>
        </div>

      </div>

    </div>

    <?php echo (!$onlineForm->isNewRecord) ? $form->hiddenField($onlineForm, 'id') : ''; ?>

    <div class="panel panel-success">
      <div class="panel-footer">
        <div class="form-group">
          <div class="col-md-10 col-md-offset-2">
            <button class="btn btn-sm btn-primary" id='submit-btn'>
              <icon class="glyphicon glyphicon-save"></icon>
              	<?=H::t('site','submit')?>
            </button>
          </div>
        </div>
      </div>
    </div>

    <?php $this->endWidget(); ?>

  </div><!-- form -->
</div>
