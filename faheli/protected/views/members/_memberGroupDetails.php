<?php
/* @var $model Members */
// all members in the group
if (!empty($model->group_name)) {
    $criteria = new CDbCriteria();
    $criteria->addColumnCondition(['group_name' => $model->group_name]);
    $criteria->addNotInCondition('id', [$model->id]);
    /** @var Members[] $groupMembers */
    $groupMembers = Members::model()->findAll($criteria);

    foreach ($groupMembers as $member) {
        echo CHtml::link($member->person->idName, Yii::app()
          ->createUrl('members/view', ['id' => $member->id]));
        echo '<br/>';
    }
}
