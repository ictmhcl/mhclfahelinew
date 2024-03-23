<?php
/* @var $this AgeegaController */
/* @var $ageega Ageega */
/* @var $ageegaReasons array */
/* @var $children AgeegaChildren[] */
/* @var $transaction AgeegaTransactions */
/* @var $docsArray string[] */
/* @var $form CActiveForm */

$clientScriptUrl = Yii::app()->request->baseUrl . "/js/jtk-4.2.1.js";
$clientScript = Yii::app()->clientScript;
$clientScript->registerScriptFile($clientScriptUrl, CClientScript::POS_HEAD);

$jsonChildren = [];
foreach ($children as $child) {
  $jsonChild = [];
  foreach ($child->attributes as $key => $value)
    $jsonChild[$key] = [
      'value' => $child->$key, 'error' => $child->hasErrors($key)
    ];

  $jsonChildren[] = $jsonChild;
}

$sheepQty = empty($sheepQty)?0:$sheepQty;
?>
<style>
  td>.form-control {
    padding: 2px;
  }
</style>
<h3>
    <span style="color:green">
    <?=$this->person->{H::tf('full_name_dhivehi')} . ' (' . $this->person->id_no
    . ') - '?> <?=H::t('ageega', 'registerForAgeega')?></span>
</h3>

<div class="tabs">
  <ul class="nav nav-tabs small"></ul>
</div>

<div class="tab-content">

  <div class="form small">

    <?php
    $form = $this->beginWidget('CActiveForm', [
      'id' => 'ageega-registration-form', 'enableAjaxValidation' => false,
      'htmlOptions' => [
        'class' => 'form-horizontal', 'enctype' => 'multipart/form-data'
      ]
    ]);
    ?>

    <?php echo $form->errorSummary([$ageega], H::t('site','inputErrors'),
      null, [
        'style' => 'margin:5px;', 'class' => 'alert alert-danger'
      ]); ?>
    <div class="panel panel-success">
      <div class="panel-heading"><?=H::t('ageega','ageegaType')?></div>
      <div class="panel-body">
        <?=$form->label($ageega, 'reason', ['class' => 'control-label col-md-2'])?>
        <div class="col-md-4" style="margin-bottom: 5px">
          <?=$form->dropDownList($ageega, 'ageega_reason_id', $ageegaReasons, [
            'class' => 'form-control','id'=>'ageega_reason', 'onChange' => '
              javascript:
                if ($(this).val() == 1) {
                  $(".otherSheepQty").val("");
                  $(".childrenDetails").slideDown();
                  $(".sheepQty").slideUp();
                  $("#form_label").html("'.H::t('ageega','formDocLabelWithChildren').' <span class=\'required\'>*</span>");
                } else {
                  $(".childSheepQty").val("");
                  $(".childrenDetails").slideUp();
                  $(".gender").val(1);
                  $(".sheepQty").slideDown();
                  $("#form_label").html("'.H::t('ageega','formDocLabel').' <span class=\'required\'>*</span>");
                }
                updatePrice();
            '
          ])?>

        </div>
        <div class="col-md-4 col-md-offset-2">
          <div class="input-group" style="width: 100%; margin-bottom: 5px">
            <div class="input-group-addon" style="width: 40%">
              <?=H::t('ageega','due')?>
            </div>
              <span class="form-control">
                <?=Yii::app()->language!='dv'?H::t('hajj','currencySymbol'):''?>
                <span class="price">0.00</span>
                <?=Yii::app()->language=='dv'?H::t('hajj','currencySymbol'):''?>
              </span>
          </div>

        </div>
      </div>
      <div class="panel-body sheepQty" style="display: none;">
        <?=CHtml::label(H::t('ageega','sheepQty'), 'sheepQty', ['class' =>
        'control-label col-md-2'])?>
        <div class="col-md-4">
          <input type="number" min="0" name="sheepQty" id="sheepQty"
                 value="<?=$sheepQty?: 0?>"
                 class="form-control otherSheepQty <?=$sheepQtyError?>" />

        </div>
      </div>
    </div>
    <div class="panel panel-success childrenDetails">
      <div class="panel-heading"><?=H::t('ageega','childrenDetails')?></div>
      <div class="panel-body">
        <div class="well well-sm text-danger">
          <strong><?=H::t('ageega','childrenNameNote')?></strong>
        </div>
        <div class="form-group">
          <div class="col-md-12">

            <table id="children-table" class="table table-responsive
              table-striped">
              <thead>
              <th><?=H::t('ageega','name_english')?></th>
              <th><?=H::t('ageega', 'name_dhivehi')?> <span class="required">*</span></th>
              <th><?=H::t('site', 'gender_id')?> <span class="required">*</span></th>
              <th><?=H::t('ageega', 'birthCertificateNumber')?> <span
                  class="required">*</span></th>
              <th><?=H::t('ageega', 'sheepQty')?> <span class="required">*</span></th>
              <th></th>
              </thead>
              <tbody>
              <?php
              $i = 0;
              $rowCount = sizeof($children)>0?sizeof($children)-1:3;
              //region
              while ($i <= 14) {
                ?>
                <tr style="<?=$i <= $rowCount ? "" : "display: none"?>;">
                  <td><?=CHtml::textField('full_name_english[]',
                      (!empty($children[$i]) ? $children[$i]->full_name_english : ''), [
                      'size' => 30, 'maxlength' => 255,
                      'style' => 'direction:ltr',
                      'class' => 'form-control englishName' . ((!empty
                        ($children[$i]) && $children[$i]->hasErrors
                          ('full_name_english'))?' error':''),
                      'data-child-id' => $i,
                      'id' => 'AgeegaChildren_full_name_english' . $i
                    ]); ?></td>
                  <td><?=CHtml::textField('full_name_dhivehi[]',
                      (!empty($children[$i]) ?
                        $children[$i]->full_name_dhivehi : ''), [
                      'id' => 'AgeegaChildren_full_name_dhivehi' . $i,
                      'class' => 'thaanaKeyboardInput form-control dhivehiName'
                        . ((!empty
                          ($children[$i])
                          && $children[$i]->hasErrors('full_name_dhivehi'))
                          ? ' error' : ''),
                      'size' => 30, 'maxlength' => 255, 'style' =>
                        'direction: rtl'
                    ]); ?></td>
                  <td><?=CHtml::dropDownList('gender_id[]', (!empty($children[$i])
                      ? $children[$i]->gender_id : ''), CHtml::listData
                    (ZGender::model()
                      ->findAll(), 'gender_id', H::tf('name_dhivehi')), [
                      'class' => 'form-control gender' . ((!empty
                          ($children[$i])
                          && $children[$i]->hasErrors('gender_id'))
                          ? ' error' : ''),'style'=>'width: 80px',
                      'id' => 'AgeegaChildren_gender_id' . $i
                    ]); ?></td>
                  <td><?=CHtml::textField('birth_certificate_no[]', (!empty($children[$i])
                      ? $children[$i]->birth_certificate_no : ''), [
                      'class' => 'form-control bCert' . ((!empty
                          ($children[$i])
                          && $children[$i]->hasErrors('birth_certificate_no'))
                          ? ' error' : ''), 'style' => 'direction:ltr',
                      'id' => 'AgeegaChildren_birth_certificate_no' . $i
                    ]); ?></td>
                  <td><?=CHtml::textField('sheep_qty[]', (!empty($children[$i])
                      ? $children[$i]->sheep_qty : ''), [
                      'class' => 'form-control childSheepQty' . ((!empty
                          ($children[$i])
                          && $children[$i]->hasErrors('sheep_qty'))
                          ? ' error' : ''), 'type' =>
                        'numeric',
                      'style' => 'direction:ltr', 'inputType' => 'number',
                      'min' => 0,
                      'id' => 'AgeegaChildren_sheep_qty' . $i, 'size' => 2
                    ]); ?></td>
                  <td><?php if ($i != 0) { ?>
                    <a class="btn btn-danger btn-sm removeBtn">
                      <icon class="glyphicon glyphicon-minus"></icon>
                    </a><?php } ?></td>
                </tr>
                <?php
                $i++;
              }

              ?>
              </tbody>
            </table>
          <div class="col-md-2">
            <a class="btn btn-success addBtn">
              <icon class="glyphicon glyphicon-plus"></icon>
              <?=H::t('ageega', 'extraChild')?>
            </a>
          </div>
          </div>
        </div>
        <div class="form-group">
        </div>
      </div>
    </div>
     
    <div class="panel panel-success">
      <div class="panel-footer">
        <div class="form-group">
          <div class="col-md-3">
            <button class="btn btn-primary btn-sm payNowBtn">
              <?=H::t('hajj', 'payAboveAmount')?>
            </button>
          </div>
        </div>
      </div>
    </div>
    <?php $this->endWidget(); ?>

  </div><!-- form -->
