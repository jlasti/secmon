$(function () {
	// Initialize collapse button
    $('.button-collapse').sideNav({
	    menuWidth: 300, // Default is 240
	    draggable: true // Choose whether you can drag to open on touch screens
    });
<<<<<<< HEAD
=======

    $('select').material_select();

    $('.datepicker').pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 15, // Creates a dropdown of 15 years to control year
        format: 'yyyy-mm-dd',
        closeOnSelect: true,
        firstDay: 1
    });
>>>>>>> 626c9dc215ba8f253c7725338640064a18fc2159
});