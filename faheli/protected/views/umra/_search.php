<?php
/* @var $form CActiveForm */
?>
<div class="input-group">
  <?php
  echo CHtml::textField('q', '', [
      'placeholder' => 'ID Number',
      'class' => 'form-control',
      'id' => 'searchMembertext'
  ]);
  ?>
  <a style="cursor:pointer;" class="btn btn-xs btn-primary input-group-addon" href="#"
     onClick='location.href = "<?=Yii::app()->createUrl('umra/searchPilgrim') .
     '/q/'; ?>" + $("#searchMembertext").val()'>
    <icon class="glyphicon glyphicon-search"></icon> Search</a>
</div>

