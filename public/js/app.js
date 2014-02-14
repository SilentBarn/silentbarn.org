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
    var navOffset = 120;

    $( document ).bind( 'ready scroll', function () {
        var docScroll = $( document ).scrollTop();

        if ( docScroll >= navOffset ) {
            $( 'body' ).addClass( 'sticky' );
        }
        else {
            $( 'body' ).removeClass( 'sticky' );
        }
    });
});