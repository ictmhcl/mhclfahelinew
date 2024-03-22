<div class="input-group">
  <div class="input-group-addon" style="width:20%">Personal Details</div>
  <div class="form-control" style="height:60px;overflow:hidden;">
    <?= '<strong>'. $model->person->getPersonText() .
    '</strong>' ?>
  </div>
</div>
<div class="input-group">
  <div class="input-group-addon" style="width:20%">Documents</div>
  <div class="form-control" style="overflow: hidden;height: 70px;">
    <?php
    $docsArray = ['id_copy', 'application_form', 'mahram_document'];
    foreach ($docsArray as $doc) {
      if (!empty($model->$doc)) {
        echo CHtml::link($model->getAttributeLabel($doc) .
          ' <icon class="glyphicon glyphicon-new-window"></icon>',
          Helpers::sysUrl(Constants::UPLOADS) . $model->$doc,
          ['target' => '_blank']);
        echo '<br/>';
      }
    }
    ?>
  </div>
</div>