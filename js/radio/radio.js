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

function lower_volume(){
    $.ajax({
        url: "lower_volume.php",
        type: "POST",
    });
};

function stop_player(){
    $.ajax({
        url: "stop_player.php",
        type: "POST",
    });
    $(".NowPlaying").hide("slow");
};

/*
$.ajax({ url: '/my/site',
    data: {action: 'test'},
    type: 'post',
    success: function(output) {
        alert(output);
    }
});*/