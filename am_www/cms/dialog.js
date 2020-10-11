function Dialog(_dlg_name, mydata)
{
	var _data=mydata;
	
	this.Display=function Display() {_Display()};
	this.dlg_name=_dlg_name;
	this.ShowDialog=function ShowDialog() {_ShowDialog()};
	this.Close=function Close() {_Close()};
	this.Save=function Save() {_Save()};
	
	function _Display()
	{ 
		$("body").append('<div id="' + _dlg_name + '_lid" class="page_lid" style="visibility:hidden;">&nbsp;</div>');
		
		var msgtitle='<div class="msgbox_title" style="' + _data['TitleStyle'] + '"><div class="msgbox_title_icon" style="background-img:url(' + _data['TitleIcon'] + ')"></div><div class="msgbox_title_text">' + _data['TitleText'] + '</div></div>';
		var msgbody='<div class="msgbox_body" style="' + _data['BodyStyle'] + '">' + _data['BodyHTML'] + '</div>';
		   msgbody+='<div class="msgbox_body loadingBody" style="display:none;' + _data['BodyStyle'] + '">Loading</div>';
		var msgbtns='<input type="button" class="cancelbtn" value="Cancel" name="cancelbtn" id="cancelbtn" />';
		if (_data['SaveURL']!="") {msgbtns=msgbtns+'<input type="button" class="okbtn" name="okbtn" value="Save" />&nbsp;&nbsp;&nbsp;';}
		var msgfooter='<div class="msgbox_btns"><div class="msgbox_line_sep">&nbsp;</div><div class="msgbox_btns_container">' + msgbtns + '</div></div>';
		var myfrm='<div id="' + _dlg_name + '" class="' + _data['CssClass'] + '"  style="display:none;"  style="' + _data['Top'] + '" style="display:none;"><div class="msgbox_container" style="' + _data['DialogStyle'] + '">' + msgtitle + msgbody + msgfooter + '</div></div>';		
		$("body").append(myfrm);
		
		$("#" + _dlg_name + " .okbtn").click(_Save);
		$(".cancelbtn").click(_Close);
		$("#" + _dlg_name + "_lid").click(_Close);
		$("#" + _dlg_name).ajaxStart(function(){ $("#" + $(this).attr('id') + " .msgbox_body").hide(); $("#" + $(this).attr('id') + " .loadingBody").show(); });
		$("#" + _dlg_name).ajaxComplete(function(request, settings){  $("#" + $(this).attr('id') + " .msgbox_body").show(); $("#" + $(this).attr('id') + " .loadingBody").hide(); });
	}
	
	function _Close()
	{
		$("#" + _dlg_name).fadeOut("slow");
		$("#" + _dlg_name + "_lid").css("visibility","hidden");
	}
	
	function _Save()
	{
		$.ajax({
   			type: "POST",
   			url: _data['SaveURL'],
   			cache: false,
   			data: getstr("#" + _dlg_name + " form"),
   			success: function(msg){
     			alert( "Data Saved: " + msg );
     			eval (_data['onSave']);
     			_Close();
   			}
 		});
	}
	
	function _ShowDialog()
	{
		$("#" + _dlg_name).centerInClient({ container: window });
		$("#" + _dlg_name).fadeIn("slow");
		$("#" + _dlg_name + "_lid").css("visibility","visible");
		$("#" + _dlg_name + "_lid").height($(document).height());
	}
}
 
 function getstr(frm)
 {
 	var gstr="";
 	$(frm + " input:text").each(function (){
 		gstr += $(this).attr('name') + "=" + $(this).val() + "&";
 	});
 	$(frm + " input:hidden").each(function (){
 		gstr += $(this).attr('name') + "=" + $(this).val() + "&";
 	});
 	$(frm + " :checked").each(function (){
 		gstr += $(this).attr('name') + "=" + $(this).val() + "&";
 	});
 	$(frm + " select").each(function (){
 		gstr += $(this).attr('name') + "=" + $(this).val() + "&";
 	});
 	alert(gstr);
 	return gstr;
 }
 
 $.fn.centerInClient = function(options) {
    /// <summary>Centers the selected items in the browser window. Takes into account scroll position.
    /// Ideally the selected set should only match a single element.
    /// </summary>    
    /// <param name="fn" type="Function">Optional function called when centering is complete. Passed DOM element as parameter</param>    
    /// <param name="forceAbsolute" type="Boolean">if true forces the element to be removed from the document flow 
    ///  and attached to the body element to ensure proper absolute positioning. 
    /// Be aware that this may cause ID hierachy for CSS styles to be affected.
    /// </param>
    /// <returns type="jQuery" />
    var opt = { forceAbsolute: false,
                container: window,    // selector of element to center in
                completeHandler: null
              };
    $.extend(opt, options);
   
    return this.each(function(i) {
        var el = $(this);
        var jWin = $(opt.container);
        var isWin = opt.container == window;

        // force to the top of document to ENSURE that 
        // document absolute positioning is available
        if (opt.forceAbsolute) {
            if (isWin)
                el.remove().appendTo("body");
            else
                el.remove().appendTo(jWin.get(0));
        }

        // have to make absolute
        el.css("position", "absolute");

        // height is off a bit so fudge it
        var heightFudge = isWin ? 2.0 : 1.8;

        var x = (isWin ? jWin.width() : jWin.outerWidth()) / 2 - el.outerWidth() / 2;
        var y = (isWin ? jWin.height() : jWin.outerHeight()) / heightFudge - el.outerHeight() / 2;

        el.css("left", x + jWin.scrollLeft());
        el.css("top", y + jWin.scrollTop());

        // if specified make callback and pass element
        if (opt.completeHandler)
            opt.completeHandler(this);
    });
}


