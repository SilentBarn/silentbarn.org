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

    // callback to add a new autocomplete slug
    //
    var saveSlug = _.throttle( function ( event, suggestion ) {
        var $elt = $( event.currentTarget );
        var fieldName = $elt.data( 'field' );
        var $slug = jQuery( '<span/>', {
            'class' : 'tag',
            'text' : suggestion[ 'name' ]
        });
        var $input = jQuery( '<input>', {
            'type' : 'hidden',
            'name' : fieldName + '[]',
            'value' : suggestion[ 'name' ]
        });
        var $close = jQuery( '<i/>', {
            'class' : 'fa fa-times remove-slug'
        })
        $slug.append( $input ).append( $close );
        $elt.closest( 'fieldset' )
            .find( '.slug-wrapper' )
            .append( $slug );
        $elt.typeahead( 'val', '' );
    }, 1000, { trailing : false } );

    // set up the tag typeahead autocomplete
    //
    var tagSource = new Bloodhound({
        datumTokenizer: function( d ) {
            return Bloodhound.tokenizers.whitespace( d.name );
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        local: tags
    });
 
    tagSource.initialize();
 
    $( '#tag-ac' ).typeahead( null, {
        displayKey: 'name',
        source: tagSource.ttAdapter()
    })
    .on( 'typeahead:selected', saveSlug )
    .on( 'keyup', function ( e ) {
        e.keyCode == 13
            && ( $this = $( this ) )
            && $this.val()
            && saveSlug( e, { 'name' : $( this ).val() } );
    });

    // set up the artist typeahead autocomplete
    //
    var artistSource = new Bloodhound({
        datumTokenizer: function( d ) {
            return Bloodhound.tokenizers.whitespace( d.name );
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        local: artists
    });
 
    artistSource.initialize();
 
    $( '#artist-ac' ).typeahead( null, {
        displayKey: 'name',
        source: artistSource.ttAdapter()
    })
    .on( 'typeahead:selected', saveSlug )
    .on( 'keyup', function ( e ) {
        e.keyCode == 13
            && ( $this = $( this ) )
            && $this.val()
            && saveSlug( e, { 'name' : $( this ).val() } );
    });

    // delete a slug event
    //
    $( '#admin.page' ).on( 'click', 'i.remove-slug', function () {
        $( this ).closest( 'span' ).remove();
    });
});