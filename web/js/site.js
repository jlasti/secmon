$(function () {
    // Initialize collapse button
    $('.button-collapse').sideNav({
        menuWidth: 250, // Default is 240
        draggable: true // Choose whether you can drag to open on touch screens
    });

    $(".compact-button").on("click", _compactBtn_onClick);
    // Inicializovanie selectov.
    $('select').material_select();
    $('.modal').modal();

    // Aktivovanie datepickera
    activateDatePicker();
    // Aktivoavnie riadkov tabulky
    activateEventsRows();

    $("body").removeClass("preload");


    //Vlozenie search ikonky do riadku flitrov
    $("table tr.filters td").first().html("<i class='material-icons'>search</i>");
});




/*
 * Inicializovanie flatpickera.
 */
function activateDatePicker(element) {

    if(element === undefined) {
        $('.rule').each(function() {
            initializeFlatpickr($(this));
        });
    } else {
        initializeFlatpickr(element);
    }
}

function initializeFlatpickr(element) {
    var flatpickrType = '';
    element.find('.input-field').each(function() {
        if($(this).attr("data-type") === "date") {
            if($(this).find('.flatpickr').length === 0) {
                var temp = $(this).find('select');
                flatpickrType = temp[0].options[temp[0].selectedIndex].value;
            } else {
                var identifier = $(this).find('input')[0];
                if(flatpickrType !== 'Last') {
                    $(identifier).attr('placeholder', 'YYYY-MM-DD HH:mm');
                    flatpickr(identifier, {
                        enableTime: true,
                        allowInput: false,
                        time_24hr: true,
                    });
                } else {
                    $(identifier).attr('placeholder', 'nY/nM/nW/nD/nH/nm/nS');
                }
            }
        }
    })
}

/*
 * Kliknutie na riadok tabulky zobrazi detail
 */
function activateEventsRows() {

    $(".clickable-table table tbody tr").one( "click", function() {
        $(this).find('a > i').click();
    });
}

/*
 * Event handler pre zmensenie/zvacsenie siderbaru.
 */
function _compactBtn_onClick (event) {
    event.preventDefault();

    $("body").toggleClass("compact");

    // ulozenie stavu sidebaru
    window.localStorage.setItem("isCompact", $("body").hasClass("compact"));
}