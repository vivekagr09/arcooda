<script type="text/javascript">
//<![CDATA[
// jQuery stuff to do all the member preferences...
$(function() {
	// initialise the checkboxes..
	$(".pref_check").click(function() {
		var tmp=$(this).attr('id');
		var id=tmp.substr(12,(tmp.length-12));

		// if prefs is visible, set color to FFAAAA; else if checkbox is checked, set color to EEFFEE; else, set color to white
		var checked=($(this).is(":checked"));
		var viewId=$.inArray(id,["2","9","16","20"]);
		$("#site_pref_"+id).css("background-color", ("#"+ (($("#prefs_"+id).css('display')=='block') ? "FFAAAA" : (checked ? "EEFFEE" : "FFF"))));
		$("#a_"+id).css("display",((viewId>=0 && checked)?"":"none"));

		// now have to update the discount, if set for that particular site...
		var disc=$('#discount_'+id).val();
		$("#discount_span_"+id).html((viewId>=0 && checked && disc!='0') ? disc+'% discount' : '');
	});
	// now initialise the "View" links
	$(".view_link").click(function() {
		var tmp=$(this).attr('id');
		var id=tmp.substr(2,(tmp.length-2));

		// hide all the boxes first + the initial message...
		$("#message_first").hide();
		// re-colour the rows...
		$(".pref_row").each(function(i) {
			var tmp=$(this).attr('id');
			var id=tmp.substr(10,(tmp.length-10));

			// need to set the colour if the input is checked...
			$(this).css("background-color",(($("#member_site_"+id+":checked").length)?"#EEFFEE":"#FFF"));
		});
		$(".prefs_box").hide();

		// now the display the box corresponding site & highlight the selected row...
		$("#prefs_"+id).show("slow");
		$("#site_pref_"+id).css('background-color',"#FFAAAA");
		return false;
	});
	// now initialise the discount drop-down
	$(".discount_select").change(function() {
		var tmp=$(this).attr('id');
		var id=tmp.substr(9,(tmp.length-9));

		var disc=$(this).val();
		$("#discount_span_"+id).html((disc!='0')?disc+'% discount':'');
	});
});
//]]>
</script>
