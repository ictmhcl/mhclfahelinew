<?php
/* @var $this UmraController */
/* @var $pilgrim UmraPilgrims */
/** @var $umraTransaction UmraTransactions */
$pilgrim = $umraTransaction->umraPilgrim;
$title = !$viewOnly ? 'EDIT RECEIPT' : 'TRANSACTION HISTORY';
?>

<h3><span
    style="color:red"><?= $title ?> </span>:
  <?= Helpers::umraReceiptNumber($umraTransaction) . ' <small>' .
  $pilgrim->person->full_name_english . ', ' . $pilgrim->getUMRA_PILGRIM_ID() .
  '</small>';
  ?></h3>
<ul class="nav nav-tabs small">
  <li class="active"><a>Receipt</a></li>
</ul>

<div class="tab-content">
  <div class="form small">

    <?php
    $form = $this->beginWidget('CActiveForm', [
      'id' => 'umra-receipt-revise-form',
      'method' => 'post',
      'enableAjaxValidation' => false,
    ]);
    ?>
    <div class="panel panel-success">
      <div class="panel-body">
        <div class="col-md-4 pull-right">
          <div class="input-group">
            <div class="input-group-addon">
              Pilgrim Balance
            </div>
            <span class="form-control balance">
              <?= Helpers::currency($pilgrim->account_balance, 'MVR'); ?>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel-success">
      <div class="panel-heading">Pilgrim Details<span
          class="pull-right">Receipt Details</span>
      </div>
      <div class="panel-body">
        <div class="col-md-7">
          <div style="margin-bottom:10px">
            <?php $this->renderPartial('_pilgrimInfo', ['pilgrim' => $pilgrim]) ?>
          </div>
        </div>
        <div class="col-md-5 pull-right">
          <?php
          if ($umraTransaction->is_cancelled) {
            echo '<span class="badge badge-important pull-right"
              style="margin-bottom:3px;">Transaction Cancelled</span>';
          }
          ?>
          <div class="input-group">
            <?php
            echo $form->label($umraTransaction, 'transaction_time', [
              'class' => 'control-label input-group-addon',
              'style' => 'width:40%;margin-top:5px']);

            if (!$viewOnly) {
              Yii::import('ext.CJuiDateTimePicker.CJuiDateTimePicker');
              $this->widget('CJuiDateTimePicker', [
                'model' => $umraTransaction,
                'attribute' => 'transaction_time',
                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                'options' => [
                  'dateFormat' => Constants::DATE_DISPLAY_FORMAT,
                ], // jquery plugin options
                'language' => '',
                'htmlOptions' => [
                  'class' => 'form-control',
                  'disabled' => ($umraTransaction->is_cancelled ? 'disabled' : ''),
                ]
              ]);
            } else
              echo '<span class="form-control">' .
                (new DateTime($umraTransaction->transaction_time))
                  ->format('d F Y H:m:s') .
                '<icon class="glyphicon glyphicon-ban-circle pull-right"
                    style="color:red"></icon></span>';

            ?>
          </div>
          <div class="input-group">
            <?php
            echo $form->label($umraTransaction, 'amount', [
              'class' => 'control-label input-group-addon',
              'style' => 'width:40%;margin-top:5px']);
            echo '<span class="form-control">' .
              Helpers::currency($umraTransaction->amount) .
              '<icon class="glyphicon glyphicon-ban-circle pull-right"
                style="color:red"></icon></span>';
            ?>
          </div>
          <div class="input-group">
            <?php
            echo $form->label($umraTransaction, 'transaction_medium_id', [
              'class' => 'control-label input-group-addon',
              'style' => 'width:40%;margin-top:5px']);
            if (!$viewOnly)
              echo $form->dropDownList($umraTransaction,
                'transaction_medium_id',
                CHtml::listData(ZTransactionMediums::model()->findAll(), 'id',
                  'name_english'), ['class' => 'form-control',
                  'disabled' => ($umraTransaction->is_cancelled ? 'disabled' : ''),
                ]);
            else
              echo '<span class="form-control">' .
                $umraTransaction->transactionMedium->name_english .
                '<icon class="glyphicon glyphicon-ban-circle pull-right"
                style="color:red"></icon></span>';

            ?>
          </div>
          <?php
          if (!$viewOnly) {
            echo CHtml::tag('div', ['class' => 'input-group'],
              CHtml::label('Edit Remarks', '', [
                'class' => 'control-label input-group-addon', 'style' =>
                  'width:40%;margin-top:5px']) .
              CHtml::textArea('editRemarks', '', [
                'class' => 'form-control',
                'placeholder' => 'Describe reason for cancellation /
                revision']));
          }
          ?>

          <?php if (!$umraTransaction->is_cancelled && !$viewOnly) { ?>
            <div class="pull-right">
              <a
                href="<?php echo $this->createUrl('umra/reviseUmraTransaction',
                  ['id' => $umraTransaction->transaction_id,
                    'mode' => 'cancel']) ?>"
                class="btn btn-sm btn-danger"
                onClick='js:
                   if ($("#editRemarks").val().trim() == "") {
                     alert("You must provide reason!")
                     return false;
                   }
                   if (confirm("Are you sure you want to cancel this " +
                    "transaction?\n\nPress OK to CONFIRM your decision!")
                      == false) {
                     turnBlockOn = false;
                     return false;
                   } else {
                     $(this).attr("class", $(this).attr("class") + " disabled");
                      location.href = $(this).attr("href") + "&editRemarks=" +
                      $("#editRemarks").val();
                    return false;
                   }'>
                <icon class="glyphicon glyphicon-remove"></icon>
                Cancel Transaction</a>
              <button type="submit" class="btn btn-sm btn-warning btn-default"
                      onClick='js:
                       if ($("#editRemarks").val().trim() == "") {
                          alert("You must provide reason!")
                          return false;
                        }
                        $(this).attr("disabled", "disabled");
                        submit();'>
                <icon class="glyphicon glyphicon-pencil"></icon>
                Update Transaction
              </button>

            </div>
          <?php } ?>

        </div>

      </div>
    </div>
    <div class="panel panel-success">
      <div class="panel-heading">Receipt Revisions</div>
      <div class="panel-body">
        <?php
        $this->widget('zii.widgets.grid.CGridView', [
          'id' => 'edit-grid',
          'dataProvider' => $editsDataProvider,
          'columns' => [
            'data.transaction_time:text:Transaction Time',
            'data.transactionMedium.name_english:text:Medium',
            [
              'header' => 'Amount',
              'value' => 'Helpers::currency($data->data->amount,"MVR")'
            ],
            'auditSummary:text:Audit Description',
            'remarks:text:Edit Remarks'
          ]
        ]);
        ?>
        <script>
          $(document).ready(function () {
            $('#edit-grid table.small').removeClass('small');
          })
        </script>
      </div>
    </div>
    <?php $this->endWidget(); ?>

  </div><!-- form -->
</div>



