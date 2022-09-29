var viewUpdateInterval = 10000;

$(function () {
  //#region [ Fields ]

  var global = (function () {
    return this;
  })();

  var hostUrl;
  var options = {};
  var activeComponentIds = [];
  var tableColumns = {};
  var tableColumnsNames = [];
  var dashboardSelect;
  var widthSelect;
  var editBtn;
  var removeBtn;
  var addComponentBtn;
  var activeGrid;
  var grid;
  var deleteComponentBtn;
  var componentForm;
  var refreshTime;
  var isInitialized = false;

  var newComponentName = "New Component";
  //#endregion

  //#region [ Methods ]

  if (typeof global.views !== "function") {
    global.views = function (args) {
      $.extend(options, args || {});
      $.extend(activeComponentIds, args.activeComponentIds || []);
      tableColumns = args.tableColumns;
      tableColumnsNames = Object.keys(tableColumns);

      hostUrl =
        location.protocol +
        "//" +
        location.hostname +
        (location.port ? ":" + location.port : "");
      dashboardSelect = $("#dashboard");
      editBtn = $("#editBtn");
      removeBtn = $("#removeBtn");
      activeGrid = $("#grid_" + dashboardSelect.val());
      addComponentBtn = $("#addComponentBtn");

      activateComponent();

      // Inicializacia boxov.
      grid = $(".grid")
        .packery({
          itemSelector: ".grid-item",
          gutter: ".gutter-sizer",
          columnWidth: ".grid-sizer",
          percentPosition: true,
        })
        .hide();

      // make all items draggable
      grid.find(".grid-item").each(function (i, gridItem) {
        var draggie = new Draggabilly(gridItem);
        // bind drag events to Packery
        grid.packery("bindDraggabillyEvents", draggie);
      });

      activeGrid.show();
      UpdateBtnUrl(editBtn, dashboardSelect.val());
      UpdateBtnUrl(removeBtn, dashboardSelect.val());

      dashboardSelect.on("change", dashboardSelect_onChange);
      addComponentBtn.on("click", addComponentBtn_onClick);
      grid.on("dragItemPositioned", saveOrder_onDragItemPositioned);

      // Show grid after js inicialization
      $(".grid").removeClass("invisible");

      getRefreshTime();
      componentUpdate();
      grid.packery("shiftLayout");
    };
  }

  /*
   * Activates chips for table columns
   */
  function activateTableColumns(e) {
    var chips;
    var addColumns;
    if (e === undefined) {
      chips = $(".chips-table");
      addColumns = $('[data-type="columnsSelectAdd"]');
    } else {
      chips = $(e).find(".chips-table");
      addColumns = $(e).find('[data-type="columnsSelectAdd"]');
    }

    var updateChips = function (target, val) {
      var newData = $.map(val, function (v) {
        return { tag: v };
      });
      target.material_chip({
        data: newData,
      });
      target
        .find("input")
        .hide()
        .css({ width: "0", padding: "0", margin: "0" });
    };

    var updateColumnsValue = function (e, target, action, chipObj) {
      var compId = $(target).attr("data-id");
      var val = $(target).material_chip("data");
      val = $.map(val, function (chip) {
        return chip.tag;
      });

      var changed = false;
      if (action === "add" && tableColumnsNames.indexOf(chipObj.tag) === -1) {
        var idx = val.indexOf(chipObj.tag);
        if (idx !== -1) {
          val.splice(idx, 1);
          changed = true;
        } else {
          console.log("chip object index not found!");
        }
      }

      if (changed) {
        updateChips($(target), val);
      }

      val = val.join(",");
      $("#componentDataTypeParameter" + compId).val(val);
    };

    addColumns.each(function () {
      var addCol = $(this);
      addCol.on("click", function (e) {
        var btn = $(this);
        var compId = btn.attr("data-id");
        var selectedValue = $("#columnsSelect" + compId).val();

        var target = $("#chipsTable" + compId);
        var val = target.material_chip("data");
        val = $.map(val, function (chip) {
          return chip.tag;
        });

        if (tableColumnsNames.indexOf(selectedValue) !== -1) {
          if (val.indexOf(selectedValue) === -1) {
            val.push(selectedValue);
            updateChips(target, val);

            val = val.join(",");
            $("#componentDataTypeParameter" + compId).val(val);
          } else
            Materialize.toast(
              'Column "' + selectedValue + '" already in list!',
              2000
            );
        } else Materialize.toast('Column "' + selectedValue + '" is unknown!', 2000);
      });
    });

    chips.each(function () {
      var chip = $(this);
      var source, dest;
      chip.sortable({
        connectWith: ".chips-table",
        start: function (e, ui) {
          source = dest = e.target;
          $(source).find(".close").hide();
        },
        change: function (e, ui) {
          if (ui.sender) {
            if (e.target.tagName.toLowerCase() !== "div") dest = e.target;
          }
        },
        stop: function (e, ui) {
          $(source).find(".close").show();

          var dText = dest.outerText
            .split("close")
            .filter(function (o) {
              return o;
            })
            .join(",");
          $(dest).siblings("input").val(dText).trigger("change");

          if (source != dest) {
            var sText = source.outerText
              .split("close")
              .filter(function (o) {
                return o;
              })
              .join(",");
            $(source).siblings("input").val(sText).trigger("change");
          }

          updateChips($(e.target), dText.split(","));
        },
      });

      var columns = chip.attr("data-table-columns");
      var data = [];
      if (columns !== undefined && columns !== "") {
        var cols = columns.split(",");
        data = $.map(cols, function (col) {
          return { tag: col };
        });
      }

      chip.material_chip({
        data: data,
      });
      chip.find("input").hide().css({ width: "0", padding: "0", margin: "0" });

      updateColumnsValue(undefined, chip, "init");

      chip.on("chip.add", function (e, chip) {
        updateColumnsValue(e, e.target, "add", chip);
      });
      chip.on("chip.delete", function (e, chip) {
        updateColumnsValue(e, e.target, "delete", chip);
      });
    });
  }

  /*
   * Activates component controls
   */
  function activateComponent(e) {
    var element;
    if (e === undefined) {
      element = $(document);
    } else {
      element = $(e);
    }

    element.find(".modal").modal();
    element.find(".close-modal").on("click", closeModal);
    element.find(".nameInput").on("keyup", changeNameHeader);
    element.find("select").material_select();
    element.find(".nameInput").on("focusout blur", name_onFocusOut);
    element.find("select.widthSelect").on("change", widthSelect_onChange);
    element.find(".deleteComponentBtn").on("click", deleteComponentBtn_onClick);
    element
      .find("[data-action='saveComponentContent']")
      .on("click", saveContentBtn_onClick);
    element
      .find("[data-action='removeComponentContent']")
      .on("click", deleteContentBtn_onClick);
    element
      .find("select[data-type='contentTypeSelect']")
      .on("change", contentTypeChanged);
    element.find("form.componentForm").on("submit", componentForm_onSubmit);

    element.find('[data-type="contentTypeSelect"]').each(contentTypeChanged);
    activateTableColumns(element);
  }

  /*
   * Add component id to list of component ids for update.
   */
  function addActiveComponent(componentId) {
    var compId = parseInt(componentId);
    if (activeComponentIds.indexOf(compId) == -1) {
      activeComponentIds.push(compId);
    }
  }

  /*
   * Remove component id from list of component ids for update.
   */
  function removeActiveComponent(componentId) {
    var compId = parseInt(componentId);
    var index = activeComponentIds.indexOf(compId);
    if (index != -1) activeComponentIds.splice(index, 1);
  }

  /*
   * Updates specific component and after completion updates next component.
   * When index is
   */
  function componentUpdate(index) {
    if (index === undefined) {
      index = 0;
    }
    if (refreshTime === undefined) {
      getRefreshTime();
    }

    if (index < activeComponentIds.length) {
      var item = activeComponentIds[index];
      var page = 1;
      if (
        $("#component_" + item).find(".pagination").length != 0 &&
        $($("#component_" + item).find(".pagination")[0]).find(".active")
          .length != 0
      ) {
        page = parseInt(
          $(
            $($("#component_" + item).find(".pagination")[0]).find(".active")[0]
          ).find("a")[0].innerHTML
        );
      }
      $.ajax({
        url: hostUrl + options.updateComponentContent,
        data: { componentId: item, pagination: page },
        async: true,
        cache: false,
      })
        .done(function (data) {
          if (!data) {
            Materialize.toast("Couldn't add filter to component.", 4000);
            return;
          }
          var cont = $("#componentContentBody" + item);
          var loader = cont.find("#componentLoader");
          var body = cont.find("#componentBody");
          loader.css("display", "none");
          if (data.contentTypeId == "table") {
            var paging = data.paging[0].count;
            var htmlData = data.html + createPagination(page, item, paging);
            cont.html(htmlData);
            $(cont.find("table")[0]).attr("class", "striped");
            $(cont.find("th")[0]).css("display", "none");
            $(cont.find("tbody")[0])
              .find("tr")
              .each(function (i, e) {
                $(e).attr("class", "component-row");
                $(e).attr(
                  "data-key",
                  "/secmon/web/security-events/view?id=" +
                    $(e).find("td")[0].innerHTML
                );
                $($(e).find("td")[0]).css("display", "none");
              });
          } else if (data.contentTypeId == "barChart") {
            var width = parseInt(cont.css("width").replace("px", ""));
            cont.empty();
            DrawBarGraph(
              JSON.parse(data.data),
              width / 2,
              width,
              "#componentContentBody" + item
            );
          } else if (data.contentTypeId == "pieChart") {
            var width = parseInt(cont.css("width").replace("px", ""));
            cont.empty();
            DrawPieGraph(
              JSON.parse(data.data),
              width / 2,
              width,
              "#componentContentBody" + item
            );
          }
          // Fit item in grid
          grid.packery("fit", $("#component_" + item)[0]);
        })
        .fail(function () {
          Materialize.toast("Couldn't update component content!", 4000);
        })
        .always(function (jqXHR, textStatus) {
          componentUpdate(index + 1);
        });
    } else {
      if (!isInitialized) {
        grid.packery("shiftLayout");
        isInitialized = true;
      }
      if (refreshTime > 0) {
        setTimeout(componentUpdate, refreshTime * 1000);
      }
    }
  }

  function singleComponentUpdate(item, page) {
    $.ajax({
      url: hostUrl + options.updateComponentContent,
      data: { componentId: item, pagination: page },
      async: true,
      cache: false,
    })
      .done(function (data) {
        if (!data) {
          Materialize.toast("Couldn't add filter to component.", 4000);
          return;
        }
        var cont = $("#componentContentBody" + item);
        var loader = cont.find("#componentLoader");
        var body = cont.find("#componentBody");
        loader.css("display", "none");
        if (data.contentTypeId == "table") {
          var paging = data.paging[0].count;
          var htmlData = data.html + createPagination(page, item, paging);
          cont.html(htmlData);
          $(cont.find("table")[0]).attr("class", "striped");
          $(cont.find("th")[0]).css("display", "none");
          $(cont.find("tbody")[0])
            .find("tr")
            .each(function (i, e) {
              $(e).attr("class", "component-row");
              $(e).attr(
                "data-key",
                "/secmon/web/security-events/view?id=" +
                  $(e).find("td")[0].innerHTML
              );
              $($(e).find("td")[0]).css("display", "none");
            });
        } else if (data.contentTypeId == "barChart") {
          var width = parseInt(cont.css("width").replace("px", ""));
          cont.empty();
          DrawBarGraph(
            JSON.parse(data.data),
            width / 2,
            width,
            "#componentContentBody" + item
          );
        } else if (data.contentTypeId == "pieChart") {
          var width = parseInt(cont.css("width").replace("px", ""));
          cont.empty();
          DrawPieGraph(
            JSON.parse(data.data),
            width / 2,
            width,
            "#componentContentBody" + item
          );
        }
        // Fit item in grid
        grid.packery("fit", $("#component_" + item)[0]);
        grid.packery("shiftLayout");
      })
      .fail(function () {
        Materialize.toast("Couldn't update component content!", 4000);
      });
  }

  function getRefreshTime() {
    $.ajax({
      url: hostUrl + options.getRefreshTimes,
    }).done(function (data) {
      if (!data) {
        Materialize.toast("Refresh times not found.", 4000);
        return;
      }
      var json = JSON.parse(data);
      refreshTime = getIntervalFromArray(json[dashboardSelect.val()]);
    });
  }

  function getIntervalFromArray(refreshString) {
    if (refreshString == "0") {
      return 0;
    }
    var timeUnit = refreshString.substring(
      refreshString.length - 1,
      refreshString.length
    );
    var refreshTime = parseInt(
      refreshString.substring(0, refreshString.length - 1)
    );
    if (timeUnit == "S") {
      return refreshTime;
    }
    refreshTime *= 60;
    if (timeUnit == "m") {
      return refreshTime;
    }
    refreshTime *= 60;
    if (timeUnit == "H") {
      return refreshTime;
    }
    return refreshTime * 24;
    if (timeUnit == "D") {
      return refreshTime;
    }
    if (timeUnit == "W") {
      return refreshTime * 7;
    }
    if (timeUnit == "M") {
      return refreshTime * 30;
    }
    if (timeUnit == "Y") {
      return refreshTime * 365;
    }
  }

  //#endregion

  //#region [ Event Handlers ]

  /*
   * Event handler na ulozenie konfiguracie obsahu
   */
  function saveContentBtn_onClick() {
    var compId = $(this).attr("data-id");
    var comp = $("#component_" + compId);
    var data = $("#contentSettingsForm" + compId).serialize();
    var remBtn = $("#removeComponentContentBtn" + compId);
    var cont = $("#componentContentBody" + compId);
    var contNew = $("#componentContentBodyNew" + compId);
    var loader = cont.find("#componentLoader");
    var body = cont.find("#componentBody");
    var edit = comp.find("#contentEdit");

    $.ajax({
      url: hostUrl + options.updateComponentSettings,
      data: data,
    })
      .done(function (data) {
        if (!data) {
          Materialize.toast("Couldn't add filter to component!", 4000);
          return;
        }

        // Update komponentu
        $("#componentContentBody" + compId).attr(
          "data-type",
          data.contentTypeId
        );
        $.ajax({
          url: hostUrl + options.updateComponent,
          data: {
            componentId: compId,
            config: JSON.stringify({
              name: comp.find("#name" + compId).val(),
              width: comp.find("#width" + compId).val(),
            }),
          },
        });

        remBtn.css("display", "block");
        cont.css("display", "block");
        contNew.css("display", "none");
        loader.css("display", "inline-block");
        edit.css("display", "block");

        addActiveComponent(compId);

        if (data.contentTypeId == "table") {
          var paging = data.paging[0].count;
          var htmlData = data.html + createPagination(1, compId, paging);
          cont.html(htmlData);
          $(cont.find("table")[0]).attr("class", "striped");
          $(cont.find("th")[0]).css("display", "none");
          $(cont.find("tbody")[0])
            .find("tr")
            .each(function (i, e) {
              $(e).attr("class", "component-row");
              $(e).attr(
                "data-key",
                "/secmon/web/security-events/view?id=" +
                  $(e).find("td")[0].innerHTML
              );
              $($(e).find("td")[0]).css("display", "none");
            });
        } else if (data.contentTypeId == "barChart") {
          var width = parseInt(cont.css("width").replace("px", ""));
          cont.empty();
          DrawBarGraph(
            JSON.parse(data.data),
            width / 2,
            width,
            "#componentContentBody" + compId
          );
        } else if (data.contentTypeId == "pieChart") {
          var width = parseInt(cont.css("width").replace("px", ""));
          cont.empty();
          DrawPieGraph(
            JSON.parse(data.data),
            width / 2,
            width,
            "#componentContentBody" + compId
          );
        }

        // Fit item in grid
        grid.packery("fit", comp[0]);
        grid.packery("shiftLayout");
      })
      .fail(function () {
        Materialize.toast("Couldn't add filter to component!", 4000);
      });
  }

  /*
   * Event handler na vymazanie konfiguracie obsahu
   */
  function deleteContentBtn_onClick() {
    var compId = $(this).attr("data-id");
    var comp = $("#component_" + compId);
    var data = $("#contentSettingsForm" + compId).serialize();
    var remBtn = $("#removeComponentContentBtn" + compId);
    var cont = $("#componentContentBody" + compId);
    var contNew = $("#componentContentBodyNew" + compId);
    var loader = cont.find("#componentLoader");
    var body = cont.find("#componentBody");
    var edit = comp.find("#contentEdit");
    $.ajax({
      url: hostUrl + options.deleteComponentSettings,
      data: data,
    }).done(function (html) {
      remBtn.css("display", "none");
      cont.css("display", "none");
      loader.css("display", "none");
      body.css("display", "none");
      edit.css("display", "none");

      removeActiveComponent(compId);

      body.html("");
      contNew.css("display", "block");
    });
  }

  /*
   * Event handler na zmenu dashboardu
   */
  function dashboardSelect_onChange(e) {
    var currentRefreshTime = refreshTime;
    activeGrid.hide();
    activeGrid = $("#grid_" + this.value);
    UpdateBtnUrl(editBtn, this.value);
    UpdateBtnUrl(removeBtn, this.value);
    activeGrid.show();

    $.ajax({
      url: hostUrl + options.changeView,
      data: { viewId: this.value },
    });
    getRefreshTime();
    isInitialized = false;
    if (currentRefreshTime == 0) {
      componentUpdate();
    }
  }

  /*
   * Event handler na zmenu sirky komponentu
   */
  function widthSelect_onChange(e) {
    var selectNode = $(this);
    var gridItemNode = $("#" + selectNode.attr("data-id")).find(".grid-item");
    var compId = $(gridItemNode).parent().attr("id").split("_")[1];
    var page = 1;
    if (gridItemNode.find(".pagination").length != 0) {
      page = parseInt(
        $($(gridItemNode.find(".pagination")[0]).find(".active")[0]).find(
          "a"
        )[0].innerHTML
      );
    }

    gridItemNode.attr("class", "grid-item card " + this.value);

    gridItemNode.parent().find("form.componentForm").submit();
    singleComponentUpdate(compId, page);
  }

  /*
   * Event handler pre pridanie komponentu
   */
  function addComponentBtn_onClick(e) {
    $.ajax({
      url: hostUrl + options.createComponent,
      data: {
        viewId: dashboardSelect.val(),
        config: JSON.stringify({
          name: newComponentName,
          width: "",
        }),
        order: activeGrid.packery("getItemElements").length,
      },
    }).done(function (data) {
      if (!data) {
        Materialize.toast("Couldn't add component.", 4000);
        return;
      }

      var gridItemNode = $(data.html);
      var gridItem = $(gridItemNode).find(".grid-item")[0];
      activeGrid.append(gridItemNode).packery("appended", gridItemNode);

      // Inicializacia noveho grid itemu
      var draggie = new Draggabilly(gridItem);
      activeGrid.packery("bindDraggabillyEvents", draggie);

      activateComponent(gridItemNode);
    });
  }

  /*
   * Event handler pre vymazanie komponentu
   */
  function deleteComponentBtn_onClick(e) {
    var componentId = $(this).attr("data-id");

    $.ajax({
      url: hostUrl + options.deleteComponent,
      data: {
        componentId: componentId,
      },
    }).done(function (data) {
      if (!data) {
        Materialize.toast("Couldn't delete component.", 4000);
        return;
      }

      removeActiveComponent(componentId);
      var component = $("#component_" + componentId).find(".grid-item")[0];
      activeGrid.packery("remove", component).packery("shiftLayout");
    });
    $("#settings" + componentId).modal("close");
  }

  /*
   * Event handler pre ulozenie poradia komponentov
   */
  function saveOrder_onDragItemPositioned(e) {
    var itemElems = activeGrid.packery("getItemElements");
    var order = itemElems.map(function (item, index) {
      var form = $(item).siblings().find("form.componentForm")[0];
      return {
        id: $(form).attr("data-id"),
        order: index,
      };
    });

    $.ajax({
      url: hostUrl + options.updateOrder,
      data: {
        viewId: dashboardSelect.val(),
        componentOrder: JSON.stringify(order),
      },
    }).done(function (data) {
      if (!data) {
        Materialize.toast("Couldn't update order of components.", 4000);
        return;
      }
    });
  }

  /*
   * Event handler pre zmenu mena komponentu
   */
  function name_onFocusOut(e) {
    var input = $(this);
    var gridItemNode = $("#" + input.attr("data-id"));
    gridItemNode.find(".nameTitle").html(input.val());
    gridItemNode.find("form.componentForm").submit();
  }

  /*
   * Event handler pre update komponentu
   */
  function componentForm_onSubmit(e) {
    e.preventDefault();

    var componentId = $(this).attr("data-id");

    $.ajax({
      url: hostUrl + options.updateComponent,
      data: {
        componentId: componentId,
        config: JSON.stringify({
          name: $(this)
            .find("#name" + componentId)
            .val(),
          width: $(this)
            .find("#width" + componentId)
            .val(),
        }),
      },
    }).done(function (data) {
      if (!data) {
        //Materialize.toast("Couldn't update component.", 4000);
        return false;
      }
    });

    return false;
  }

  function contentTypeChanged() {
    var compId = $(this).attr("data-id");
    var val = $(this).val();
    var comp = $("#component_" + compId);
    var types = comp.find("[data-content-type]");
    types.each(function (i, e) {
      var element = $(e);
      var type = element.attr("data-content-type");
      var options = element.find("select, input");
      if (type == val) {
        element.show();
        options.removeAttr("disabled");
      } else {
        element.hide();
        options.attr("disabled", "disalbed");
      }
    });
  }

  //#endregion

  //#region [ Public Methods ]

  /*
   * Funckia na update id v href atribute buttonu
   */
  function UpdateBtnUrl(btn, id) {
    var href = btn.attr("href").split("=");
    href[1] = id;
    btn.attr("href", href.join("="));
  }

  /*
   * Funkcia na vykreslenie bar grafu
   */
  function DrawBarGraph(data, outboundHeight, outboundWidth, node) {
    if (!data || !outboundHeight || !outboundWidth || !node) {
      return;
    }

    // set the dimensions and margins of the graph
    var margin = { top: 20, right: 20, bottom: 40, left: 80 },
      width = outboundWidth - margin.left - margin.right,
      height = outboundHeight - margin.top - margin.bottom;

    // set the ranges
    var x = d3.scaleBand().range([0, width]).padding(0.1);
    var y = d3.scaleLinear().range([height, 0]);

    // append the svg object to the body of the page
    // append a 'group' element to 'svg'
    // moves the 'group' element to the top left margin
    var svg = d3
      .select(node)
      .append("svg")
      .attr("id", "barChart")
      .attr("width", width + margin.left + margin.right)
      .attr("height", height + margin.top + margin.bottom)
      .append("g")
      .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    var tooltip = d3
      .select(node) // select element in the DOM with id 'chart'
      .append("div") // append a div element to the element we've selected
      .attr("class", "tooltip"); // add class 'tooltip' on the divs we just selected

    tooltip
      .append("div") // add divs to the tooltip defined above
      .attr("class", "label"); // add class 'label' on the selection

    tooltip
      .append("div") // add divs to the tooltip defined above
      .attr("class", "count"); // add class 'count' on the selection

    // format the data
    data.forEach(function (d) {
      d.count = +d.count;
    });

    // Scale the range of the data in the domains
    x.domain(
      data.map(function (d) {
        return d.x;
      })
    );
    y.domain([
      0,
      d3.max(data, function (d) {
        return d.y;
      }),
    ]);

    // append the rectangles for the bar chart
    svg
      .selectAll(".bar")
      .data(data)
      .enter()
      .append("rect")
      .attr("class", "bar")
      .attr("x", function (d) {
        return x(d.x);
      })
      .attr("width", x.bandwidth())
      .attr("y", function (d) {
        return y(d.y);
      })
      .attr("height", function (d) {
        return height - y(d.y);
      })
      .attr("fill", "#039be5")
      .append("title")
      .text(function (d) {
        return d.x;
      });

    // add the x Axis
    svg
      .append("g")
      .attr("transform", "translate(0," + height + ")")
      .call(d3.axisBottom(x))
      .append("text")
      .attr("fill", "#000000")
      .attr("y", 30)
      .attr("x", width)
      .attr("font-size", "14px")
      .style("text-anchor", "end")
      .text("[HOUR MONTH-DAY]");

    // add the y Axis
    svg
      .append("g")
      .call(d3.axisLeft(y))
      .append("text")
      .attr("fill", "#000000")
      .attr("transform", "rotate(-90)")
      .attr("y", -50)
      .attr("x", 0)
      .attr("font-size", "14px")
      .style("text-anchor", "end")
      .text("[COUNT]");

    var bars = svg.selectAll(".bar");

    bars.on("mouseover", function (d) {
      // when mouse enters div
      tooltip.select(".label").html("Value: " + d.x); // set current label
      tooltip.select(".count").html("Count: " + d.y); // set current count
      tooltip.style("display", "block"); // set display
      tooltip
        .style("top", d3.event.layerY + 10 + "px") // always 10px below the cursor
        .style("left", d3.event.layerX + 10 + "px");
      d3.select(this).attr("fill", "#F44336");
    });

    bars.on("mouseout", function () {
      // when mouse leaves div
      tooltip.style("display", "none"); // hide tooltip for that element
      d3.select(this).attr("fill", "#039be5");
    });

    bars.on("mousemove", function (d) {
      // when mouse moves
      tooltip
        .style("top", d3.event.layerY + 10 + "px") // always 10px below the cursor
        .style("left", d3.event.layerX + 10 + "px"); // always 10px to the right of the mouse
    });
  }

  function DrawPieGraph(data, outboundHeight, outboundWidth, node) {
    if (!data || !outboundHeight || !outboundWidth || !node) {
      return;
    }

    var radius = Math.min(outboundWidth, outboundHeight) / 2;

    var color = d3.scaleOrdinal(d3.schemeCategory20b);

    // append the svg object to the body of the page
    // append a 'group' element to 'svg'
    // moves the 'group' element to the top left margin
    var svg = d3
      .select(node)
      .append("svg")
      .attr("id", "pieChart")
      .attr("width", outboundWidth)
      .attr("height", outboundHeight)
      .append("g")
      .attr(
        "transform",
        "translate(" + outboundWidth / 2 + "," + outboundHeight / 2 + ")"
      );

    var arc = d3
      .arc()
      .innerRadius(0) // none for pie chart
      .outerRadius(radius); // size of overall chart

    var pie = d3
      .pie() // start and end angles of the segments
      .value(function (d) {
        return d.count;
      }) // how to extract the numerical data from each entry in our dataset
      .sort(null); // by default, data sorts in oescending value. this will mess with our animation so we set it to null

    var tooltip = d3
      .select(node) // select element in the DOM with id 'chart'
      .append("div") // append a div element to the element we've selected
      .attr("class", "tooltip"); // add class 'tooltip' on the divs we just selected

    tooltip
      .append("div") // add divs to the tooltip defined above
      .attr("class", "label"); // add class 'label' on the selection

    tooltip
      .append("div") // add divs to the tooltip defined above
      .attr("class", "count"); // add class 'count' on the selection

    tooltip
      .append("div") // add divs to the tooltip defined above
      .attr("class", "percent"); // add class 'percent' on the selection

    data.forEach(function (d) {
      d.count = +d.count; // calculate count as we iterate through the data
      d.enabled = true; // add enabled property to track which entries are checked
    });

    var path = svg
      .selectAll("path") // select all path elements inside the svg. specifically the 'g' element. they don't exist yet but they will be created below
      .data(pie(data)) //associate dataset wit he path elements we're about to create. must pass through the pie function. it magically knows how to extract values and bakes it into the pie
      .enter() //creates placeholder nodes for each of the values
      .append("path") // replace placeholders with path elements
      .attr("d", arc) // define d attribute with arc function above
      .attr("fill", function (d) {
        return color(d.data.label);
      }) // use color scale to define fill of each label in dataset
      .each(function (d) {
        this._current - d;
      }); // creates a smooth animation for each track

    path.on("mouseover", function (d) {
      // when mouse enters div
      var totalCount = d3.sum(
        data.map(function (d) {
          // calculate the total number of tickets in the dataset
          return d.enabled ? d.count : 0; // checking to see if the entry is enabled. if it isn't, we return 0 and cause other percentages to increase
        })
      );
      var percentage = Math.round((10000 * d.data.count) / totalCount) / 100; // calculate percent
      tooltip.select(".label").html("Value: " + d.data.label); // set current label
      tooltip.select(".count").html("Count: " + d.data.count); // set current count
      tooltip.select(".percent").html(percentage + "%"); // set percent calculated above
      tooltip.style("display", "grid"); // set display
      tooltip
        .style("top", d3.event.layerY + 10 + "px") // always 10px below the cursor
        .style("left", d3.event.layerX + 10 + "px"); // always 10px to the right of the mouse
      d3.select(this).attr("fill", "#F44336");
    });

    path.on("mouseout", function () {
      // when mouse leaves div
      tooltip.style("display", "none"); // hide tooltip for that element
      d3.select(this).attr("fill", color(this.__data__.data.label));
    });

    path.on("mousemove", function () {
      // when mouse moves
      tooltip
        .style("top", d3.event.layerY + 10 + "px") // always 10px below the cursor
        .style("left", d3.event.layerX + 10 + "px"); // always 10px to the right of the mouse
    });
  }

  function closeModal() {
    var e = "#" + $(this).parent().parent().attr("id");
    $(e).modal("close");
  }

  function changeNameHeader() {
    var nameHeader = $(this).parent().parent().siblings(".nameHeader")[0];
    nameHeader.innerHTML = this.value + " - options";
  }

  function createPagination(page, componentId, paging) {
    var pagination = '<ul class="pagination" data-id="' + componentId + '">';

    //Pages
    var maxPage = 1;
    if (paging % 10 == 0 && paging != 0) {
      maxPage = paging / 10;
    } else if (paging != 0) {
      maxPage = paging / 10 + 1;
    }
    for (var i = page > 3 ? page - 2 : 1; i <= page + 2 && i <= maxPage; i++) {
      pagination +=
        i == page
          ? '<li class="active change-page"><a href="#!">' + i + "</a></li>"
          : '<li class="waves-effect change-page"><a href="#!">' +
            i +
            "</a></li>";
    }

    //Arrows
    pagination += "<br>";
    //First page
    pagination +=
      '<li class="waves-effect pagination-end" data-page=1><a href="#!"><i class="material-icons">first_page</i></a></li>';
    //Left
    pagination +=
      page == 1
        ? '<li class="disabled">'
        : '<li class="waves-effect pagination-step" data-direction="left">';
    pagination +=
      '<a href="#!"><i class="material-icons">chevron_left</i></a></li>';
    //Right
    pagination +=
      page == Math.floor(maxPage)
        ? '<li class="disabled">'
        : '<li class="waves-effect pagination-step" data-direction="right">';
    pagination +=
      '<a href="#!"><i class="material-icons">chevron_right</i></a></li>';
    //Last page
    pagination +=
      '<li class="waves-effect pagination-end" data-page=' +
      Math.floor(maxPage) +
      '><a href="#!"><i class="material-icons">last_page</i></a></li>';

    pagination += "</ul>";
    return pagination;
  }

  $(document).on("click", ".component-row", function () {
    window.location = $(this).data("key");
  });

  $(document).on("click", ".change-page", function () {
    var component = $(this).parent().data("id");
    var page = parseInt($(this).find("a")[0].innerHTML);
    singleComponentUpdate(component, page);
  });

  $(document).on("click", ".pagination-step", function () {
    var component = $(this).parent().data("id");
    var page = 1;
    if ($(this).siblings(".active").find("a").length != 0) {
      page = parseInt($(this).siblings(".active").find("a")[0].innerHTML);
      page += $(this).data("direction") == "left" ? -1 : 1;
    }
    singleComponentUpdate(component, page);
  });

  $(document).on("click", ".pagination-end", function () {
    var component = $(this).parent().data("id");
    var page = $(this).data("page");
    singleComponentUpdate(component, page);
  });

  //#endregion
});
