$(function () {
  /*
   * Inicializacia boxov.
   */
  var grid = $('.grid').packery({
    itemSelector: '.grid-item',
    gutter: 10,
    columnWidth: 20
  }).hide();

  var dashboard = $('#dashboard');
  var activeGrid = $('#' + dashboard[0].value);
  activeGrid.show();

  // make all items draggable
  grid.find('.grid-item').each( function( i, gridItem ) {
    var draggie = new Draggabilly( gridItem );
    // bind drag events to Packery
    grid.packery( 'bindDraggabillyEvents', draggie );
  });

  dashboard.on('change', function() {
    activeGrid.hide();
    activeGrid = $('#' + this.value);
    activeGrid.show();
  });
});


  /*
   * Funckia na vykreslenie ciary grafu
   */
  function DrawLineGraph(data){
    var svg = d3.select("svg"),
        margin = {top: 20, right: 20, bottom: 30, left: 50},
        width = +svg.attr("width") - margin.left - margin.right,
        height = +svg.attr("height") - margin.top - margin.bottom;

    svg.selectAll("*").remove();
    var g = svg.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    var parseTime = d3.timeParse("%H-%M");
    var x = d3.scaleTime()
        .rangeRound([0, width]);

    var y = d3.scaleLinear()
        .rangeRound([height, 0]);

    var line = d3.line()
        .x(function(d) { return x(d.time); })
        .y(function(d) { return y(d.count); });

    for ( var i = 0 ; i < data.length ; i++ )
    {
        data[i].time = parseTime(data[i].time);
        data[i].count = +data[i].count;
    }

    x.domain(d3.extent(data, function(d) { return d.time; }));
    y.domain(d3.extent(data, function(d) { return d.count; }));

    g.append("g")
        .attr("class", "axis axis--x")
        .attr("transform", "translate(0," + height + ")")
        .call(d3.axisBottom(x));

    g.append("g")
        .attr("class", "axis axis--y")
        .call(d3.axisLeft(y))
        .append("text")
        .attr("fill", "#000")
        .attr("transform", "rotate(-90)")
        .attr("y", 6)
        .attr("dy", "0.71em")
        .style("text-anchor", "end")
        .text("Number of fault logins");

    g.append("path")
        .datum(data)
        .attr("class", "line")
        .attr("d", line);
    }
