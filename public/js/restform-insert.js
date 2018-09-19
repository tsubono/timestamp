/// <reference path="./../typings/jquery/jquery.d.ts" />
function addForm()
{
  var bodyNode = document.getElementsByTagName('div').item(0);
  var title3Node = document.getElementById('clock');
  var newNode = document.createElement('div');

  var textNode = document.createTextNode("<br /><br /><div class=\"form-group\">test</div>");

/*<label class=\"font-normal col-xs-12 col-sm-2 control-label\">休憩</label>
<div class=\"col-xs-10 col-sm-6\">
<input type=\"text\" name=\"rest\" value=\"\" class=\"form-control\" placeholder=\"休憩時刻\">
</div>
<br />
<br />
<label class=\"font-normal col-xs-12 col-sm-2 control-label\">復帰</label>
<div class=\"col-xs-10 col-sm-6\">
<input type=\"text\" name=\"resume\" value=\"\" class=\"form-control\" placeholder=\"復帰時刻\">
</div>
</div>
<br />
<br />*/
  title3Node.appendChild(textNode);
  title3Node.id = 'rest_resume';
  bodyNode.appendChild(newNode);
}
