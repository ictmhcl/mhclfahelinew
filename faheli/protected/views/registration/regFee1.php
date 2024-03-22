<?php
/* @var $this MembersController */
/* @var $model Members */

//$this->menu = StaticMenus::$menus;
?>

<h3><?php echo $formTitle; ?></h3>

<?php $this->renderPartial('_mbrRegFee', ['model'=>$model]); ?>