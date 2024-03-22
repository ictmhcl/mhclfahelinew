<?php
/* @var $this AgeegaController */
/** @var $transaction AgeegaTransactions */
/** @var $editsDataProvider CArrayDataProvider */
/** @var $viewOnly Boolean */
$title = !$viewOnly ? 'EDIT RECEIPT' : 'TRANSACTION HISTORY';
$rates = Helpers::getCurrentAgeegaRates();
?>

<h3><span
    style="color:red"><?= $title ?> </span>:
  <?= Helpers::ageegaReceiptNumber($transaction) . ' <small>' .
  $transaction->ageega->person->idName . ', phone: ' .
  $transaction->ageega->phone_number .
  '</small>';
  ?></h3>
<ul class="nav nav-tabs small">
  <li class="active"><a>Receipt</a></li>
</ul>

<div class="tab-content">
  <div class="form small">

    <?php
    $form = $this->beginWidget('CActiveForm', [
      'id' => 'ageega-receipt-revise-form',
      'method' => 'post',
      'enableAjaxValidation' => false,
    ]);
    ?>
    <div class="panel panel-success">
      <div class="panel-heading">Ageega Details<span
          class="pull-right">Receipt Details</span>
      </div>
      <div class="panel-body">
        <div class="col-md-7">
          <div class="row">

            <div class="form-group"
                 style="text-align: right;font-family:Faruma; font-size: 14px; font-weight: 700">
              <div class="col-md-4">
                އަގު
              </div>
              <div class="col-md-2">
                ބަކަރި
              </div>
              <div class="col-md-2">
                ޖިންސު
              </div>
              <div class="col-md-4">
                ނަން
              </div>

            </div>
          </div>
          <hr size="2px" style="margin:0 0 5px">
          <?php
          $total = 0;
          foreach ($transaction->ageega->ageegaChildrens as $child) {
            $amount = $child->sheep_qty * $rates[$child->gender_id];
            $total += $amount;
            ?>
            <div class="row">
              <div class="form-group"
                   style="text-align: right">
                <div class="col-md-4">
                  <?= Helpers::currency($amount) ?>
                </div>
                <div class="col-md-2">
                  <?= $child->sheep_qty ?>
                </div>
                <div class="col-md-2" style="font-family:Faruma">
                  <?= $child->gender->name_dhivehi ?>
                </div>
                <div class="col-md-4" style="font-family:Faruma">
                  <?= $child->full_name_dhivehi ?>
                </div>
              </div>
            </div>
            <hr style="margin:0 0 5px">
          <?php } ?>
          <div class="row">
            <div class="form-group"
                 style="text-align: right;font-size: 14px; font-weight: 700">
              <div class="col-md-4">
                <?= Helpers::currency($total) ?>
              </div>
              <div class="col-md-4 col-md-offset-4" style="font-family:Faruma">
                ޖުމްލަ
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-5 pull-right">
          <?php
          if ($transaction->is_cancelled) {
            echo '<span class="badge badge-important pull-right"
              style="margin-bottom:3px;">Transaction Cancelled</span>';
          }
          ?>
          <div class="input-group">
            <?php
            echo $form->label($transaction, 'transaction_time', [
              'class' => 'control-label input-group-addon',
              'style' => 'width:40%;margin-top:5px']);

            if (!$viewOnly) {
              Yii::import('ext.CJuiDateTimePicker.CJuiDateTimePicker');
              $this->widget('CJuiDateTimePicker', [
                'model' => $transaction,
                'attribute' => 'transaction_time',
                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                'options' => [
                  'dateFormat' => Constants::DATE_DISPLAY_FORMAT,
                ], // jquery plugin options
                'language' => '',
                'htmlOptions' => [
                  'class' => 'form-control',
                  'disabled' => ($transaction->is_cancelled ? 'disabled' : ''),
                ]
              ]);
            } else
              echo '<span class="form-control">' .
                (new DateTime($transaction->transaction_time))
                  ->format('d F Y H:m:s') .
                '<icon class="glyphicon glyphicon-ban-circle pull-right"
                    style="color:red"></icon></span>';

            ?>
          </div>
          <div class="input-group">
            <?php
            echo $form->label($transaction, 'amount', [
              'class' => 'control-label input-group-addon',
              'style' => 'width:40%;margin-top:5px']);
            echo '<span class="form-control">' .
              Helpers::currency($transaction->amount) .
              '<icon class="glyphicon glyphicon-ban-circle pull-right"
                style="color:red"></icon></span>';
            ?>
          </div>
          <div class="input-group">
            <?php
            echo $form->label($transaction, 'transaction_medium_id', [
              'class' => 'control-label input-group-addon',
              'style' => 'width:40%;margin-top:5px']);
            if (!$viewOnly)
              echo $form->dropDownList($transaction,
                'transaction_medium_id',
                CHtml::listData(ZTransactionMediums::model()->findAll(), 'id',
                  'name_english'), ['class' => 'form-control',
                  'disabled' => ($transaction->is_cancelled ? 'disabled' : ''),
                ]);
            else
              echo '<span class="form-control">' .
                $transaction->transactionMedium->name_english .
                '<icon class="glyphicon glyphicon-ban-circle pull-right"
                style="color:red"></icon></span>';

            ?>
          </div>
          <?php
          if (!$viewOnly && !$transaction->is_cancelled) {
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

          <?php if (!$transaction->is_cancelled && !$viewOnly) { ?>
            <div class="pull-right" style="margin-bottom: 5px">
              <a
                href="<?php echo $this->createUrl
                ('ageega/reviseAgeegaTransaction',
                  ['id' => $transaction->transaction_id,
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



