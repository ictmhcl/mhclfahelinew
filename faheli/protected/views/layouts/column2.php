<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>
<div class="">
  <div id="flasher">
    <?php
    if (!Helpers::config('keepAlerts')) {
      $alertPeriod = (int) Helpers::config('alertPeriod');

      Yii::app()->clientScript->registerScript(
              'myHideEffect', '$(".alert-flash").animate({opacity: 1.0}, ' . ((empty($alertPeriod) ? 15 : $alertPeriod) * 1000) . ').slideUp("slow");', CClientScript::POS_READY
      );
    }
    foreach (Yii::app()->user->getFlashes() as $key => $message) {
      echo '<div class="alert alert-' . ($key=='error'?'danger':$key)
        . ' alert-flash small">' . $message . '</div>';
    }
    ?>
  </div>

  <div id="content">

    <?php
    if (!empty($icon))
      echo '<h4><icon class="glyphicon glyphicon-' . $icon . ' pull-left" style="margin:2px 10px"></icon></h4>';

    if (Yii::app()->controller->id == 'umra') {
      echo "
        <style>
          .panel-success>.panel-heading {
          background: lavender;
          border-color: lavender;
          color: darkblue;
          }
          a.list-group-item.active, a.list-group-item.active:hover, a.list-group-item.active:focus {
              z-index: 2;
              color: #fff;
              background-color: #3F4398;
              border-color: #3F4398;
          }
        </style>
      ";
    } elseif (Yii::app()->controller->id == 'nonMembers') {
      echo "
        <style>
          .panel-success>.panel-heading {
          background: sandybrown;
          border-color: sandybrown;
          color: saddlebrown;
          }
          a.list-group-item.active, a.list-group-item.active:hover, a.list-group-item.active:focus {
              z-index: 2;
              color: #fff;
              background-color: saddlebrown;
              border-color: saddlebrown;
          }
        </style>
      ";

    } elseif (Yii::app()->controller->id == 'attendance') {
      echo "
        <style>
          .panel-success>.panel-heading {
          background: papayawhip;
          border-color: papayawhip;
          color: peru;
          }
          a.list-group-item.active, a.list-group-item.active:hover, a.list-group-item.active:focus {
              z-index: 2;
              color: #fff;
              background-color: peru;
              border-color: peru;
          }
        </style>
      ";

    }
      echo $content;
    ?>
  </div><!-- content -->
</div>
<?php $this->endContent(); ?>