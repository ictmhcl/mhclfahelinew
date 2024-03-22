<?php
/* @var $this AgeegaController */
/* @var $model UmraPilgrims */
/* @var $ageega Ageega */
/* @var $children AgeegaChildren[] */
/* @var $sheepQty Integer */
?>

<h3>
    <span style="color:green">
    <?=$this->person->{H::tf('full_name_dhivehi')} . ' (' . $this->person->id_no
    . ') - '?> <?=H::t('ageega', 'payForAgeega')?></span>
</h3><ul class="nav nav-tabs small">

</ul>

<div class="tab-content">
  <div class="form ">

    <?php
    $rates = Helpers::getCurrentAgeegaRates();
    ?>
    <div class="panel panel-success">
      <div class="panel-heading"><?=H::t('act','details')?></div>
      <div class="panel-body">
        <?php if ($ageega->ageega_reason_id == Constants::AGEEGA_REASON_CHILDREN_NAMING) { ?>
        <div class="col-md-7">
          <div class="row">

            <div class="form-group"
                 style="font-size: 14px; font-weight:
                 700;<?=H::t('site','txtAlign')?>">
              <div class="col-xs-4">
                <?=H::t('ageega','name')?>
              </div>
              <div class="col-xs-2">
                <?=H::t('site', 'gender_id')?>
              </div>
              <div class="col-xs-2">
                <?=H::t('ageega', 'sheepQty')?>
              </div>
              <div class="col-xs-4">
                <?=H::t('ageega', 'price') . ' (' . H::t('hajj',
                  'currencySymbol'). ')'?>
              </div>

            </div>
          </div>
          <hr size="2px" style="margin:0 0 5px">
          <?php
          $total = 0;
          if (!empty($children) && $ageega->ageega_reason_id ==
            Constants::AGEEGA_REASON_CHILDREN_NAMING)
          foreach ($children as $child) {
            $amount = $child->sheep_qty * $rates[$child->gender_id];
            $total += $amount;
            ?>
            <div class="row">
              <div class="form-group"
                   style="<?=H::t('site', 'txtAlign')?>">
                <div class="col-xs-4">
                  <?= $child->{H::tf('full_name_dhivehi')} ?>
                </div>
                <div class="col-xs-2">
                  <?= $child->gender->{H::tf('name_dhivehi')} ?>
                </div>
                <div class="col-xs-2">
                  <?= $child->sheep_qty ?>
                </div>
                <div class="col-xs-4">
                  <?= Helpers::currency($amount) ?>
                </div>
              </div>
            </div>
            <hr style="margin:0 0 5px">
          <?php } ?>
          <div class="row">
            <div class="form-group"
                 style="<?=H::t('site', 'txtAlign')?>;font-size: 14px; font-weight: 700">
              <div class="col-md-4 col-md-offset-8">
                <?= Helpers::currency($total) ?>
              </div>
            </div>
          </div>
        </div>
        <?php } else {
          $total = $sheepQty * $rates[Constants::GENDER_FEMALE];
          ?>
          <div class="col-md-7">
            <div class="row">

              <div class="form-group"
                   style="<?=H::t('site', 'txtAlign')?>;font-size: 14px;
                     font-weight: 700">
                <div class="col-xs-4">
                  <?=H::t('ageega', 'ageegaType')?>
                </div>
                <div class="col-xs-2">
                  <?=H::t('site', 'sheepQty')?>
                </div>
                <div class="col-xs-4">
                  <?=H::t('ageega', 'Price').' ('.H::t('hajj',
                    'currencySymbol').')'?>)
                </div>

              </div>
            </div>
            <hr size="2px" style="margin:0 0 5px">
                <div class="row">
                  <div class="form-group"
                       style="<?=H::t('site', 'txtAlign')?>">
                    <div class="col-xs-4">
                      <?=$ageega->ageegaReason->{H::tf('name_dhivehi')}?>
                    </div>
                    <div class="col-xs-2">
                      <?=$sheepQty?>
                    </div>
                    <div class="col-xs-4">
                      <?=Helpers::currency($total)?>
                    </div>
                  </div>
                </div>
                <hr style="margin:0 0 5px">
          </div>
        <?php } ?>
      </div>
      <div class="panel-body">
        <div class="form-group">
          <div class="col-md-4">
            <span class="btn btn-success btn-sm onlinPayBtn"
              <?=$this->processPayments
                ? 'onclick="javascript:$(\'.onlinePay\').slideToggle()"' : ''?>>
              <?=H::t('hajj', 'payOnline')?> <?=!$this->processPayments
                ? H::t('hajj', 'temporarilyDown') : ''?>
            </span>
          </div>
        </div>

      </div>
      <?php if ($this->processPayments) {

        $childrenArray = [];
        if (!empty($children) && $ageega->ageega_reason_id ==
          Constants::AGEEGA_REASON_CHILDREN_NAMING){
          foreach($children as $child)
            $childrenArray[] = $child->attributes;
        }

        $fixedAmount = $total;

        $payload = CJSON::encode([
          'ageega' => $ageega->attributes,
          'sheepQty' => $sheepQty,
          'children' => $childrenArray
        ]);
        $paymentTypeId = Constants::ONLINE_PAYMENT_AGEEGA;

        include_once(Yii::getPathOfAlias('webroot')
          . "/js/process-payments.php");
      } ?>


    </div>

  </div><!-- form -->
</div>



