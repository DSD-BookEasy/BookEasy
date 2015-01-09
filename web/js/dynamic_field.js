$(function(){
    $('#dynamic_add').click(function(ev){
        ev.preventDefault();

        var $copied = $('.dynamic_field:first-child').clone(true);
        $('input',$copied).val('');//Empty the fields
        $copied.append('<div class="col-md-2"><a href="#" class="btn btn-warning glyphicon glyphicon-remove dynamic_remove"></a></div>');//Add the delete button

        $(this).before($copied);//Put the copied fields above the add button
    });

    $(document).on('click','.dynamic_remove',function(ev){
        ev.preventDefault();

        $(this).closest('.dynamic_field').remove();
    });
});

function calendarAddTimespan(){

}