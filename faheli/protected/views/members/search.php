<?php
/* @var $this MembersController */
/* @var $model Members */
/* @var $form CActiveForm */
//$this->menu = StaticMenus::$memberMenus;
?>
<h3>Search Member</h3>
<ul class="nav nav-tabs small">
  <li class="active"><a>Search</a></li>
</ul>

<div class="tab-content">
  <div class="form small">
    <?php $this->renderPartial('_search'); ?>
  </div>
</div>

