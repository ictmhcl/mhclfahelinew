<?php
/* @var $this MembersController */
/* @var $model Members */
/* @var $form CActiveForm */
?>
<div class="input-group">
  <?php
  echo CHtml::textField('q', '', [
      'placeholder' => ' Name, ID, MHC No (separate with commas if many MHC Nos)',
      'class' => 'form-control',
      'id' => 'searchMembertext',
      'autofocus' => 'autofocus'
  ]);
  ?>
  <a style="cursor:pointer;" class="btn btn-xs btn-primary input-group-addon" href="#"
     onClick='location.href = "<?=Yii::app()->createUrl('members/search') .
     '/q/'; ?>" + $("#searchMembertext").val()'>
    <icon class="glyphicon glyphicon-search"></icon> Search</a>
</div>

