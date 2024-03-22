<?php
/* @var $this RegistrationController */
/* @var $model RegistrationHelper */

$tabDisplay = RegistrationHelper::$tabDisplay;
?>

<h3><?php
  echo '<span style="color:green">Enter ' . $tabDisplay[$tab][1]
    . ' Details</span>' .
    ($tab == 1 ? '' : (' <small><strong>' . $model->full_name_english) . ', '
      . $model->id_pp_no . '</strong></small>')
  ?></h3>

<div class="tabs">
  <ul class="nav nav-tabs small">
    <?php
    $validTabs = $model->formCompletedCheck(false);
    $model->clearErrors();
    $model->scenario = $tabs[$tab];
    if (!empty($_POST)) {
      $model->validate();
    }
    $idParam = [];
    if (!$model->isNewRecord) {
      $idParam = ['id' => $model->id];
    }
    foreach ($tabs as $tabNum => $tabName) {
      echo '<li' . ($tabNum == $tab ? ' class="active"' : '') . '><a'
        . (($validTabs[$tabNum] || $model->isNewRecord || $tabNum == $tab) ? '' :
          ' class="tab-invalid"') . ' href="' .
        $this->createUrl('', array_merge($idParam, ['tab' => $tabNum])) . '">' .
        '<icon class="glyphicon glyphicon-' . $tabDisplay[$tabNum][0] . '"></icon>&nbsp; ' .
        ucwords($tabDisplay[$tabNum][1]) . '</a></li>';
    }
    ?>
  </ul>
</div>

<div class="tab-content">
  <?php $this->renderPartial('_newForm', [
    'model' => $model,
    'tabs' => $tabs,
    'tab' => $tab,
    'getPerson' => (empty($getPerson) ? null : $getPerson)
  ]); ?>
</div>