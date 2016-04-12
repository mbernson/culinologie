/** Reviews in RecipesController@show **/
$('#rating-switch').click(function(){
    if ($('input[name=rating]').prop('disabled')) {
        $('input[name=rating]').prop('disabled', '');
        $(this).toggleClass('btn-danger'); $(this).toggleClass('btn-primary');
        $(this).html('On');
    }
    else {
        $('input[name=rating]').prop('disabled', 'disabled');
        $(this).toggleClass('btn-danger'); $(this).toggleClass('btn-primary');
        $(this).html('Off');
    }
});
