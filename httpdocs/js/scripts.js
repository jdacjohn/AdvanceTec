//------------------------------------------------------------------------------------//


//------------------- OPEN NEW WINDOW -------------------//
var newWindow = null;

function closeWin(){
	if (newWindow != null){
		if(!newWindow.closed)
			newWindow.close();
	}
}

function popUpWin(url, type, strWidth, strHeight){
	closeWin();
	if (type == "fullScreen"){
		strWidth = screen.availWidth - 10;
		strHeight = screen.availHeight - 160;
	}
	var tools="";
	if (type == "standard" || type == "fullScreen") tools = "resizable=no,toolbar=no,location=no,scroll=yes,scrollbars=yes,menubar=no,width="+strWidth+",height="+strHeight+",top=0,left=0";
	if (type == "console") tools = "resizable=no,toolbar=no,location=no,directories=no,status=no,scroll=no,scrollbars=no,menubar=no,width="+strWidth+",height="+strHeight+",left=0,top=0";
	newWindow = window.open(url, 'newWin', tools);
	newWindow.focus();
}

function popUpSWF(url, type, Width, Height, arg1, arg2, arg3){
	closeWin();
	tools = "resizable=no,toolbar=no,location=no,directories=no,status=no,scroll=no,scrollbars=no,menubar=no,width="+Width+",height="+Height+",left=0,top=0";
	if(type == "mp"){
		newWindow = window.open(url+"?bandwidth="+arg1+"&video="+arg2+"&lan="+arg3, 'newWin', tools);
	}
	if(type == "mc"){
		newWindow = window.open(url+"?category="+arg1+"&videoNum="+arg2, 'newWin', tools);
	}
	if(type == "bna"){
		newWindow = window.open(url+"?procedure="+arg1+"&caseNum="+arg2, 'newWin', tools);
	}
	newWindow.focus();
}


//------------------- for clearing and replacing text in form input fields and textareas -------------------//
function clearText(thefield) {
  if (thefield.defaultValue==thefield.value) { thefield.value = "" }
} 
function replaceText(thefield) {
  if (thefield.value=="") { thefield.value = thefield.defaultValue }
}


//---------------------ActiveX--EMBED-------------------//
function printActiveX(source, width, height, id) {
	document.write('<object classid="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95" width="'+width+'" height="'+height+'" id="'+id+'" />');
	document.write('<param name="FileName" value="'+source+'" />');
	document.write('<param name="Autostart" value="True" />');
	document.write('<param name="ShowControls" value="True" />');
	document.write('<param name="ShowStatusBar" value="False" />');
	document.write('<param name="ShowDisplay" value="False"/>');
	document.write('<param name="AutoRewind" value="True" />');
	document.write('<embed src="'+source+'" width="'+width+'" height="'+height+'" />');
	document.write('</object>');
}


//------------------- Son-Of-Sucker-Fish IE Hack -------------------//
sfHover = function() {
	var sfEls = document.getElementById("nav").getElementsByTagName("LI");
	// for each list item in the menu...
	for (var i=0; i < sfEls.length; i++) {
		// Is this IE7?  If so, use onmouseleave to fix the fact that onmouseout won't fire
		is_IE7 = navigator.appVersion.indexOf("MSIE 7.0") != -1;

		sfEls[i].onmouseover = function() {
			this.className+=" sfHover";
			// is this a top-level menu item?
			var child_ul = this.getElementsByTagName('ul')[0];
			if (child_ul && is_IE7){
				// fix for IE7
				child_ul.style.position = 'static';
			}
		}

		sfEls[i].onmouseleave = function() {
			// is this a top-level menu item?
			var child_ul = this.getElementsByTagName('ul')[0];
			if (child_ul && is_IE7){
				// fix for IE7
				child_ul.style.position = 'absolute';
				child_ul.style.left = '-9000px';
			}
		}

		sfEls[i].onmouseout = function() {
			this.className=this.className.replace(new RegExp(" sfHover\\b"), "");
		}

	}
}
if (window.attachEvent) window.attachEvent("onload", sfHover);


//------------------- Form Validation -------------------//
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function YY_checkform() { //v4.71
//copyright (c)1998,2002 Yaromat.com
  var a=arguments,oo=true,v='',s='',err=false,r,o,at,o1,t,i,j,ma,rx,cd,cm,cy,dte;
  for (i=1; i<a.length;i=i+4){
    if (a[i+1].charAt(0)=='#'){r=true; a[i+1]=a[i+1].substring(1);}else{r=false}
    o=MM_findObj(a[i].replace(/\[\d+\]/ig,""));
    o1=MM_findObj(a[i+1].replace(/\[\d+\]/ig,""));
    v=o.value;t=a[i+2];dv = o.defaultValue;
    if (o.type=='text'||o.type=='password'||o.type=='hidden'){
      if ((r&&v.length==0)||v==dv){err=true}
      if (v.length>0)
      if (t==1){ //fromto
        ma=a[i+1].split('_');if(isNaN(v)||v<ma[0]/1||v > ma[1]/1){err=true}
      } else if (t==2){
        rx=new RegExp("^[\\w\.=-]+@[\\w\\.-]+\\.[a-zA-Z]{2,4}$");if(!rx.test(v))err=true;
      } else if (t==3){ // date
        ma=a[i+1].split("#");at=v.match(ma[0]);
        if(at){
          cd=(at[ma[1]])?at[ma[1]]:1;cm=at[ma[2]]-1;cy=at[ma[3]];
          dte=new Date(cy,cm,cd);
          if(dte.getFullYear()!=cy||dte.getDate()!=cd||dte.getMonth()!=cm){err=true};
        }else{err=true}
      } else if (t==4){ // time
        ma=a[i+1].split("#");at=v.match(ma[0]);if(!at){err=true}
      } else if (t==5){ // check this 2
            if(o1.length)o1=o1[a[i+1].replace(/(.*\[)|(\].*)/ig,"")];
            if(!o1.checked){err=true}
      } else if (t==6){ // the same
            if(v!=MM_findObj(a[i+1]).value){err=true}
      }
    } else
    if (!o.type&&o.length>0&&o[0].type=='radio'){
          at = a[i].match(/(.*)\[(\d+)\].*/i);
          o2=(o.length>1)?o[at[2]]:o;
      if (t==1&&o2&&o2.checked&&o1&&o1.value.length/1==0){err=true}
      if (t==2){
        oo=false;
        for(j=0;j<o.length;j++){oo=oo||o[j].checked}
        if(!oo){s+='* '+a[i+3]+'\n'}
      }
    } else if (o.type=='checkbox'){
      if((t==1&&o.checked==false)||(t==2&&o.checked&&o1&&o1.value.length/1==0)){err=true}
    } else if (o.type=='select-one'||o.type=='select-multiple'){
      if(t==1&&o.selectedIndex/1==0){err=true}
    }else if (o.type=='textarea'){
      if(v.length<a[i+1]){err=true}
    }
    if (err){s+=a[i+3]+'\n'; err=false}
  }
  if (s!=''){alert('Please complete the following required fields:\t\t\t\t\t\n\n'+s)}
  document.MM_returnValue = (s=='');
}