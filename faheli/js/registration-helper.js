/* 
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from ... in writing.
 */
alert('test');
function copyPermAddress() {
    permIslandId = $('#ApplicationFormsHelper_perm_address_island_id').val();
    permHouseEng = $('#ApplicationFormsHelper_perm_address_english').val();
    permHouseEng = $('#ApplicationFormsHelper_perm_address_dhivehi').val();

    $.get("<?php ?>", function(data) {
        $(".result").html(data);
        alert("Load was performed.");
    });

}


