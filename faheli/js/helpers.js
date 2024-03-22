/*
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from ... in writing.
 */
$(document)
        .on('change', '.btn-file :file', function() {
          var input = $(this),
                  numFiles = input.get(0).files ? input.get(0).files.length : 1,
                  label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
          input.trigger('fileselect', [numFiles, label]);
        });

$(document).ready(function() {
  $('.btn-file :file').on('fileselect', function(event, numFiles, label) {

    var input = $(this).parents('.input-group').find(':text'),
            log = numFiles > 1 ? numFiles + ' files selected' : label;

    if (input.length) {
      input.val(log);
    } else {
      if (log)
        alert(log);
    }

  });
});

function blockUI() {
  document.body.style.overflow = "hidden";
  $.msg({
    // autoUnblock:9000,
    timeOut: 20000,
    fadeIn: 100,
    clickUnblock: false,
    content: 'Loading ...',
    bgPath: '/mhc/images/'
  });
}
;

var turnBlockOn = true;

$('a, .btn').click(function(e) {
turnBlockOn=false});

function showFlash(fkey, fmsg, stay) {
    stay = typeof stay !== 'undefined' ? stay : 0;
    flasher = $('#flasher');
    flasher.html((fkey=='hide')?'':'<div class="alert alert-'+fkey+' alert-flash flash-info">'+fmsg+'</div>');
    flasher.show();
//    if (fkey=='error') stay=0;
    if (stay!=0)
        flasher.animate({opacity: 1.0}, stay*1000).slideUp("slow");
    //$('html,body').animate({scrollTop: flasher.offset().top},'slow');
}

$(document).ready(function() {
  $.each($('input[inputType]'), function(key, inputField) {
    $(inputField).attr('type',$(inputField).attr('inputType'))
  });
})

