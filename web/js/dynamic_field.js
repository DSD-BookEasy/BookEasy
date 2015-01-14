$(function(){
    $('#dynamic_add').click(function(ev){
        ev.preventDefault();

        var $copied = $('.dynamic_field:last').clone();
        $('input',$copied).val('');//Empty the fields
        $('.input-group.date',$copied).each(function(){
            $(this).datetimepicker({
                autoclose: true,
                startDate: new Date(),
                minuteStep: 30
            });
        });

        //Timeslots array indexing management
        var n=/Timeslot\[(\d+)\]/.exec($('input',$copied).first().attr('name'))[1];
        n++;
        $('.picker_start',$copied).attr('name','Timeslot[' + n + '][start]');
        $('.picker_end',$copied).attr('name','Timeslot[' + n + '][end]');


        $copied.append('<div class="col-md-2"><a href="#" class="btn btn-warning glyphicon glyphicon-remove dynamic_remove"></a></div>');//Add the delete button


        $(this).before($copied);//Put the copied fields above the add button
    });

    $(document).on('click','.dynamic_remove',function(ev){
        ev.preventDefault();

        $(this).closest('.dynamic_field').remove();
    });

    $(document).on('change','.picker_start',function(ev){
        ev.preventDefault();

        var $t=$(this);

        var splitVal = $t.val().split(' ');
        var splitDate  = splitVal[0].split('-');
        var splitHour = splitVal[1].split(':');

        var val=new Date(splitDate[0],splitDate[1],splitDate[2],splitHour[0],splitHour[1]);
        //Calculate the moment of the end
        val.setMinutes(val.getMinutes() + simulationDuration);

        var $container = $t.closest('.dynamic_field');
        $('input:not(.picker_start)', $container).val(
            val.getFullYear()+"-"+
            (val.getMonth()+1)+"-"+
            val.getDate()+" "+
            val.getHours()+":"+
            val.getMinutes());
    });

    //Enable datetimepicker for initial existing fields
    jQuery('.picker_input').each(function() {
        $(this).parent().datetimepicker({
            autoclose: true,
            startDate: new Date(),
            minuteStep: 30
        });
    });
});

function calendarAddTimespan(start, end, jsEvent, view){
    var diff=end.diff(start)/1000;//Seconds of difference
    if(diff<=(simulationDuration*60*1000)){//Half an hour selected. Convert into simulation duration
        end = start.clone();
        end = end.add(simulationDuration, 'minutes');
    }

    var $timespan = $('.dynamic_field:last');
    if ($('.picker_start',$timespan).val() != '') {
        $('#dynamic_add').click();
        $timespan = $('.dynamic_field:last');
    }

    $('.picker_start',$timespan).val(start.format('YYYY-MM-DD HH:mm'));
    $('.picker_end',$timespan).val(end.format('YYYY-MM-DD HH:mm'));
}