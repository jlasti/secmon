$(function () {
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