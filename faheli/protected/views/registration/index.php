<?php
/* @var $this RegistrationController */
/* @var $dataProvider CActiveDataProvider */

//$this->menu = StaticMenus::$menus;
?>

<h3>Application Forms</h3>

<?php $this->widget('zii.widgets.CListView', [
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
]); ?>
