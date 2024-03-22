<div class="input-group">
  <div class="input-group-addon" style="width:20%">
    Personal Details
  </div>
  <div class="form-control" style="height:42px;overflow:hidden;">
    <?php if (in_array("editPilgrim", Helpers::perms())) { ?>
      <span class="screen-only">
              <a href="<?= $this->createUrl('editPilgrim',
                ['id' => $pilgrim->id]) ?>"
                 class="pull-right">
                <icon class="glyphicon glyphicon-pencil"></icon>
              </a>

                </span>
    <?php } ?>
    <?= "<strong>" . $pilgrim->person->personText . "</strong>" ?>
  </div>
</div>
<div class="input-group">
  <div class="input-group-addon" style="width:20%">
    Contact Info
  </div>
  <div class="form-control" style="height:42px;overflow:hidden;">
    <?= "<strong>" . implode(", ", array_filter([$pilgrim->phone_number,
      $pilgrim->email_address])) . "</strong>" ?>
  </div>
</div>
<?php if (!empty($pilgrim->group_name)) { ?>
  <div class="input-group">
    <div class="input-group-addon" style="width:20%">
      Group Info
    </div>
    <?php
    $criteria = new CDbCriteria();
    $criteria->compare('group_name', $pilgrim->group_name);
    $criteria->compare('umra_trip_id', $pilgrim->umra_trip_id);
    $criteria->addNotInCondition('id', [$pilgrim->id]);
    /** @var UmraPilgrims[] $groupMembers */
    $groupMembers = UmraPilgrims::model()->findAll($criteria);
    $gmHeight = sizeof($groupMembers) * 18 + 24;
    ?>
    <div class="form-control" style="height:<?= $gmHeight ?>px;
      overflow:hidden;">
      <strong><?= $pilgrim->group_name ?></strong>
      <?php
      foreach ($groupMembers as $groupMember) {
        echo '<br>' .
          CHtml::link($groupMember->person->idName,
            $this->createUrl('', ['id' => $groupMember->id])) .
          ', paid: ' . Helpers::currency($groupMember->account_balance) .
          (!empty($groupMember->mahram) ?
            (', Mahram: ' . $groupMember->mahram->person->id_no) :
            '');
      }
      ?>
    </div>
  </div>
<?php } ?>
<?php if (!empty($pilgrim->mahram) && ($pilgrim->group_name <> $pilgrim->mahram->group_name)) { ?>
  <div class="input-group">
    <div class="input-group-addon" style="width:20%">
      Mahram
    </div>
    <div class="form-control" style="">
      <strong><?= CHtml::link
        ($pilgrim->mahram->person->idName, $this->createUrl('', ['id' => $pilgrim->mahram->id])) ?></strong>
    </div>
  </div>
<?php } ?>
<div class="input-group screen-only">
  <div class="input-group-addon" style="width:20%">
    Documents
  </div>
  <div class="form-control" style="overflow: hidden;height: 70px;">
    <?php
    $docsArray = ['application_form', 'id_copy', 'mahram_document'];
    foreach ($docsArray as $doc) {
      if (!empty($pilgrim->$doc)) {
        echo CHtml::link($pilgrim->getAttributeLabel($doc) .
          ' <icon class="glyphicon glyphicon-new-window"></icon>',
          Helpers::sysUrl(Constants::UPLOADS) . $pilgrim->$doc,
          ['target' => '_blank']);
        echo '<br/>';
      }
    }
    ?>
  </div>
</div>
