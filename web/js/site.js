$(function () {
	// Initialize collapse button
    $('.button-collapse').sideNav({
	    menuWidth: 250, // Default is 240
	    draggable: true // Choose whether you can drag to open on touch screens
    });

    $('select').material_select();

    $('.datepicker').pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 15, // Creates a dropdown of 15 years to control year
        format: 'yyyy-mm-dd',
        closeOnSelect: true,
        firstDay: 1
    });

    var grid = $('.grid').packery({
      itemSelector: '.grid-item',
      gutter: 10,
      columnWidth: 20
    });

    // make all items draggable
    grid.find('.grid-item').each( function( i, gridItem ) {
      var draggie = new Draggabilly( gridItem );
      // bind drag events to Packery
      grid.packery( 'bindDraggabillyEvents', draggie );
    });
});