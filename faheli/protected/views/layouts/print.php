<?php
/* @var $this Controller */
Yii::app()->clientScript->registerMetaTag('text/html; charset=utf-8', null, 'Content-Type');
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/print.css');
?>
    <div id="content">
<?php echo $content; ?>
    </div><!-- content -->

