( function ( $ ) {
$.fn.extend({

    /**
     * Usage: $.fn.modal({ options })
     */
    modal: function ( options ) {
        // defaults
        var defaults = {
            top: 100,
            overlay: 0.5,
            closeButton: null
        }

        // create the overlay and append it
        // create the modal container and append it
        var $body = $( 'body' )
          , $overlay = $( '<div id="modal-overlay"></div>' )
          , $container = $( '<div id="modal-container"></div>' );
        $overlay.append( $container );
        $body.append( $overlay );

        // extend the defaults with passed in options
        options = $.extend( defaults, options );

        // hide the overlay and container when the overlay
        // is clicked
        $overlay.on( 'click', function () {
            closeModal();
        });
        // dont propagate clicks up on this
        $container.on( 'click', function ( e ) {
            e.stopPropagation();
            e.preventDefault();
            return false;
        })

        // if close button is specified, create that element
        // close the modal when close button is clicked
        if ( options.closeButton ) {
            // create the close button and append it to the
            // modal
            var $closeButtonWrap = $( '<div/>', {
                id: 'modal-close-wrap' });
            var $closeButton = $( '<a>', {
                id: "modal-close",
                href: "javascript:;",
                html: options.closeButton });
            $closeButtonWrap.append( $closeButton );
            $overlay.append( $closeButtonWrap );
            // attach click event
            $closeButton.click( function () {
                 closeModal();
            });
        }

        // attach close handler to escape keypress
        $( document ).keyup( function ( e ) {
            if ( e.keyCode == 27 ) {
                closeModal();
            } // esc
        });

        // attach closure to each element in set
        return this.each( function () {
            // save options locally
            var o = options;
            // everything happens on click
            $( this ).click( function ( e ) {
                // get the target content
                var modalId = $( this ).data( 'target' );
                // clone the target html and inject it into our
                // modal content
                var content = $( modalId ).html();
                $container.html( content );
                // get modal size
                var modalWidth = $container.outerWidth();
                // show the overlay
                $overlay.show();
                // hide the scrollbar from the body
                $body.addClass( 'with-modal' );
                // set the CSS on the target element
                $container.css({
                    'left' : 50 + '%',
                    'margin-left' : -( modalWidth / 2 ) + "px",
                    'top' : o.top + "px"
                }); // click event
                e.preventDefault();
            }); // click event
        }); // return

        /**
         * Removes the modal container content, hides both the
         * container and the overlay.
         */
        function closeModal() {
            $body.removeClass( 'with-modal' );
            $overlay.hide();
            $container.html( '' );
        }
    }
});
})( jQuery );