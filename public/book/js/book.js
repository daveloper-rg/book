var child_warning = true;
var BookAutomation = {


    /**
     * Init method
     */
    init: function (  ) {
        //Init additional global method
        this.initRoutes();
        this.initPassengers();
        this.initFormValidation();
    },
    initRoutes:function(){
        var $this = this;
        var $arrivals = $('#arrival');
        $('#departure').change(function () {
            $('#departure option:not([value])').remove();
            var departure = $(this).val(), lcns = routes[departure] || [];

            var html = $.map(lcns, function(lcn){
                return '<option value="' + lcn + '">' + routes_info[lcn].label + '</option>'
            }).join('');
            $arrivals.html(html);
            $arrivals.prop('disabled',false);
            $this.setScheduledDates();
        });

        $('#arrival').change(function () {
            $this.setScheduledDates();
        });

        $('#flight-type').on("click",function(){
            $('#flight-type .active > input[type="radio"]').prop('checked',true);
            setTimeout(function(){
                if($('#one-way').is(':checked')){
                    $('#return-date').val('');
                    $('#return-date').prop('disabled',true);
                }else{
                    $('#return-date').prop('disabled',false);
                }
            }, 400);


        })
    },
    /**
     * Init DatePickers
     */
    initDatePickers: function(departuresDates,returnDates)
    {

        var date = new Date();
        var today = moment(date).format('YYYY-MM-DD');

        $datePicker = $('#departure-date').data('DateTimePicker');
        if(typeof $datePicker != "undefined"){
            $datePicker.destroy();
        }
        $('#departure-date').datetimepicker({
            ignoreReadonly: true,
            enabledDates:departuresDates,
            format: 'DD/MM/YYYY',
            minDate: today
        });

        $('#departure-date').data('DateTimePicker').clear();

        $datePicker = $('#return-date').data('DateTimePicker');
        if(typeof $datePicker != "undefined"){
            $datePicker.destroy();
        }
        $('#return-date').datetimepicker({
            ignoreReadonly: true,
            enabledDates:returnDates,
            format: 'DD/MM/YYYY',
            minDate: today,
            useCurrent: false
        });

        $('#return-date').data('DateTimePicker').clear();

        // Set Styles
        $('td.day:not(.disabled)').removeClass('calendar-day');



        $('#departure-date').add('#return-date').on("dp.show", function (e) {
            highlightCalendar();
        });
        $('#departure-date').add('#return-date').on("dp.update", function (e) {
            highlightCalendar();
        });

        //linked date pickers

        $("#departure-date").on("dp.change", function (e) {
            $('#return-date').data("DateTimePicker").minDate(e.date);
        });
        $("#return-date").on("dp.change", function (e) {
            $('#departure-date').data("DateTimePicker").maxDate(e.date);
        });

    },
    initPassengers: function(){
        $(document).on('click','.value-control',function(){
            var action = $(this).attr('data-action');
            var target = $(this).attr('data-target');
            var value  = parseFloat($('[id="'+target+'"]').val());

            if ( action == "plus" ) {
                value++;
            }
            if ( action == "minus" ) {
                value--;
            }
            if(value==-1){
                return false;
            }

            $('[id="'+target+'"]').val(value + ' ' + (value > 1 ? $('[id="'+target+'"]').attr('data-label') : $('[id="'+target+'"]').attr('data-label-one')));
            if(parseFloat($('#adults').val())==0 && parseFloat($('#children').val())>0 && child_warning){
                var message = 'Children between 0 and 4 years of age must always fly accompanied by an adult.';
                $('.alert-message').html(message);
                $('#alert-modal').modal();
                child_warning = false;
            }
            return false;
        })
    },
    setScheduledDates: function(){
        var departure = $('#departure').val();
        var arrival = $('#arrival').val();
        if(!departure || !arrival){
            return;
        }
        $this = this;
        var data = {'departure':departure,'arrival':arrival};
        $.ajax({
            type: 'post',
            url: '/book/schedule',
            data: data,
            dataType: 'json',
            async: true,
            success: function (data) {
                var departuresDates = data.departure_dates;
                var returnDates = data.return_dates;
                $this.initDatePickers(departuresDates,returnDates);
            },
            complete:function(){
            }
        });
    },
    initFormValidation: function(){
        $('#submit').on("click",function(){
            var message = ValidateForm();
            if(message!=""){
                $('.alert-message').html(message);
                $('#alert-modal').modal();
            }else{
                $('#go').click();
            }
        });


    }

};

function ValidateForm(){
    var message = '';
    if($('#departure').val()==""){
        message = 'Before continuing, you must select your origin.';
    }
    if(message == "" && $('#arrival').val()==""){
        message = 'Before continuing, you must select your destination.';
    }
    if(message == "" && $('#departure-date').val()==""){
        message = 'Before continuing, you must select your Outbound Date.';
    }
    if(message == "" && $('#return-date').val()=="" && $('#round-trip').is(':checked')){
        message = 'Before continuing, you must select your Return Date.';
    }

    if(message == "" && parseFloat($('#adults').val())==0 && parseFloat($('#children').val())==0){
        message = 'Before continuing, please select the number of passengers that are going to fly. Please choose again.';
    }

    if(message == "" && parseFloat($('#adults').val())==0 && parseFloat($('#babies').val())>0){
        message = 'Children between 0 and 4 years of age must always fly accompanied by an adult.';
    }

    return message;

}

function highlightCalendar(){
    $('td.day:not(.disabled)').wrapInner( "<a></a>");
    $('td.day:not(.disabled)').addClass('calendar-day');
}

