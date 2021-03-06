$(document).ready(function() {
    // hide alert message if javascript disabled
    $('.alert-javascript').remove();

    //  Not mandetory field showing/hidding in event creation

    // Hide not mandatory field
    $( '.not-mandetory' ).each(function(textInput, textTextarea) {
        textInput = $( this ).find('input').val();
        textTextarea = $( this ).find('textarea').val();
        if (textInput == '' || textTextarea == '') {
            // If field has value, its form submited form
            // If field doesn't have value, hide it
            $( this ).find('.control-group').css('display', 'none');
        }
    });

    // Shows and hides not mandatory fields for event creation
    $( '.not-mandetory' ).each(function(title) {
        // Need to remember fields title when showing it again
        title = $( this ).find( '.well p' ).text();
        // On click show field
        $( this ).find( '.well' ).click(function() {
            display = $( this ).parent().find('.control-group').css('display');
            if (display == 'none') {
                // Field is hidden, show it
                $( this ).parent().find('.control-group').css('display', 'block');
                // Change show buttons text
                $( this ).find('p').text('Paslēpt');
            }
            else {
                // Field is visible, hide it
                $( this ).parent().find('.control-group').css('display', 'none');
                // Give field its original title
                $( this ).find('p').text(title);
            }
        });
    });

    // Deleting all values for not mandetory fields whitch user chose to not add
    $('#event').submit(function() {
        // Check each not mandetory field
        $( '.not-mandetory' ).each(function() {
            // Check if field is hidden
            display = $( this ).find('.control-group').css('display');
            if (display == 'none') {
                // Field is hidden, delete its value
                $( this ).find('input').val('');
                $( this ).find('textarea').val('');
            }
        });
    });

    // Hide comments and show them on click
    $('.comments ul').css({
        visibility: 'hidden',
        display: 'none'
    });

    $('.comment-heading-wrapper').each(function() {
        var comments = $(this).parent().find('ul');
        $(this).click(function() {
            if (comments.css('visibility') == 'hidden') {
                comments.css ({
                    visibility: 'visible'
                });
                comments.animate({
                    height: 'toggle'
                }, 'fast');
            }
            else {
                comments.animate({
                    height: 'toggle'
                }, 'fast', function(){
                    comments.css ({
                        visibility: 'hidden'
                    });
                });
            }
        });
    });

    // show "..." on long description on home page event descriptions
    $('.event-thumb').each(function() {
        var desc = $(this).find('p');
        if (desc.text().length > 348) {
            var index = desc.text().lastIndexOf(' ', 348);
            var newString = desc.text().substring(0, index);
            newString += ' ...';
            desc.text(newString);
        }
    });
});

// Add new tag to hidden input element for creating new tags
function addTag(tag, index) {
    var input = $('#new-tags');
    input.val(input.val() + ', ' + tag);
    var a = $('#tag-' + index);
    a.attr('onclick', 'return false;');
    a.find('span').removeClass('label-success').text('Birka pievienota pasākumam');
    a.css('cursor', 'default');
}