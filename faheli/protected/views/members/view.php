<?php
/* @var $this MembersController */
/* @var $model Members */
$id = $model->id;
?>

<h3><?php
  echo ucwords($model->person->full_name_english) .
    ' <small><strong>' . $model->MHC_ID . '</strong></small>';
  ?></h3>
<ul class="nav nav-tabs small">
  <li class="active"><a>Summary</a></li>
</ul>

<div class="tab-content">
  <div class="form small">

    <?php
    $form = $this->beginWidget('CActiveForm', [
      'id' => 'members-form',
      'method' => 'get',
      'action' => 'search',
      // Please note: When you enable ajax validation, make sure the corresponding
      // controller action is handling ajax validation correctly.
      // There is a call to performAjaxValidation() commented in generated controller code.
      // See class documentation of CActiveForm for details on this.
      'enableAjaxValidation' => false,
    ]);
    ?>
    <div class="panel panel-success">
      <div class="panel-body">
        <div class="col-md-8 screen-only">
          <?php $this->renderPartial('_search'); ?>
        </div>
        <div class="col-md-4">
          <?php if ($model->state_id == Constants::MEMBER_PENDING_FIRST_PAYMENT) { ?>
            <span
              class="badge badge-important pull-right">Pending First Payment</span>
          <?php } else { ?>
            <div class="input-group pull-right">
              <div class="input-group-addon">
                Balance <span
                  class="print-only"><?= ' (' . date('d F Y') . ')' ?></span>
              </div>
              <span class="form-control balance">
                <?php echo Helpers::currency($model->accountBalance, 'MVR'); ?>
              </span>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
    <div class="panel panel-success">
      <div class="panel-heading">Member Details</div>
      <div class="panel-body">
        <div class="col-md-9" style="margin-bottom:10px">
          <?php $this->renderPartial('_memberInfo', ['model' => $model]); ?>
        </div>
        <div class="col-md-3" style="text-align: center; margin-bottom:10px">
          <?php $this->renderPartial('_photo', ['model' => $model]); ?>
          <div style="margin-top: 10px">
            <span
              style="font-size: 16px;font-weight: bold;color:black"><?php echo $model->MHC_ID ?></span><br>
            <span style="color:black">Member since</span><br>
            <span
              style="font-size: 14px;color:black"><?php echo date('d F Y', strtotime($model->membership_date)) ?></span>
          </div>
          <div class="screen-only">
            <?php if ((Yii::app()->controller->action->id <> "deposit") && in_array('deposit', Helpers::perms())) { ?>
            <div style="margin-top: 10px">
              <a
                href="<?php echo $this->createUrl('deposit', ['id' => $model->id]); ?>"
                style="width:200px" class="btn btn-sm btn-primary">
                <icon class="glyphicon glyphicon-circle-arrow-down"></icon>
                Collect Deposit</a>
            </div>
            <?php } ?>
            <?php if ((Yii::app()->controller->action->id <> "statement") && in_array('statement', Helpers::perms())) { ?>

            <div style="margin-top: 10px">
              <a
                href="<?php echo $this->createUrl('statement', ['id' => $model->id]); ?>"
                style="width:200px" class="btn btn-sm btn-primary">
                <icon class="glyphicon glyphicon-list"></icon>
                Member Statement</a>
            </div>
            <?php } ?>
            <?php if (Helpers::hasPerm('index','messaging')) { ?>
            <div style="margin-top: 10px">
              <a
                href="<?php echo Yii::app()->createUrl('messaging/index', ['recipient' => $model->id]); ?>"
                style="width:200px" class="btn btn-sm btn-primary">
                <icon class="glyphicon glyphicon-envelope"></icon>
                Send Message</a>
            </div>
            <?php } ?>
            <?php if (Helpers::hasPerm('memberCard')) { ?>
            <div style="margin-top: 10px">
              <a
                href="<?php echo $this->createUrl('memberCard', ['id' => $model->id]); ?>"
                style="width:200px" class="btn btn-sm btn-primary">
                <icon class="glyphicon glyphicon-credit-card"></icon>
                Membership Card</a>
            </div>
            <?php } ?>
            <?php if (0 && (Yii::app()->controller->action->id <> "documentUpload") && Helpers::hasPerm('documentUpload')) { ?>
            <div style="margin-top: 10px">
              <a
                href="<?php echo $this->createUrl('documentUpload', ['id' => $model->id]); ?>"
                style="width:200px" class="btn btn-sm btn-primary">
                <icon class="glyphicon glyphicon-picture"></icon>
                Upload Documents</a>
            </div>
            <?php } ?>
            <?php if ((Yii::app()->controller->action->id <> "debit")
              && Helpers::hasPerm('debit','members')
              && $model->accountBalance > 0) { ?>
              <div style="margin-top: 10px">
                <a
                  href="<?php echo Yii::app()->createUrl('members/debit', ['id' => $model->id]); ?>"
                  style="width:200px" class="btn btn-sm btn-primary">
                  <icon class="glyphicon glyphicon-export"></icon>
                  Debit Account</a>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
    <?php $this->endWidget(); ?>

  </div>
  <!-- form -->
</div>
