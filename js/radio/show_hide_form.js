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
        $('tr.Talk').hide();
        $('a.music').hide();
        $('a.talk').show();
	}
	function talk() {
		//$('div.hideForm').effect("slide", { direction: "up" });
        $('tr.Talk').effect("fade");
        $('tr.Music').hide();
        $('a.talk').hide();
        $('a.music').show();
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
