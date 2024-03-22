<?php

$docsArray = ['application_form', 'id_copy', 'passport_photo',
  'mahram_document', 'medical_document'];

foreach ($docsArray as $doc) {
  if (!empty($model->applicationForm->$doc)) {
    echo CHtml::link($model->applicationForm->getAttributeLabel($doc) .
      ' <icon class="glyphicon glyphicon-new-window"></icon>',
      Helpers::sysUrl(Constants::UPLOADS) . $model->applicationForm->$doc,
      ['target' => '_blank']);
    echo '<br/>';
  }
}
?>
