/**
 * Application JS (for now)
 */
$( document ).ready( function () {
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
        $body = $( 'body' );

    $( document ).bind( 'ready scroll', function () {
        var docScroll = $document.scrollTop();

        if ( docScroll >= navOffset ) {
            $body.addClass( 'sticky' )
                .addClass( 'shiftlogo' );
        }
        else if ( docScroll >= logoOffset ) {
            $body.removeClass( 'sticky' )
                .addClass( 'shiftlogo' );
        }
        else {
            $body.removeClass( 'sticky' )
                .removeClass( 'shiftlogo' );
        }
    });
});