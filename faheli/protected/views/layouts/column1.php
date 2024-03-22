<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>
<div id="flasher">
  <?php
  if (!Helpers::config('keepAlerts')) {
    $alertPeriod = (int) Helpers::config('alertPeriod');

    Yii::app()->clientScript->registerScript(
            'myHideEffect', '$(".alert-flash").animate({opacity: 1.0}, ' . ((empty($alertPeriod) ? 15 : $alertPeriod) * 1000) . ').slideUp("slow");', CClientScript::POS_READY
    );
  }
  foreach (Yii::app()->user->getFlashes() as $key => $message) {
    echo '<div class="alert alert-' . ($key == 'error' ? 'danger' : $key)
      . ' alert-flash small">' . $message . '</div>';
  }
  ?>
</div>

<div id="content">

  <?php
  $appActionItem = AppActions::model()->with('navigations')->find([
      'condition' => 't.controller = :controller AND t.action = :action AND navigations.parent_id IS NOT NULL',
      'params' => [
          'controller' => Yii::app()->controller->id,
          'action' => Yii::app()->controller->action->id,
      ],
  ]);
  if (!empty($appActionItem)) {
    $navItems = $appActionItem->navigations;
    if (!empty($navItems))
      $icon = $navItems[0]->icon;
  }



  if (!empty($icon))
    echo '<h4><icon class="glyphicon glyphicon-' . $icon . ' pull-right"
style="margin:2px 10px"></icon></h4>';
  echo $content;
  ?>
</div><!-- content -->
<?php $this->endContent(); ?>