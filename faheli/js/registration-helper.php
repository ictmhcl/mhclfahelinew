<?php
/*
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from ... in writing.
 */
?>
<script>
  function getId() {
    url='<?=$this->createUrl('create')?>?get_id='+$('#get_id').val();
    location.href = url;
  }

  $('#full_name_english').on('change',
    function () {
      $.ajax({
        url: '<?=Yii::app()->createUrl('helper/dhivehiName')?>',
        dataType: 'json',
        data: {
          'q': $('#full_name_english')
            .val()
        },
        success: function (data) {
          $('#full_name_dhivehi')
            .val(data.dhivehiName);
          if (data.dhivehiName.indexOf("?") == -1)
            $('#full_name_dhivehi')
              .removeClass('error');
          else
            $('#full_name_dhivehi')
              .addClass('error');
        }
      })
    })
  $('input').on('change', function () {
    $(this).removeClass('error')
  })

</script>
