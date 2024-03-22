<?php
/* @var $this MembersController */
/* @var $model Members */


?>

<h3>Update Members <?php echo $model->id; ?></h3>

<?php $this->renderPartial('_form', ['model'=>$model]); ?>