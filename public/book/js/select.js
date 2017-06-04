$(document).ready(function() {
    var outBoundTable = $('#outbound-table').DataTable({
        searching: false,
        lengthChange:false,
        ordering: false,
        PaginationType:"full_numbers",
        language:{
            "sInfo": "Showing _START_ to _END_ of _TOTAL_ Flights",
        },
        fnDrawCallback: function(oSettings) {
            if ((oSettings._iDisplayLength+1) > oSettings.fnRecordsDisplay()) {
                $(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
            }
        },
        columnDefs: [
            { className: "dt-right", "targets": [1] },
        ]
    });

    $('#outbound-table tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            outBoundTable.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    } );

    if($('#return-table').length==1){
        var returnTable = $('#return-table').DataTable({
            searching: false,
            lengthChange:false,
            ordering: false,
            PaginationType:"full_numbers",
            language:{
                "sInfo": "Showing _START_ to _END_ of _TOTAL_ Flights",
            },
            fnDrawCallback: function(oSettings) {
                if ((oSettings._iDisplayLength+1) > oSettings.fnRecordsDisplay()) {
                    $(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
                }
            },
            columnDefs: [
                { className: "dt-right", "targets": [1] },
            ]
        });

        $('#return-table tbody').on( 'click', 'tr', function () {
            if ( $(this).hasClass('selected') ) {
                $(this).removeClass('selected');
                $('#return-flight').val('');
            }
            else {
                returnTable.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
        } );
    }


    $('#submit').on( 'click',  function () {
        var valid = false;
        var message = '';
        if($('#outbound-table').length==1){
            var index = $('#outbound-table tr.selected .current-row').val();
            if(typeof index == "undefined"){
                message = 'Please Select the Outbound Flight';
            }
            $('#outbound-flight').val(index);
        }
        if($('#return-table').length==1){
            var index = $('#return-table tr.selected .current-row').val();
            if(typeof index == "undefined"){
                message = 'Please Select the Return Flight';
            }
            $('#return-flight').val(index);

        }

        if(message!=""){
            $('.alert-message').html(message);
            $('#alert-modal').modal();
        }else{
            $('#go').click();
        }
    });
} );