<?php
/* @var $this MembersController */
/* @var $dataProvider CActiveDataProvider */

?>

<h3>Members</h3>

<?php $this->widget('zii.widgets.CListView', [
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
]); ?>
