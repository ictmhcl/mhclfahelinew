<?php
/* @var $this MembersController */
/* @var $model Members */
$id = $model->id;

$pieces = [
  ['heading' => 'Individual Info', 'height' => '42px',
    'text' => "<strong>" . $model->memberPersonalInfoText . "</strong>"],
  ['heading' => 'Family', 'text' => $model->familyInfoText],
  ['heading' => 'Emergency', 'text' => $model->emergencyContactInfoText],
  ['heading' => 'Hajj Info', 'text' => $model->hajjInfoText],
  [
    'heading' => 'Documents',
    'text' => $this->renderPartial('//members/_memberDocsSummary',
      ['model' => $model], true),
    'height' => '70px',
    'url' => in_array('documentUpload', Helpers::perms()) ?
      Yii::app()->createUrl('members/documentUpload', ['id' => $id]) : ""
  ],
  [
    'heading' => 'Group Members',
    'text' => $this->renderPartial('//members/_memberGroupDetails',
      ['model' => $model], true),
    'height' => ($model->groupCount > 1 ? ((($model->groupCount * 15)+10)
      .'px'):null),
    'invisible' => $model->groupCount < 2
  ],
];

foreach ($pieces as $piece) {

  if (!empty($display) && !in_array($piece['heading'], $display)) continue;

  if (!empty($piece['invisible'])) continue;

  $url = !empty($piece['url']) ? $piece['url'] :
    (in_array('updateInformation', Helpers::perms()) ?
      Yii::app()->createUrl('members/updateInformation', ['id' => $id]) : "");

  $urlTag = empty($url) ? "" : CHtml::tag('span', ['class' => 'screen-only pull-right'],
    Chtml::link(
      Chtml::tag('icon', ['class' => 'glyphicon glyphicon-pencil']), $url));


  $height = !empty($piece['height']) ? ("height:" . $piece['height'] . ";") : "";

  echo CHtml::tag('div', ['class' => 'input-group'],
    CHtml::tag('div', ['class' => 'input-group-addon', 'style' => 'width:20%'],
      $piece['heading']) .
    CHtml::tag('div', [
      'class' => 'form-control', 'style' => $height . 'overflow:hidden'],
      $urlTag
      . $piece['text'])
  );
}
