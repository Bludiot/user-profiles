/**
 * Backend scripts
 */

// Accordion UI
( function( $ ) {
	$( function () {

		// Expand/Collapse accordion sections on click.
		$( '.accordion-container' ).on( 'click keydown', '.accordion-section-title', function( e ) {
			if ( e.type === 'keydown' && 13 !== e.which ) { // "Return" key.
				return;
			}
			e.preventDefault(); // Keep this AFTER the key filter above.
			accordionSwitch( $( this ) );
		});
	});

	/**
	 * Close the current accordion section and open a new one.
	 *
	 * @param {Object} el Title element of the accordion section to toggle.
	 * @since 3.6.0
	 */
	function accordionSwitch ( el ) {
		var section = el.closest( '.accordion-section' ),
			sectionToggleControl = section.find( '[aria-expanded]' ).first(),
			container = section.closest( '.accordion-container' ),
			siblings = container.find( '.open' ),
			siblingsToggleControl = siblings.find( '[aria-expanded]' ).first(),
			content = section.find( '.accordion-section-content' );

		// This section has no content and cannot be expanded.
		if ( section.hasClass( 'cannot-expand' ) ) {
			return;
		}

		// Add a class to the container to let us know something is happening inside.
		// This helps in cases such as hiding a scrollbar while animations are executing.
		container.addClass( 'opening' );

		if ( section.hasClass( 'open' ) ) {
			section.toggleClass( 'open' );
			content.toggle( true ).slideToggle( 250 );
		} else {
			siblingsToggleControl.attr( 'aria-expanded', 'false' );
			siblings.removeClass( 'open' );
			siblings.find( '.accordion-section-content' ).show().slideUp( 250 );
			content.toggle( false ).slideToggle( 250 );
			section.toggleClass( 'open' );
		}

		// We have to wait for the animations to finish.
		setTimeout(function(){
		    container.removeClass( 'opening' );
		}, 250);

		// If there's an element with an aria-expanded attribute, assume it's a toggle control and toggle the aria-expanded value.
		if ( sectionToggleControl ) {
			sectionToggleControl.attr( 'aria-expanded', String( sectionToggleControl.attr( 'aria-expanded' ) === 'false' ) );
		}
	}
})(jQuery); // End accordion UI
