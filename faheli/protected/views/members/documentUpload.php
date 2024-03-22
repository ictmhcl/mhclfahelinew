<script>turnBlockOn = false;</script><?php
/* @var $this RegistrationController */
/* @var $appForm ApplicationForms */
/* @var $form CActiveForm */

?>
<h3><span style="color:green">Document Upload : </span>
  <?= $member->person->full_name_english ?> 
  <small><strong><?= $member->MHC_ID?></strong></small>
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
      'action' => $this->createUrl('documentUpload', ['id' => $member->id]),
      'htmlOptions' =>
        ['class' => 'form-horizontal','enctype' => 'multipart/form-data']
    ]);
    ?>

    <?php
    $appForm->scenario = 'documentsReq';
    $docsArray = ['application_form', 'id_copy', 'mahram_document'];
    ?>

    <div class="panel panel-success">
      <div class="panel-heading">Upload Documents</div>
      <div class="panel-body">

        <?php
        foreach ($docsArray as $doc) {
          ?>
          <div class="form-group">
            <?php echo $form->labelEx($appForm, $doc,
              ['class' => 'col-md-3 control-label']); ?>

            <div class="col-md-4">
              <div class="input-group">
                <span class="input-group-btn">
                  <span class="btn btn-info btn-xs btn-file"
                        style="height: 24px">
                    Browse <?php echo $form->fileField($appForm, $doc); ?>
                  </span>
                </span>
                <input type="text" class="form-control" readonly="">
              </div>
            </div>
            <div class="col-md-3">
              <?php
              if (!empty($appForm->$doc)) {
                $x = uniqid();
                echo '<span id="' . $x . '">&nbsp;&nbsp;' .
                  CHtml::link('View current document',
                    Helpers::sysUrl(Constants::UPLOADS) . $appForm->$doc,
                    ['target' => '_blank']);
              }
              ?>
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
          <div class="col-md-12" style="text-align: right">
            <?php echo CHtml::button('Cancel', [
                'class' => 'btn btn-sm btn-danger',
                'onclick' => 'js:window.location.href=\'' .
                  $this->createUrl('view', ['id' => $member->id]) . '\';'
            ]); ?>
            <?php echo CHtml::submitButton('Upload',
                ['class' => 'btn btn-sm btn-primary']) .
              '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; ?>
          </div>
        </div>
      </div>
    </div>

    <?php $this->endWidget(); ?>

  </div><!-- form -->
</div>
