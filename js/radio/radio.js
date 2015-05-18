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
        url: "raise_volume.php",
        type: "POST",
        success: function(output) {
            alert(output);
        }


});
/*
$.ajax({ url: '/my/site',
    data: {action: 'test'},
    type: 'post',
    success: function(output) {
        alert(output);
    }
});*/