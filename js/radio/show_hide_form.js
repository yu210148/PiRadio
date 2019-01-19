$(document).ready(function(){
        //$('h2').fadeIn(2000);
        //$('table').fadeIn(2000);
        //$('input').fadeIn(2000);
        //$('b').fadeIn(2000);
        //$('tr.Music').fadeIn(2000);

        });

$(function() {
	function music() {
		//$('div.hideForm').effect("drop", { direction: "up" });
        $('tr.Music').effect("fade");
	}
	function talk() {
		//$('div.hideForm').effect("slide", { direction: "up" });
        $('tr.Talk').effect("fade");
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
