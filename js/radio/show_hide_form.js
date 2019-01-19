$(document).ready(function(){
        //$('h2').fadeIn(2000);
        //$('table').fadeIn(2000);
        //$('input').fadeIn(2000);
        //$('b').fadeIn(2000);
        $('tr.music').fadeIn(2000);

        });

$(function() {
	function hide() {
		//$('div.hideForm').effect("drop", { direction: "up" });
        $('tr.music').effect("fade");
	}
	function show() {
		//$('div.hideForm').effect("slide", { direction: "up" });
        $('tr.music').effect("fade");
	}
$( "#hide" ).click(function() {
	hide();
	return false;
	});
$( "#show" ).click(function() {
	show();
	return false;
	});
});