</div>
<?php include_once(Yii::getPathOfAlias('webroot')
  . "/js/registration-helper.php"); ?>

<script>
  ageegaChildren = <?=CJSON::encode($jsonChildren)?>;
  $('td').css('padding','2px');
  $('.removeBtn').on('click', function () {
    $(this).parent().parent().remove()
  })
  $('.addBtn').on('click', function() {
    $('#children-table tr').each(function (e) {
      if ($(this).css('display') == "none") {
        $(this).css('display', '');
        return false;
      }
    })
  })
  $('.englishName').on('change', function() {
    var row = $(this).closest('tr');
    var engNameVal = $(this).val();
    var dhiName = row.find('.dhivehiName');
    $.ajax({
      url: '<?=Yii::app()->createUrl('helper/dhivehiName')?>',
      dataType: 'json',
      data: {
        'q': engNameVal
      },
      success: function (data) {
        dhiName.val(data.dhivehiName);
        if (data.dhivehiName.indexOf("?") == -1)
          dhiName.removeClass('error');
        else
          dhiName.addClass('error');
      }
    })

  })
  var sheepPrice = <?=AgeegaRates::model()->findByPk(1)->rate?>;
  $('.childSheepQty, .otherSheepQty').on('change', updatePrice);
  $(document).ready($('#ageega_reason').trigger('change'));
  function updatePrice() {
      var amount = 0;
      $('.childSheepQty').each(function (e) {
        amount = amount + (parseInt($(this).val()) || 0);
      })
      amount = amount + (parseInt($('.otherSheepQty').val()) || 0);
      $('.price').html(numberWithCommas(amount * sheepPrice));
  }
  function numberWithCommas(x) {
    x = x.toString();
    var pattern = /(-?\d+)(\d{3})/;
    while (pattern.test(x))
      x = x.replace(pattern, "$1,$2");
    return x + '.00';
  }
</script>

