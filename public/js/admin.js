/**
 * Admin application JS
 */
jQuery( function( $ ) {
    // datepickers
    //
    $( '.datepicker' ).pikaday({
        format: 'M/D/YYYY'
    });

    var $imageInput = $( '#image-input' );

    if ( $imageInput.length ) {
        var $imagePreview = $( '#image-preview' );

        function readURL ( input ) {
            if ( input.files && input.files[ 0 ] ) {
                var reader = new FileReader();
                reader.onload = function ( e ) {
                    $imagePreview.find( 'img' ).attr( 'src', e.target.result );
                    $imagePreview.find( 'span' ).hide();
                }
                reader.readAsDataURL( input.files[ 0 ] );
            }
        }
        
        $imageInput.change( function () {
            readURL( this );
        });
    }

    // save button
    //
    $( '#save-article' ).on( 'click', function () {
        $( 'form#edit-form' ).submit();
    });

    // delete button
    //
    $( '#delete-post' ).on( 'click', function () {
        var $this = $( this );
        $this.hide();
        $this.next().show();

        var revert = function () {
            var $reallyDelete = $( '#really-delete-post' );
            $reallyDelete.hide();
            $reallyDelete.prev().show();
        };

        var timeoutId = window.setTimeout( revert, 5000 );
    });
});