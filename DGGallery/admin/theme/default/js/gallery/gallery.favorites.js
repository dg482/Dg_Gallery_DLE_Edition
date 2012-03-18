gallery.favorites = {

    }

$(this).keypress(function(event) {
    if ( event.which == 13 ) {
        activateAjaxDialog('add','$id',$('#new_tag').val());
    }
})

