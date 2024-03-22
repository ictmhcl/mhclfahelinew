<?php $this->renderPartial('//members/_photo', ['model' => $pilgrim]); ?>
<?php if ($pilgrim->person->member) {
  $model = $pilgrim->person->member;
  ?>
  <div style="margin-top: 10px">
    <?= CHtml::link(CHtml::tag('span', [
      'class' => 'badge badge-important'], "REGULAR MEMBER"),
      Yii::app()->createUrl('members/view', ['id' => $model->id])) ?>
    <br>
            <span
              style="font-size: 16px;font-weight: bold;color:black"><?php echo $model->MHC_ID ?></span><br>
    <span style="color:black">Member since</span><br>
            <span
              style="font-size: 14px;color:black"><?php echo date('d F Y', strtotime($model->membership_date)) ?></span>
  </div>
<?php } ?>
<div class="screen-only">
  <?php
  if (in_array("umraPayment", Helpers::perms())) { ?>
    <div style="margin-top: 10px">
      <a
        href="<?= $this->createUrl('umraPayment', ['id' => $pilgrim->id]) ?>"
        style="width:200px" class="btn btn-sm btn-primary">
        <icon class="glyphicon glyphicon-circle-arrow-down"></icon>
        Collect Payment</a>
    </div>
    <?php
  }
  if (in_array("umraStatement", Helpers::perms())) {
    ?>
    <div style="margin-top: 10px">
      <a
        href="<?= $this->createUrl('umraStatement',
          ['id' => $pilgrim->id]) ?>"
        style="width:200px" class="btn btn-sm btn-primary">
        <icon class="glyphicon glyphicon-list"></icon>
        Statement</a>
    </div>
    <?php
  }
  ?>

  <!--            <div style="margin-top: 10px">-->
  <!--              <a-->
  <!--                href="-->
  <?php //echo Yii::app()->createUrl('messaging/index', ['recipient' => $model->id]); ?><!--"-->
  <!--                style="width:200px" class="btn btn-sm btn-primary">-->
  <!--                <icon class="glyphicon glyphicon-envelope"></icon>-->
  <!--                Send Message</a>-->
  <!--            </div>-->
  <!--            <div style="margin-top: 10px">-->
  <!--              <a-->
  <!--                href="-->
  <?php //echo $this->createUrl('memberCard', ['id' => $model->id]); ?><!--"-->
  <!--                style="width:200px" class="btn btn-sm btn-primary">-->
  <!--                <icon class="glyphicon glyphicon-credit-card"></icon>-->
  <!--                Membership Card</a>-->
  <!--            </div>-->
  <!--            --><?php //if (!empty($model->applicationForm->passport_photo)) { ?>
  <!--              <div style="margin-top: 10px">-->
  <!--                <a-->
  <!--                  href="-->
  <?php //echo $this->createUrl('documentUpload', ['id' => $model->id]); ?><!--"-->
  <!--                  style="width:200px" class="btn btn-sm btn-primary">-->
  <!--                  <icon class="glyphicon glyphicon-picture"></icon>-->
  <!--                  Upload Documents</a>-->
  <!--              </div>-->
  <!--            --><?php //} ?>
  <?php if (in_array("umraDebit", Helpers::perms()) &&
    $pilgrim->account_balance > 0
  ) { ?>
    <div style="margin-top: 10px">
      <a
        href="<?= Yii::app()->createUrl('umra/umraDebit',
          ['id' => $pilgrim->id]);
        ?>"
        style="width:200px" class="btn btn-sm btn-primary">
        <icon class="glyphicon glyphicon-export"></icon>
        Debit Account</a>
    </div>
  <?php } ?>
</div>
