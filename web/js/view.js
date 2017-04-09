$(function () {
    //#region [ Fields ]

    var global = (function () { return this; })();
    var options = {};

    var dashboardSelect;
    var widthSelect;
    var editBtn;
    var removeBtn;
    var addComponentBtn;
    var activeGrid;
    var grid;
    var deleteComponentBtn;
    var componentForm;

    var hostUrl;
    var newComponentName = "New Component";

    //#endregion


    //#region [ Methods ]
    
    if (typeof (global.views) !== "function") {
        global.views = function (args) {
            $.extend(options, args || {});

            hostUrl = location.protocol + "//" + location.hostname + (location.port ? ":" + location.port : "");
            dashboardSelect = $('#dashboard');
            widthSelect = $('select.widthSelect');
            editBtn = $("#editBtn");
            removeBtn = $("#removeBtn");
            activeGrid = $('#grid_' + dashboardSelect.val());
            addComponentBtn = $("#addComponentBtn");
            deleteComponentBtn = $(".deleteComponentBtn");
            componentForm = $(".componentForm");

            // Inicializacia boxov.
            grid = $('.grid').packery({
                itemSelector: '.grid-item',
                gutter: 10,
                columnWidth: 20
            }).hide();

            // make all items draggable
            grid.find('.grid-item').each( function( i, gridItem ) {
                var draggie = new Draggabilly( gridItem );
                // bind drag events to Packery
                grid.packery( 'bindDraggabillyEvents', draggie );
            });
            
            activeGrid.show();
            UpdateBtnUrl(editBtn, dashboardSelect.val());
            UpdateBtnUrl(removeBtn, dashboardSelect.val());

            dashboardSelect.on('change', dashboardSelect_onChange);
            widthSelect.on('change', widthSelect_onChange);
            addComponentBtn.on('click', addComponentBtn_onClick);
            deleteComponentBtn.on('click', deleteComponentBtn_onClick);
            componentForm.on('submit', componentForm_onSubmit);
        }
    };

    //#endregion


    //#region [ Event Handlers ]

    /*
     * Event handler na zmenu dashboardu
     */
    function dashboardSelect_onChange (e) {
        activeGrid.hide();
        activeGrid = $('#grid_' + this.value);
        UpdateBtnUrl(editBtn, this.value);
        UpdateBtnUrl(removeBtn, this.value);
        activeGrid.show();

        $.ajax({
            url: hostUrl + options.changeView,
            data : { viewId : this.value}
        });
    };

    /*
     * Event handler na zmenu sirky komponentu
     */
    function widthSelect_onChange (e) {
        var selectNode = $(this);
        var gridItemNode = $("#" + selectNode.attr('data-id'));
        gridItemNode.attr('class', 'grid-item card ' + this.value);
        grid.packery('fit', gridItemNode[0]);
    };

    /*
     * Event handler pre pridanie komponentu
     */
    function addComponentBtn_onClick (e) {
        $.ajax({
            url: hostUrl + options.createComponent,
            data : { 
                viewId : dashboardSelect.val(),
                config : JSON.stringify({
                    name: newComponentName,
                    width: '',
                })
            },
        }).done(function (data) {
            if (!data) {
                Materialize.toast("Couldn't add component.", 4000);
                return;
            }

            var gridItemNode = $(data.html);
            activeGrid
                .append(gridItemNode)
                .packery( 'appended', gridItemNode );

            var draggie = new Draggabilly( gridItemNode[0] );
            activeGrid.packery( 'bindDraggabillyEvents', draggie );
        });
    };

    /*
     * Event handler pre vymazanie komponentu
     */
    function deleteComponentBtn_onClick (e) {
        var componentId = $(this).attr('data-id');
        
        $.ajax({
            url: hostUrl + options.deleteComponent,
            data : { 
                componentId : componentId
            },
        }).done(function (data) {
            if (!data) {
                Materialize.toast("Couldn't delete component.", 4000);
                return;
            }

            activeGrid.packery('remove', $("#component_" + componentId));
        });
    };

    /*
     * Event handler pre update komponentu
     */
    function componentForm_onSubmit (e) {
        e.preventDefault();
        
        var componentId = $(this).attr('data-id');
        var name = $(this).find("#name" + componentId).val();
        $.ajax({
            url: hostUrl + options.updateComponent,
            data : { 
                componentId : componentId,
                config : JSON.stringify({
                    name: $(this).find("#name" + componentId).val(),
                    width: $(this).find("#width" + componentId).val(),
                })
            },
        }).done(function (data) {
            if (!data) {
                Materialize.toast("Couldn't update component.", 4000);
                return;
            }

            window.location.reload(true);
        });

        return false;
    };

    //#endregion


    //#region [ Public Methods ]

    /*
     * Funckia na update id v href atribute buttonu
     */
    function UpdateBtnUrl (btn, id) {
        var href = btn.attr('href').split('=');
        href[1] = id;
        btn.attr('href', href.join('='));
    };

    /*
     * Funckia na vykreslenie ciary grafu
     */
    function DrawLineGraph(data){
        if (!data) {
            return;
        }
        
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
    };

    //#endregion
});