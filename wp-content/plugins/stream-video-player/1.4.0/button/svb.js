// pass the window to the object
var RodrigoPolo = window.RodrigoPolo || {};
// set the function
(RodrigoPolo.Tag = function() {
	return {
		// set the function
		embed : function() {
			
			// check if it's string the url and if the function tb_show works, if not, return
			if (typeof this.configUrl !== 'string' || typeof tb_show !== 'function') {
				return;
			}
			
			// pepare the url
			var url = this.configUrl + ((this.configUrl.match(/\?/)) ? "&" : "?") + "TB_iframe=true";
			
			// call lightbox to show the embed
			tb_show('Stream Video Player Tag Generator', url , false);
		}
		
	};
	
}());
/*
	Generator specific script
*/
(RodrigoPolo.Tag.Generator = function(){
	// tags to find
	var tags = 'provider,base,flv,img,hd,mp4,ogv,captions,embed,share,width,height,dock,controlbar,skin,adscode,logo,bandwidth,title,volume,autostart,streamer,opfix,gapro,playlistfile,config,playlist,repeat,playlistsize,responsive'.split(',');				  
	// to validate and generate the tag
	var vt = function(id){
		var form =  jQuery.trim(jQuery('#'+id).val());
		if(form==''){
			return '';
		}else if(isyoutube(form)){
			return ' '+id+'='+escape(form);
		}else if(id=='provider' && form=='http'){
			return '';
		}else if(id=='skin' && form=='default'){
			return '';
		}else if(id=='opfix' && form=='false'){
			return '';
		}else if(id=='playlist' && form=='false'){
			return '';
		}else if(id=='repeat' && form=='false'){
			return '';
		}else if(id=='gapro' || id=='adscode'){
			
			if(jQuery('#'+id).attr('alt') != form && form!=''){
				return ' '+id+'='+form;
			}else{
				return '';
			}
		}else{
			return ' '+id+'='+form;
		}
		//return (form=='' || form == 'false' || form == 'default')?'':' '+id+'='+escape(form);
	};
	// Check if it is a YouTube url
	var isyoutube = function(str){
		return (str.substring(0, 22)=='http://www.youtube.com' || str.substring(0, 18)=='http://youtube.com');
	};
	// Get the image preview from a YouTube URL
	var getYouTubePreview = function(url){
		var results = url.match("[\\?&]v=([^&#]*)");
		var vid = ( results === null ) ? url : results[1];
		return "http://img.youtube.com/vi/"+vid+"/0.jpg";
	}
	// to build tag
	var buildTag = function() {
		var r = '[stream'; 
		for (i=0;i<tags.length;i++){
			r += vt(tags[i]);
		}	
		return r + ' /]';
	};
	
	// get the selected text in the box
	var getSel = function(){
		var win = window.parent || window;
		var ret = '';
		if ( typeof win.tinyMCE !== 'undefined' && ( win.ed = win.tinyMCE.activeEditor ) && !win.ed.isHidden() ) {
			win.ed.focus();
			if (win.tinymce.isIE){
				win.ed.selection.moveToBookmark(win.tinymce.EditorManager.activeEditor.windowManager.bookmark);
			}
			ret = win.tinymce.EditorManager.activeEditor.selection.getContent({format : 'text'});
		} else {
			var myField = win.edCanvas;
			// IE
			if (document.selection) {
				myField.focus();
				ret = (win.document.all) ? win.document.selection.createRange().text : win.document.getSelection();			
			}
			// Moxilla, Netscape
			else if (myField.selectionStart || myField.selectionStart == '0') {
				ret = myField.value.substring(myField.selectionStart, myField.selectionEnd);
			} 
		}
		return (ret=='')?false:ret;
	};
	
	// Tag parser
	var parseTagEdit = function(){
		// get selection
		var selec = getSel();
		if(selec != false){
			// trim
			selec = selec.replace(/^\s+|\s+$/g,'');
			
			// look for the ending poing
			var endp = selec.lastIndexOf(' /]');
			// if no ending point is found
			if(endp == -1){
				return;
			}
			
			// look for the starting point
			if(selec.substring(0,8) != '[stream '){
				return;
			}

			// only params
			selec = selec.substring(8, endp);
			
			// Get the params as object
			params = proc_params(selec);
			
			// Change the params in the form
			jQuery.each(params, function(i, val) {
			  jQuery('#'+i).val(unescape(val));
			});			
			
		}
		
	};
	
	var proc_params = function(cmd){
		cmd = cmd.replace(/\s+/g, ' ')
		var tmp1 = cmd.split(' '); //explode(' ',);
		var tmp2 = [];
		var xarr = {};
		var last_key = '';
		jQuery.each(tmp1, function() {
			tmp2 = (''+this+'').split('=');//explode('=',val);
			if(tmp2.length==2){
				xarr[tmp2[0]]=tmp2[1].replace(/["']{1}/gi,"");
				last_key = tmp2[0];
			}else if(tmp2.length==1){
				xarr[last_key] +=' '+tmp2[0].replace(/["']{1}/gi,"");
			}		
		});
		return xarr;
	};
	
	// to insert tag
	var insertTag = function() {
		var tag = buildTag() || "";
		var win = window.parent || window;
				
		if ( typeof win.tinyMCE !== 'undefined' && ( win.ed = win.tinyMCE.activeEditor ) && !win.ed.isHidden() ) {
			win.ed.focus();
			if (win.tinymce.isIE){
				win.ed.selection.moveToBookmark(win.tinymce.EditorManager.activeEditor.windowManager.bookmark);
			}
						
			win.ed.execCommand('mceInsertContent', false, tag);
		} else {
			win.edInsertContent(win.edCanvas, tag);
		}
		
		// Close Lightbox
		win.tb_remove();
		
	};
	return {
		initialize : function() {
			if (typeof jQuery === 'undefined') {
				return;
			}
			jQuery("#generate").click(function(e) {
				e.preventDefault();
				insertTag();
			});
			
			// Add the image preview for youtube videos.
			jQuery("#flv").change(function() {
				var fldval = jQuery(this).val();
				if(isyoutube(fldval)){
					var prev = getYouTubePreview(fldval);
					jQuery("#img").val(prev);
					jQuery("#provider").val('youtube');
				}
			});
			
			parseTagEdit();
		}
	};
}());