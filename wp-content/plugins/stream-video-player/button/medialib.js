jQuery(document).ready(function($){
	// Enable the file tree jq plug-in
	$("#browser").treeview({
		collapsed: true,
		//animated: "fast",
		unique: true,
		persist: "cookie",
		cookieId: "stream-video-player"
	});
	// Open the first tree
	//$("#uplds").click();
	// Get the HREF
	$("#browser a").click(function(e) {
		e.preventDefault();
		var href = absPath($(this).attr("href"));
		
		// send to the form
		parent.chfld(href);
		
		// show the file selected
		var cfpath = (href.substring(0, href.lastIndexOf('/')));
		if(href == cfpath+'/'){
			href = cfpath; 
		}
		var cfar = href.split('/');
		fln = cfar[cfar.length-1];
		if(fln.length>22){
			fln = fln.substring(0,22)+'&hellip;';
		}
		$('#fl').html(fln);
		
	});
	// Change classes for file type icons
	$("#browser a").each(function() {
		var dat = $(this).parent().attr("class");
		var ext = $(this).attr("rel");
		$(this).parent().attr("class",dat+' '+ext);

	});
	// The Absolute Path Fixer
	function absPath(url){
		
		if(url.substring(0,7)=='http://' || url.substring(0,8)=='https://'){
			return url;
		}
		
		var Loc = location.href;	
		Loc = Loc.substring(0, Loc.lastIndexOf('/'));
		while (/^\.\./.test(url)){		 
			Loc = Loc.substring(0, Loc.lastIndexOf('/'));
			url= url.substring(3);
		}
		return Loc + '/' + url;
	}
});