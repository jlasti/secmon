$(function () {
    //#region [ Fields ]

    var global = (function () { return this; })();

    var hostUrl;
    var options = {};
    var activeComponentIds = [];
    var dashboardSelect;
    var widthSelect;
    var editBtn;
    var removeBtn;
    var addComponentBtn;
    var activeGrid;
    var grid;
    var deleteComponentBtn;
    var componentForm;
    var saveContentBtn;
    var deleteContentBtn;

    var newComponentName = "New Component";

    //#endregion


    //#region [ Methods ]
    
    if (typeof (global.views) !== "function") {
        global.views = function (args) {
            $.extend(options, args || {});
            $.extend(activeComponentIds, args.activeComponentIds || []);

            hostUrl = location.protocol + "//" + location.hostname + (location.port ? ":" + location.port : "");
            dashboardSelect = $('#dashboard');
            widthSelect = $('select.widthSelect');
            editBtn = $("#editBtn");
            removeBtn = $("#removeBtn");
            activeGrid = $('#grid_' + dashboardSelect.val());
            addComponentBtn = $("#addComponentBtn");
            deleteComponentBtn = $(".deleteComponentBtn");
            componentForm = $("form.componentForm");
            saveContentBtn = $("[data-action='saveComponentContent']");
            deleteContentBtn = $("[data-action='removeComponentContent']");
            nameInputs = $(".nameInput");



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
            grid.on( 'dragItemPositioned', saveOrder_onDragItemPositioned );
            saveContentBtn.on('click', saveContentBtn_onClick);
            deleteContentBtn.on('click', deleteContentBtn_onClick);
            nameInputs.on('focusout blur', name_onFocusOut);

            // Show grid after js inicialization
            $('.grid').removeClass("invisible");

            //setInterval(componentUpdate, 5000);
            componentUpdate();
        }
    };

    //#endregion

    function componentUpdate() {
        if (activeComponentIds.length) {
            activeComponentIds.forEach(function (item, index) {
                $.ajax({
                    url: hostUrl + options.updateComponentContent,
                    data: {componentId: item},
                    async: true,
                    cache: false
                }).done(function (data) {
                    if (!data) {
                        Materialize.toast("Couldn't add filter to component.", 4000);
                        return;
                    }
                    var cont = $("#componentContentBody" + item);
                    var loader = cont.find("#componentLoader");
                    var body = cont.find("#componentBody");
                    loader.css('display', 'none');

                    if (data.contentTypeId == "table") {
                        cont.html(data.html);
                    }
                    if (data.contentTypeId == "lineChart") {
                        var width = parseInt(cont.css("width").replace("px",""));
                        cont.empty();
                        DrawBarGraph(JSON.parse(data.data), width/2, width, "#componentContentBody" + item);
                    }
                }).fail(function () {
                    Materialize.toast("Couldn't update component content!", 4000);
                });
            });
        }
    }

    //#region [ Event Handlers ]

    /*
     * Event handler na ulozenie konfiguracie obsahu
     */
    function saveContentBtn_onClick() {
         var compId = $(this).attr('data-id');
         var comp = $('#component_' + compId);
         var data = $('#contentSettingsForm' + compId).serialize();
         var remBtn = $("#removeComponentContentBtn" + compId);
         var cont = $("#componentContentBody" + compId);
         var contNew = $("#componentContentBodyNew" + compId);
         var loader = cont.find("#componentLoader");
         var body = cont.find("#componentBody");
         var edit = comp.find("#contentEdit");

         $.ajax({
             url: hostUrl + options.updateComponentSettings,
             data: data
         }).done(function(data) {
             if (!data){
                 Materialize.toast("Couldn't add filter to component!", 4000);
                 return;
             }
             
             // Update komponentu
             $("#componentContentBody" + compId).attr('data-type', data.contentTypeId);
             $.ajax({
                url: hostUrl + options.updateComponent,
                data : { 
                    componentId : compId,
                    config : JSON.stringify({
                        name: comp.find("#name" + compId).val(),
                        width: comp.find("#width" + compId).val(),
                        dataType: data.contentTypeId
                    })
                },
            });

             remBtn.css('display', 'block');
             cont.css('display', 'block');
             contNew.css('display', 'none');
             loader.css('display', 'inline-block');
             edit.css('display', 'block');

             if (activeComponentIds.indexOf(compId) == -1) {
                activeComponentIds.push(compId);
             }

             if (data.contentTypeId == "table") {
                cont.html(data.html);
             }
             if (data.contentTypeId == "lineChart") {
                var width = parseInt(cont.css("width").replace("px",""));
                cont.empty();
                DrawBarGraph(JSON.parse(data.data), width/2, width, "#componentContentBody" + compId);
             }
         }).fail(function(){
             Materialize.toast("Couldn't add filter to component!", 4000);
         });
    }

    /*
     * Event handler na vymazanie konfiguracie obsahu
     */
    function deleteContentBtn_onClick() {
        var compId = $(this).attr('data-id');
        var comp = $('#component_' + compId);
        var data = $('#contentSettingsForm' + compId).serialize();
        var remBtn = $("#removeComponentContentBtn" + compId);
        var cont = $("#componentContentBody" + compId);
        var contNew = $("#componentContentBodyNew" + compId);
        var loader = cont.find("#componentLoader");
        var body = cont.find("#componentBody");
        var edit = comp.find("#contentEdit");
        $.ajax({
            url: hostUrl + options.deleteComponentSettings,
            data: data
        }).done(function(html) {
            remBtn.css('display', 'none');
            cont.css('display', 'none');
            loader.css('display', 'none');
            body.css('display', 'none');
            edit.css('display', 'none');

            var index = activeComponentIds.indexOf(compId);
            if (index != -1)
                activeComponentIds.splice(index, 1);

            body.html('');
            contNew.css('display', 'block');
        });
    }

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
        gridItemNode.find("form.componentForm").submit();

        // Resize graph
        var width = parseInt(gridItemNode.find(".card-content").css("width").replace("px",""));
        var height = parseInt(gridItemNode.find(".card-content").css("height").replace("px",""));
        UpdateBarGraph(height, width, "#componentContentBody" + gridItemNode.find("form.componentForm").attr('data-id'));
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
                    dataType: 'table'
                }),
                order : activeGrid.packery("getItemElements").length
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
            
            // Inicializacia noveho grid itemu
            var draggie = new Draggabilly( gridItemNode[0] );
            activeGrid.packery( 'bindDraggabillyEvents', draggie );
            gridItemNode.find('select').material_select();
            gridItemNode.find('select.widthSelect').on("change", widthSelect_onChange);
            gridItemNode.find('.modal').modal();
            gridItemNode.find("[data-action='saveComponentContent']").on('click', saveContentBtn_onClick);
            gridItemNode.find("[data-action='removeComponentContent']").on('click', deleteContentBtn_onClick);
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
     * Event handler pre ulozenie poradia komponentov
     */
    function saveOrder_onDragItemPositioned (e) {
        var itemElems = activeGrid.packery('getItemElements');
        var order = itemElems.map(function (item, index) {
            return {
                id: item.id.replace("component_", ""),
                order: index
            }
        });

        $.ajax({
            url: hostUrl + options.updateOrder,
            data : { 
                viewId : dashboardSelect.val(),
                componentOrder : JSON.stringify(order)
            },
        }).done(function (data) {
            if (!data) {
                Materialize.toast("Couldn't update order of components.", 4000);
                return;
            }
        });
    };

    /*
     * Event handler pre zmenu mena komponentu
     */
    function name_onFocusOut (e) {
        var input = $(this);
        var gridItemNode = $("#" + input.attr('data-id'));
        gridItemNode.find(".nameTitle").html(input.val());
        gridItemNode.find("form.componentForm").submit();
    }

    /*
     * Event handler pre update komponentu
     */
    function componentForm_onSubmit (e) {
        e.preventDefault();

        var componentId = $(this).attr('data-id');
        
        $.ajax({
            url: hostUrl + options.updateComponent,
            data : { 
                componentId : componentId,
                config : JSON.stringify({
                    name: $(this).find("#name" + componentId).val(),
                    width: $(this).find("#width" + componentId).val(),
                    dataType: $("#componentContentBody" + componentId).attr('data-type')
                })
            },
        }).done(function (data) {
            if (!data) {
                Materialize.toast("Couldn't update component.", 4000);
                return false;
            }
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
     * Funkcia na vykreslenie ciary grafu
     */
    function DrawLineGraph(data) {
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

    /*
     * Funkcia na vykreslenie bar grafu
     */
    function DrawBarGraph(data,outboundHeight,outboungWidth,node) {
        if (!data || !outboundHeight || !outboungWidth || !node) {
            return;
        }

        // set the dimensions and margins of the graph
        var margin = {top: 20, right: 20, bottom: 30, left: 40},
            width = outboungWidth - margin.left - margin.right,
            height = outboundHeight - margin.top - margin.bottom;

        // set the ranges
        var x = d3.scaleBand()
                    .range([0, width])
                    .padding(0.1);
        var y = d3.scaleLinear()
                    .range([height, 0]);
                    
        // append the svg object to the body of the page
        // append a 'group' element to 'svg'
        // moves the 'group' element to the top left margin
        var svg = d3.select(node).append("svg")
            .attr("id","barChart")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
            .append("g")
            .attr("transform", 
                    "translate(" + margin.left + "," + margin.top + ")");

        // format the data
        data.forEach(function(d) {
        d.count = +d.count;
        });

        // Scale the range of the data in the domains
        x.domain(data.map(function(d) { return d.hour; }));
        y.domain([0, d3.max(data, function(d) { return d.count; })]);

        // append the rectangles for the bar chart
        svg.selectAll(".bar")
            .data(data)
        .enter().append("rect")
            .attr("class", "bar")
            .attr("x", function(d) { return x(d.hour); })
            .attr("width", x.bandwidth())
            .attr("y", function(d) { return y(d.count); })
            .attr("height", function(d) { return height - y(d.count); })
            .attr("fill", "#039be5")
            .on("mouseover", function() {
                d3.select(this)
                    .attr("fill", "#F44336");
            })
            .on("mouseout", function(d, i) {
                d3.select(this).attr("fill", "#039be5");
            })
            .append("title")
            .text(function(d) {
                return d.hour;
            });

        // add the x Axis
        svg.append("g")
            .attr("transform", "translate(0," + height + ")")
            .call(
            d3.axisBottom(x)
            );

        // add the y Axis
        svg.append("g")
            .call(
            d3.axisLeft(y)
            );

        var bars = svg.selectAll(".bar");

        bars.on("mouseover", function() {
            d3.select(this)
            .attr("fill", "red");
        });
    };

    /*
    * Funkcia na update bar grafu
    */
    function UpdateBarGraph(outboundHeight,outboungWidth,node) {
        if (!outboundHeight || !outboungWidth || !node) {
            return;
        }

        var data = d3.select(node).selectAll("svg").selectAll(".bar").data();
        console.log(node);

        if (!data) {
            return;
        }

        d3.select(node).selectAll("#barChart").remove();

        // set the dimensions and margins of the graph
        var margin = {top: 20, right: 20, bottom: 30, left: 40},
            width = outboungWidth - margin.left - margin.right,
            height = outboundHeight - margin.top - margin.bottom;

        // set the ranges
        var x = d3.scaleBand()
                    .range([0, width])
                    .padding(0.1);
        var y = d3.scaleLinear()
                    .range([height, 0]);
                    
        // append the svg object to the body of the page
        // append a 'group' element to 'svg'
        // moves the 'group' element to the top left margin
        var svg = d3.select(node).append("svg")
            .attr("id","barChart")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
            .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

        // format the data
        data.forEach(function(d) {
            d.count = +d.count;
        });

        // Scale the range of the data in the domains
        x.domain(data.map(function(d) { return d.hour; }));
        y.domain([0, d3.max(data, function(d) { return d.count; })]);

        // append the rectangles for the bar chart
        svg.selectAll(".bar")
            .data(data)
        .enter().append("rect")
            .attr("class", "bar")
            .attr("x", function(d) { return x(d.hour); })
            .attr("width", x.bandwidth())
            .attr("y", function(d) { return y(d.count); })
            .attr("height", function(d) { return height - y(d.count); })
            .attr("fill", "#039be5")
            .on("mouseover", function() {
                d3.select(this)
                    .attr("fill", "#F44336");
            })
            .on("mouseout", function(d, i) {
                d3.select(this).attr("fill", "#039be5");
            })
            .append("title")
            .text(function(d) {
                return d.hour;
            });

        // add the x Axis
        svg.append("g")
            .attr("transform", "translate(0," + height + ")")
            .call(
            d3.axisBottom(x)
        );

        // add the y Axis
        svg.append("g")
            .call(
            d3.axisLeft(y)
        );

        var bars = svg.selectAll(".bar");

        bars.on("mouseover", function() {
                d3.select(this)
                    .attr("fill", "red");
        });
    };

    //#endregion
});