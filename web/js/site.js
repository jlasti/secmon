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
 * Inicializovanie datepickera.
 */
function activateDatePicker(element) {
    if (element !== undefined)
        element = $(element).find('.datepicker');
    else
        element = '.datepicker';

    $(element).pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 15, // Creates a dropdown of 15 years to control year
        format: 'yyyy-mm-dd',
        closeOnSelect: true,
        firstDay: 1
    });
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