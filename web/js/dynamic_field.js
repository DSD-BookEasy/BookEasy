$(function(){
    $('#dynamic_add').click(function(ev){
        ev.preventDefault();

        var $copied = $('.dynamic_field:last').clone();
        $('input',$copied).val('');//Empty the fields
        $('.input-group.date',$copied).each(function(){
            $(this).datetimepicker({
                autoclose: true
            });
        });

        //Timeslots array indexing management
        var n=/Timeslot\[(\d+)\]/.exec($('input',$copied).first().attr('name'))[1];
        n++;
        $('input',$copied).first().attr('name','Timeslot[' + n + '][start]');


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
        var val=new Date($t.val());
        //Calculate the moment of the end
        val.setMinutes(val.getMinutes() + simulationDuration);

        var $container = $t.closest('.dynamic_field');
        console.log($container);
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
            autoclose: true
        });
    });
});

function calendarAddTimespan(){
    //$('#dynamic_add').click();

}