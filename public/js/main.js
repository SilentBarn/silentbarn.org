/**
 * Main application JS
 */
jQuery( function( $ ) {
    // set up homepage filmroll
    //
    if ( $( '#hero-carousel' ).length ) {
        var filmRoll = new FilmRoll({
            animation: 1000,
            container: '#hero-carousel',
            scroll: true,
            interval: 15000,
            start_index: 0,
            height: 420,
            prev: '#carousel-prev',
            next: '#carousel-next'
        });
    }

    // sticky nav on scroll
    //
    var navOffset = 122,
        logoOffset = 110,
        $document = $( document ),
        $body = $( 'body' ),
        pageNavClass = ( $( '#page-nav-wrap' ).length ) ? ' pagenav' : '';

    $( document ).bind( 'ready scroll', function () {
        var docScroll = $document.scrollTop();

        if ( docScroll >= navOffset ) {
            $body.addClass( 'sticky' + pageNavClass )
                .addClass( 'shiftlogo' );
        }
        else if ( docScroll >= logoOffset ) {
            $body.removeClass( 'sticky' + pageNavClass )
                .addClass( 'shiftlogo' );
        }
        else {
            $body.removeClass( 'sticky' + pageNavClass )
                .removeClass( 'shiftlogo' );
        }
    });

    if ( $( '#full-clndr' ).length ) {
        $( '#full-clndr' ).clndr({
            template: $('#full-clndr-template').html(),
            events: calendarEvents
        });
    }
});