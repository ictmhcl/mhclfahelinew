<div
  style="border:1px solid #ccc;margin-left:auto; margin-right:auto; width:120px;height:120px;vertical-align: central; text-align: center">
  <table>
    <tr>
      <td style="width:120px;height:120px;">
        <?php
        $latestPassport = $model->person->latestPassport;
        if (!empty($model->person->photoUrl)) {
          echo CHtml::image($model->person->photoUrl,
            $model->person->full_name_english,
            [
              'style' => 'max-height:120px;max-width:120px;'
            ]);
        } else {
          if (!empty($model->person->gender_id)) {
            $dummyPhotoColor = $model->person->gender_id == 1 ? 'blue' : 'hotpink';
            ?>
            <span
              class="fa fa-<?php echo strtolower($model->person->gender->name_english) ?>"
              style="font-size:40px;color:<?= $dummyPhotoColor ?>"></span>
            <br><br>
            <?php
          } else {
            ?>
            <span class="fa fa-ban"
                  style="font-size:40px;color:lightgrey"></span><br>
            <br>
            <?php
          }
        }
        ?>
<!--        --><?php
//        if (!empty($model->applicationForm->passport_photo)) {
//          echo CHtml::image(Helpers::sysUrl(Constants::UPLOADS) . $model->applicationForm->passport_photo, $model->person->full_name_english, [
//            'style' => 'max-height:120px;max-width:120px;'
//          ]);
//        } else {
//          if (!empty($model->person->gender_id)) {
//            $dummyPhotoColor = $model->person->gender_id == 1 ? 'blue' : 'hotpink';
//            ?>
<!--            <span-->
<!--              class="fa fa---><?php //echo strtolower($model->person->gender->name_english) ?><!--"-->
<!--              style="font-size:40px;color:--><?//= $dummyPhotoColor ?><!--"></span><br>-->
<!--            <br>-->
<!--            --><?php
//          } else {
//            ?>
<!--            <span class="fa fa-ban"-->
<!--                  style="font-size:40px;color:lightgrey"></span><br><br>-->
<!--            --><?php
//          }
//          ?>
<!--          <span class="screen-only">-->
<!--            <a-->
<!--              href="--><?php //echo $this->createUrl('documentUpload', ['id' => $model->id]); ?><!--"-->
<!--              class="btn btn-xs btn-primary">Upload Photo</a>-->
<!--          </span>-->
<!--          --><?php
//        }
//        ?>
      </td>
    </tr>
  </table>
</div>