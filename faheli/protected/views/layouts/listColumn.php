<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>
  <div class="col-md-3" style="padding:0px">
    <h3>&nbsp;</h3>

    <div class="list-group">
      <div id="hajji_grid">
        <?php
        if (Yii::app()->controller->id == 'lists') {
          $this->renderPartial('hajjiList');
        }
        ?>
      </div>

      <?php

      $sideMenus = Helpers::sideMenus();
      if (!empty($sideMenus))
        echo '<div class="list-group-item" style="font-weight:bold;">Operations</div>';
      foreach ($sideMenus as $sideMenu) {
        if ($sideMenu['visible']) {
          echo '<a class="list-group-item' . ($sideMenu['active'] ? ' active' : '') . '" href="' . $sideMenu['url'] . '">' .
            '<icon class="glyphicon glyphicon-' . $sideMenu['icon'] . '"></icon>&nbsp;&nbsp;' . $sideMenu['label'] . '</a>';
        }
        if ($sideMenu['active'])
          $icon = $sideMenu['icon'];
      }
      ?>
    </div>
  </div>

  <div class="col-md-9">
    <div id="flasher">
      <?php
      if (!Helpers::config('keepAlerts')) {
        $alertPeriod = (int)Helpers::config('alertPeriod');

        Yii::app()->clientScript->registerScript(
          'myHideEffect', '$(".alert-flash").animate({opacity: 1.0}, ' . ((empty($alertPeriod) ? 15 : $alertPeriod) * 1000) . ').slideUp("slow");', CClientScript::POS_READY
        );
      }
      foreach (Yii::app()->user->getFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . ' alert-flash small">' . $message . '</div>';
      }
      ?>
    </div>

    <div id="content">

      <?php
      if (!empty($icon))
        echo '<h4><icon class="glyphicon glyphicon-' . $icon . ' pull-left" style="margin:2px 10px"></icon></h4>';
      echo $content;
      ?>
    </div>
    <!-- content -->
  </div>

<?php $this->endContent(); ?>