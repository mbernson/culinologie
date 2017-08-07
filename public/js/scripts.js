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

$('.deleteComment').click(function(){
    var url = $(this).data('url');
    var token = $('input[name=_token]').val();
    $.ajax({
        url: url,
        type: 'DELETE',
        data: {_token: token},
        success: function(result) {
            location.reload();
        }
    });
});
