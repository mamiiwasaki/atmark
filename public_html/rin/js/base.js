var firstopenflag=false;
var openflag=true;
var playflag=false;
var msie=navigator.appVersion.toLowerCase();

msie=(msie.indexOf("msie")>-1) ? parseInt(msie.replace(/.*msie[ ]/,"").match(/^[0-9]+/)) : 0;

(function(d,e,j,h,f,c,b){
	d["GoogleAnalyticsObject"]=f;
	d[f]=d[f]||function(){
		(d[f].q=d[f].q||[]).push(arguments);
	}
	,d[f].l=1*new Date();
	c=e.createElement(j),b=e.getElementsByTagName(j)[0];
	c.async=1;
	c.src=h;
	b.parentNode.insertBefore(c,b);
}
)(window,document,"script","//www.google-analytics.com/analytics.js","ga");
ga("create","UA-6726846-15","auto");
ga("send","pageview");

if(document.getElementById("demo")){
	document.write('<script type="text/javascript" src="http://b.st-hatena.com/js/bookmark_button.js" charset="utf-8" async="async"><\/script>');
	document.write('<script type="text/javascript" src="https://apis.google.com/js/plusone.js"><\/script>');
	!function(e,a,f){
		var c,b=e.getElementsByTagName(a)[0];
		if(!e.getElementById(f)){
			c=e.createElement(a);
			c.id=f;
			c.src="//platform.twitter.com/widgets.js";
			b.parentNode.insertBefore(c,b);
		}
	}
	(document,"script","twitter-wjs");
(function(e,a,f){
var c,b=e.getElementsByTagName(a)[0];

if(e.getElementById(f)){
	return;
}
c=e.createElement(a);
c.id=f;
c.src="//connect.facebook.net/ja_JP/all.js#xfbml=1";
b.parentNode.insertBefore(c,b);
}
	(document,"script","facebook-jssdk"));
}else{
	if(document.getElementById("google")){
		document.getElementById("google").onclick=function(){
		window.open(this.href,"googlewin","width=650, height=450, menubar=no, toolbar=no, scrollbars=yes");
	return false;
}
;
	document.getElementById("hatena").onclick=function(){
	window.open(this.href,"hatenawin","width=550, height=450,personalbar=0,toolbar=0,scrollbars=1,resizable=1");
	return false;
}
;
	document.getElementById("facebook").onclick=function(){
	window.open(this.href,"facebookwin","left=50,top=50,width=600,height=350,toolbar=0");
	return false;
}
;
	document.getElementById("twitter").onclick=function(){
	window.open(this.href,"twitterwin","left=50,top=50,width=550, height=450,personalbar=0,toolbar=0,scrollbars=1,resizable=1");
	return false;
}
;
}
}
var imgObj=document.getElementsByTagName("img");
for(i=1;i<imgObj.length;i++){
	if(-1<imgObj[i].src.indexOf("-off")){
		imgObj[i].onmouseover=function(){
		this.src=this.src.replace("-off","-on");
	}
	;
	imgObj[i].onmouseout=function(){
	this.src=this.src.replace("-on","-off");
}
;
}
}






function searchPopup(){
	var a=createXMLHttp();
	if(a){
		a.onreadystatechange=function(){
		if(a.readyState==4&&a.status==200){
			if(a.responseText==""){
				document.getElementById("pop_result").innerHTML="<p>そのキーワードではヒットしませんでした。</p>";
			}else{
				document.getElementById("pop_result").innerHTML=a.responseText;
				var c=document.getElementById("pop_result").getElementsByTagName("span");
				for(var b=0;b<c.length;b++){
					c[b].onclick=function(){
						if(msie<10&&msie>6){
							ie8Play(this);
						}else{
							defaultPlay(this);
						}
					}
				;
				} // for b
			} // if responseText
		} // if readystate
	} // if a
	;
	a.open("get","/sound/ajax.php?searchtext="+encodeURI(document.getElementById("searchtext").value),true);
	a.send(null);
	}
} // searchPopup





function createXMLHttp(){
	try{
		return new XMLHttpRequest;
	}catch(a){
		try{
			return new ActiveXObject("Microsoft.XMLHTTP");
		}catch(a){
		}
	}
} // createXMLHttp







var timer=0;
var count=1300;
function popupClose(){
	document.getElementById("pop_result").style.display="none";
	clearInterval(timer);
}
	var elements=document.getElementById("wrap").getElementsByTagName("span");
	if(msie<10&&msie>6){
		document.write('<bgsound id="bgsound" src="" loop="1">');
		for(i=0;i<elements.length;i++){
			elements[i].onclick=function(){
			ie8Play(this);
		}
	;
	}
}else{
	var playing="";
	var se=new Audio;
	for(i=0;i<elements.length;i++){
		elements[i].onclick=function(){
		defaultPlay(this);
	}
	;
	}
}







