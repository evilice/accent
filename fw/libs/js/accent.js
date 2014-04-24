/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function g2p(url) {window.location=url;}

$(function() {
    $('textarea.tinymce').tinymce({
        // Location of TinyMCE script
        script_url : '/fw/libs/js/tinymce/tiny_mce.js',
        language : "ru",

        // General options
        theme : "advanced",
        plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",

        // Theme options
        theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,
        template_replace_values : {
                username : "Some User",
                staffid : "991234"
        },
        file_browser_callback : 'elFinderBrowser'
    });
});

String.prototype.replaceAll = function(find, replace) {
    return this.toString().split(find).join(replace);
}

function elFinderBrowser (field_name, url, type, win) {
    var elfinder_url = '/fw/libs/php/elfinder/elfinder.html';    // use an absolute path!
    tinyMCE.activeEditor.windowManager.open({
        file: elfinder_url,
        title: 'elFinder 2.0',
        width: 900,
        height: 450,
        resizable: 'yes',
        inline: 'yes',    // This parameter only has an effect if you use the inlinepopups plugin!
        popup_css: false, // Disable TinyMCE's default popup CSS
        close_previous: 'no'
    }, {
        window: win,
        input: field_name
    });
    return false;
}