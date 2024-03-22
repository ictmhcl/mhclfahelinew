<div class="input-group">
  <div class="input-group-addon" style="width:20%">Personal Details</div>
  <div class="form-control" style="height:60px;overflow:hidden;">
    <?= '<strong>' . $model->memberPersonalInfoText . '</strong>' ?>
  </div>
</div>
<div class="input-group">
  <div class="input-group-addon" style="width:20%">Deposits</div>
  <div class="form-control" style="overflow: hidden;"><?= $model->depositsInfoText ?></div>
</div>
<div class="input-group">
  <div class="input-group-addon" style="width:20%">Documents</div>
  <div class="form-control" style="overflow: hidden;height: 70px;">
    <?php $this->renderPartial('/members/_memberDocsSummary', ['model' => $model]); ?>
  </div>
</div>