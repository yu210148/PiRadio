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

function raise_volume(){
    $.ajax({
        url: "raise_volume.php",
        type: "POST",
    });
};

/*
$.ajax({ url: '/my/site',
    data: {action: 'test'},
    type: 'post',
    success: function(output) {
        alert(output);
    }
});*/