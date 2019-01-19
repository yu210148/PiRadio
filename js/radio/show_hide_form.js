$(document).ready(function(){
        //$('h2').fadeIn(2000);
        //$('table').fadeIn(2000);
        //$('input').fadeIn(2000);
        //$('b').fadeIn(2000);
        $('tr.Music').hide();

        });

$(function() {
	function music() {
		//$('div.hideForm').effect("drop", { direction: "up" });
        $('tr.Music').effect("fade");
        $('div.musicButton').effect("fade");
	}
	function talk() {
		//$('div.hideForm').effect("slide", { direction: "up" });
        $('tr.Talk').effect("fade");
        $('div.talkButton').effect("fade");
	}
$( "#talk" ).click(function() {
	talk();
	return false;
	});
$( "#music" ).click(function() {
	music();
	return false;
	});
});
