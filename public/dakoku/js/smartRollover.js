$(function(){
$('[src*="_off."]')
.mouseover(function()
{$(this).attr("src",$(this).attr("src").replace(/^(.+)_off(\.[a-z]+)$/,"$1_on$2"));})
.mouseout(function()
{$(this).attr("src",$(this).attr("src").replace(/^(.+)_on(\.[a-z]+)$/,"$1_off$2"));})
.each(function(init)
{$("<img>").attr("src",$(this).attr("src").replace(/^(.+)_off(\.[a-z]+)$/,"$1_on$2"));})
});