function ie8Play(b){
	var d=b.className;
	if(d=="playing"){
		document.getElementById("bgsound").src="";
		b.className="";
	}else{
		var a=document.getElementById("wrap").getElementsByTagName("span");
		for(i=0;	i<a.length;	i++){
			a[i].className="";
		}
		var c=document.getElementById("pop_result").getElementsByTagName("span");
		for(i=0;i<c.length;i++){
			c[i].className="";
		}
		b.className="playing";
		document.getElementById("bgsound").src="http://soundeffect-lab.info/"+b.id+".mp3";
	}
}




function defaultPlay(b){
	var d=b.className;
	if(d=="playing"){
		se.pause();
		b.className="";
	}else{
		var a=document.getElementById("wrap").getElementsByTagName("span");
		for(i=0;i<a.length;i++){
			a[i].className="";
		}
		var c=document.getElementById("pop_result").getElementsByTagName("span");
		for(i=0;i<c.length;i++){
			c[i].className="";
		}
		b.className="playing";
		se.src="http://soundeffect-lab.info/"+b.id+".mp3";
		se.addEventListener("ended",seEnded,false);
		se.play();
		playing=b.id;
	}
}





function seEnded(){
	var a=document.getElementById("wrap").getElementsByTagName("span");
	for(i=0;i<a.length;i++){
		a[i].className="";
	}
	var a=document.getElementById("pop_result").getElementsByTagName("span");
	for(i=0;i<a.length;i++){
		a[i].className="";
	}
}



document.getElementById("searchtext").value="効果音を検索";
document.getElementById("searchtext").onclick=function(){
if(this.value=="効果音を検索"){
	this.style.color="#333";
	this.value="";
	animation();
}
}
;
var element=document.createElement("div");
element.id="pop_result";
element.className="popmenu";
element.innerHTML="";
element.onmouseover=function(){
clearInterval(timer);
}
;
element.onmouseout=function(){
clearInterval(timer);
timer=setInterval("popupClose()",count);
}
;
var objBody=document.getElementsByTagName("body").item(0);
objBody.appendChild(element);
if(navigator.userAgent.indexOf("MSIE 6.0")!=-1||navigator.userAgent.indexOf("MSIE 7.0")!=-1){
	if(document.getElementById("topimage")){
		element.style.top=document.getElementById("serachform").offsetTop+37+"px";
		element.style.left=document.getElementById("serachform").offsetLeft+63+"px";
	}else{
		element.style.top=document.getElementById("wrap").offsetTop+document.getElementById("searchtext").offsetTop+9+"px";
		element.style.left=document.getElementById("container").offsetLeft+16+"px";
	}
}else{
	if(navigator.userAgent.indexOf("MSIE")!=-1){
		if(document.getElementById("topimage")){
			element.style.top=document.getElementById("serachform").offsetTop+37+"px";
			element.style.left=document.getElementById("serachform").offsetLeft+63+"px";
		}else{
			element.style.top=document.getElementById("searchtext").offsetTop+25+"px";
			element.style.left=document.getElementById("searchtext").offsetLeft+"px";
		}
	}else{
		if(document.getElementById("topimage")){
			element.style.top=document.getElementById("serachform").offsetTop+37+"px";
			element.style.left=document.getElementById("serachform").offsetLeft+63+"px";
		}		else{
			element.style.top=document.getElementById("searchtext").offsetTop+26+"px";
			element.style.left=document.getElementById("searchtext").offsetLeft+"px";
		}
	}
}
element.style.display="none";




function animation(){
	if(document.getElementById("pop_result").style.display=="block"){
		searchPopup();
	}else{
		document.getElementById("pop_result").style.display="block";
		searchPopup();
	}
}


document.getElementById("searchtext").onkeydown=function(){
	animation();
}
;
document.getElementById("searchtext").onkeyup=function(){
	animation();
}
;
document.getElementById("searchtext").onkeypress=function(){
	animation();
}
;
if(!(msie<10&&msie>6)){
	document.getElementById("searchtext").onblur=function(){
	animation();
}
;
document.getElementById("searchtext").onchange=function(){
	animation();
}
;
}



document.getElementById("searchsubmit").onclick=function(){
	if(document.getElementById("searchtext").value==""||document.getElementById("searchtext").value=="効果音を検索"){
		alert("検索キーワードを入力してください。");
		return false;
	}
}
;


if(document.getElementById("searchtext").value==""){
	document.getElementById("searchtext").style.color="#999999";
}


document.getElementById("searchtext").onmouseout=function(){
	clearInterval(timer);
	timer=setInterval("popupClose()",count);
}
;

if(document.getElementById("wrap").className=="loading"){
	document.getElementById("wrap").className="";
}else{
	document.getElementById("wrap").className="loading";
}
