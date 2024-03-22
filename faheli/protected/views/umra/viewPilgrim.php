<?php
/* @var $this UmraController */
/* @var $pilgrim UmraPilgrims */

?>

<h3><?php
  echo ucwords($pilgrim->person->full_name_english) .
    ' <small><strong>' . $pilgrim->getUMRA_PILGRIM_ID() . '</strong></small>';
  ?></h3>
<ul class="nav nav-tabs small">
  <li class="active"><a>Summary</a></li>
</ul>

<div class="tab-content">
  <div class="form small">

    <?php
    $form = $this->beginWidget('CActiveForm', [
      'id' => 'pilgrim-view',
      'method' => 'get',
      'action' => 'search',
      'enableAjaxValidation' => false,
    ]);
    ?>
    <div class="panel panel-success">
      <div class="panel-body">
        <div class="col-md-8 screen-only">
          <h4><?= $pilgrim->umraTrip->name_english ?></h4>
          <!--          --><?php //$this->renderPartial('_search'); ?>
        </div>
        <div class="col-md-4">
          <?php if (empty($pilgrim->account_balance)) { ?>
            <span
              class="badge badge-important pull-right">Pending First Payment</span>
          <?php } else { ?>
            <div class="input-group pull-right">
              <div class="input-group-addon">
                Total Paid
                <span class="print-only">
                  <?= ' (as at ' . date('d F Y') . ')' ?>
                </span>
              </div>
              <span class="form-control balance">
                <?php echo Helpers::currency($pilgrim->account_balance, 'MVR'); ?>
              </span>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
    <div class="panel panel-success">
      <div class="panel-heading">Pilgrim Details</div>
      <div class="panel-body">
        <div class="col-md-9" style="margin-bottom:10px">
          <?php $this->renderPartial('_pilgrimInfo',['pilgrim' => $pilgrim]); ?>
        </div>
        <div class="col-md-3" style="text-align: center; margin-bottom:10px">
          <?php
          $this->renderPartial('_pilgrimSideBar', ['pilgrim' => $pilgrim]);
          ?>
        </div>
      </div>
    </div>
    <?php $this->endWidget(); ?>

  </div>
  <!-- form -->
</div>
