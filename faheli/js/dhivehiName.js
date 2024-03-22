/**
 * Created by user on 3/12/2016.
 */

$('#ApplicationFormsHelper_applicant_full_name_english').on('change',
function () {
  $.ajax({
    url: '<?=Yii::app()->createUrl('helper/dhivehiName')?>',
  dataType: 'json',
  data: {
    'q': $('#ApplicationFormsHelper_applicant_full_name_english')
    .val()
  },
  success: function (data) {
    $('#ApplicationFormsHelper_applicant_full_name_dhivehi')
    .val(data.dhivehiName);
    if (data.dhivehiName.indexOf("?") == -1)
      $('#ApplicationFormsHelper_applicant_full_name_dhivehi')
      .removeClass('error');
    else
      $('#ApplicationFormsHelper_applicant_full_name_dhivehi')
      .addClass('error');
  }
})
})
$('input').on('change', function () {
  $(this).removeClass('error')
})
