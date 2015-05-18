// function update_number_of_weeks(num_of_weeks){
//     var textInput = document.getElementById(num_of_weeks).value;
//     $.ajax({
//         type: "POST",
//         url: "update_number_of_weeks.php",
//         data: "numberOfWeeks="+textInput,
//     }).done(function( textInput ) {
//         //alert( textInput );
//     }); 
// };

function raise_volume(volUp){
    $.ajax({
        type: "POST",
        url: "raise_volume.php",
        data: "volUp="+true,
    }).done(function( true ) {
        alert( true );
    });
};