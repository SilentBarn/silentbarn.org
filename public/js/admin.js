/**
 * Admin application JS
 */
jQuery( function( $ ) {

var AdminPage = {
    // set up datepickers
    datepickers: function () {
        $( '.datepicker' ).pikaday({
            format: 'M/D/YYYY'
        });
    },

    // set up timepickers
    timepickers: function () {
        $( '.timepicker' ).timepicker({});
    },

    // handle image previewing
    imagePreview: function () {
        var $imageInput = $( '#image-input' );

        if ( $imageInput.length ) {
            var $imagePreview = $( '#image-preview' ),
                $noImageNote = $( '#image-none' );

            function readURL ( input ) {
                if ( input.files && input.files[ 0 ] ) {
                    var reader = new FileReader();
                    reader.onload = function ( e ) {
                        $imagePreview.attr( 'src', e.target.result ).show();
                        $noImageNote.hide();
                    }
                    reader.readAsDataURL( input.files[ 0 ] );
                }
            }

            $imageInput.change( function () {
                readURL( this );
            });
        }
    },

    // handle image cropping
    imageCrop: function () {
        var $imageCrop = $( '#image-crop' ),
            CROP_ENABLED = false;
        if ( ! $imageCrop.length ) return;

        // update the coordinates in the form, and display
        // the height and width for the user
        function updateCoords( c ) {
            if ( ! CROP_ENABLED ) return;
            $( '#ic_x1' ).val( c.x );
            $( '#ic_x2' ).val( c.x2 );
            $( '#ic_y1' ).val( c.y );
            $( '#ic_y2' ).val( c.y2 );
            $( '#ic_w' ).text( c.w + 'px' );
            $( '#ic_h' ).text( c.h + 'px' );
        };

        // instantiate the cropper
        var $cropper = $( '#cropper' ),
            jcrop;
        $imageCrop.Jcrop({
            onChange: updateCoords,
            onSelect: updateCoords,
            minSize: [ 310, 310 ],
            setSelect: [ 0, 0, 310, 310 ]
        }, function () {
            jcrop = this;
        });

        // set up onclick event to open cropper
        $( '#open-cropper' ).on( 'click', function () {
            CROP_ENABLED = true;
            $cropper.show();
            $.scrollTo( $cropper.position().top - 50 );
            jcrop.setSelect( [ 0, 0, 310, 310 ] );
        });

        $( '#cancel-cropper' ).on( 'click', function () {
            CROP_ENABLED = false;
            $cropper.hide();
            $( '#ic_x1' ).val( '' );
            $( '#ic_x2' ).val( '' );
            $( '#ic_y1' ).val( '' );
            $( '#ic_y2' ).val( '' );
        })
    },

    // Post actions: save/delete
    postActions: function () {
        // save button
        $( '#save-object' ).on( 'click', function () {
            $( 'form#edit-form' ).submit();
        });

        // delete button
        $( '#delete-object' ).on( 'click', function () {
            var $this = $( this );
            $this.hide();
            $this.next().show();

            var revert = function () {
                var $reallyDelete = $( '#really-delete-object' );
                $reallyDelete.hide();
                $reallyDelete.prev().show();
            };

            var timeoutId = window.setTimeout( revert, 3000 );
        });
    },

    // Artist, tag, grant slug handlers
    slugs: function () {
        if ( ! $( '#tag-ac' ).length ) {
            return;
        }

        // callback to add a new autocomplete slug
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
        $( '#admin.page' ).on( 'click', 'i.remove-slug', function () {
            $( this ).closest( 'span' ).remove();
        });
    },

    // Show the end event date/time inputs, location input
    meta: function () {
        $( '#set-event-end-date' ).on( 'click', function () {
            var $this = $( this );
            $this.hide();
            $this.next().show().focus();
        });

        $( '.set-time' ).on( 'click', function () {
            var $this = $( this );
            $this.hide();
            $this.next().show().focus();
        });

        $( '#set-location' ).on( 'click', function () {
            var $this = $( this );
            $this.hide();
            $this.next().show().find( 'input' ).focus();
        });

        $( '#set-price' ).on( 'click', function () {
            var $this = $( this );
            $this.hide();
            $this.next().show().find( 'input' ).focus();
        });

        // Show formatting help
        $( '#show-formatting-help' ).on( 'click', function () {
            $( '#formatting-help' ).toggle();
        });

        // Show audio file
        $( '#add-audio' ).on( 'click', function () {
            $( this ).hide().next().show().find( 'input' ).click();
        });

        $( '#clear-audio' ).on( 'click', function () {
            $( this ).prev().val( '' );
        });

        // Show author
        $( '#add-author' ).on( 'click', function () {
            $( this ).hide().next().show().find( 'input' ).focus();
        });
    },

    // Page content editors
    pageEditor: function () {
        if ( ! $( '#editor' ).length ) {
            return;
        }

        var $admin = $( '#admin' ),
            $editor = $( '#editor' ),
            $preview = $( '#preview' ),
            editor = ace.edit( "editor" ),
            newHeight, previewClick, editorClick;

        // Create the Ace Editor
        editor.setTheme( "ace/theme/chrome" );
        editor.setHighlightActiveLine( false );
        editor.getSession().setMode( "ace/mode/html" );
        document.getElementById( 'editor' ).style.fontSize='16px';

        // Kill the footer
        $( '#admin-footer' ).hide();

        // Resize editor to be full height of the page
        newHeight = $( window ).height()
            - $editor.offset().top
            - 15; // padding
        newHeight = newHeight 
        $editor.css( 'height', newHeight );

        // Save the editor HTML when save button is clicked
        $( '#save-object' ).on( 'click', function () {
            $( '#editor-content' ).val( editor.getValue() );
            $( '#editor-form' ).submit();
        });

        // Live preview mode renders the HTML, changes nav tabs
        previewClick = function () {
            var $modeEditor = $( '#mode-editor' ),
                $modePreview = $( '#mode-preview' );
            var $newSpan = $( '<span>', {
                'id': 'mode-preview',
                'text': 'Preview'
            });
            var $newLink = $( '<a>', {
                'id': 'mode-editor',
                'text': 'Editor',
                'href': 'javascript:;'
            });
            $modePreview.replaceWith( $newSpan );
            $modeEditor.replaceWith( $newLink );
            $editor.hide();
            $preview.html( editor.getValue() ).show();
        };

        // Editor mode renders Ace editor, changes nav tabs
        editorClick = function () {
            var $modeEditor = $( '#mode-editor' ),
                $modePreview = $( '#mode-preview' );
            var $newSpan = $( '<span>', {
                'id': 'mode-editor',
                'text': 'Editor'
            });
            var $newLink = $( '<a>', {
                'id': 'mode-preview',
                'text': 'Preview',
                'href': 'javascript:;'
            });
            $modeEditor.replaceWith( $newSpan );
            $modePreview.replaceWith( $newLink );
            $preview.hide();
            $editor.show();
        };

        $admin.on( 'click', '#mode-editor', editorClick );
        $admin.on( 'click', '#mode-preview', previewClick );
    },

    homepage: function () {
        $( '.page' ).on( 'click', '.remove-post', function () {
            $( this ).closest( 'li' ).remove();
        });
    }
};

// call our page functions
AdminPage.datepickers();
AdminPage.timepickers();
AdminPage.imagePreview();
AdminPage.imageCrop();
AdminPage.postActions();
AdminPage.slugs();
AdminPage.meta();
AdminPage.pageEditor();
AdminPage.homepage();

});