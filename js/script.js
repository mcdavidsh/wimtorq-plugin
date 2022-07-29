$ = jQuery;
$(document).ready(function() {
    $('#movietable').dataTable({
        "processing": true,
        "serverSide": false,
    } );
} );