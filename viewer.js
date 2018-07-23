//Script found @ http://www.javascriptkit.com/javatutors/externalphp2.shtml
var cview;                           //Canvas Viewer
var canvas;                          //Canvas
var curimg = 0;                      //Initialize counter for array.
var picRedSize = 80;                 //Percent of reduced size as whole number for %
var startx=0;
var starty=0;
var cwidth=630;
var cheight=460;
var resizeTimeoutId;
var timeoutId;
var imageWidth=0;
var imageHeight=0;
var maxWidth=0;
var maxHeight=0;
var cscale = 1.0;
var fullsize = false;
var loadct=0;
var rooturl = "http://tsbac.dreamersnet.net/";

function ImageObject() {
	this.width = 0;
	this.height = 0;
	this.src = "";
	this.id= "";
	this.img = new Image();
	this.loaded = false;

	ImageObject.prototype.getClientWidth = function() {
		if (document.body && document.body.offsetWidth)
			return document.body.offsetWidth;
		if (document.compatMode=='CSS1Compat' && document.documentElement && document.documentElement.offsetWidth )
		{
			return document.documentElement.offsetWidth;
		}
		if (window.innerWidth && window.innerHeight)
			return window.innerWidth;
		return -1;
	};

	ImageObject.prototype.getClientHeight = function() {
		if (document.body && document.body.offsetHeight)
			return document.body.offsetHeight;
		if (document.compatMode=='CSS1Compat' && document.documentElement && document.documentElement.offsetHeight )
			return document.documentElement.offsetHeight;
		if (window.innerHeight)
			return window.innerHeight;

		//if it hasn't already returned anything this won't work anyway
		return -1;
	};

	ImageObject.prototype.setID = function(newID)
	{
		id= newID;
	};

	ImageObject.prototype.load = function() {

	}
}


 //Function to go to next image in picture viewer (basically a go to next image function) this is looped by the setInterval later.
//Author: unknown (JavaScriptKit)
//Edited: Ben Parker - rewrote if statement.  I think it's easier to read without the short-hand method.

//go to newer (first is newest)
function drawSmall(){
	fullsize=false;
	cview.clearRect(startx,starty,galleryarray[curimg].width,galleryarray[curimg].height);
	canvas.setAttribute('height', cheight);
	canvas.setAttribute('width', cwidth);
	cview.drawImage(galleryarray[curimg],startx,starty,imageWidth,imageHeight);
}

function nextimage(){
	drawSmall();
	if (curimg < galleryarray.length-1)   //check to see if we are at the end of the array
	{
		curimg++;
		document.getElementById("btnFirst").disabled=false;
		document.getElementById("btnPrev").disabled=false;
	}
	prepareImage();
	if (curimg == galleryarray.length-1)
	{
		document.getElementById("btnLast").disabled=true;
		document.getElementById("btnNext").disabled=true;
	}

}

function loadimage(cimg)
{
	if (cimg > 0)
	{
		document.getElementById("btnLast").disabled=false;
		document.getElementById("btnNext").disabled=false;
	}
	prepareImage();
	if (cimg == 0)
	{
		document.getElementById("btnFirst").disabled=true;
		document.getElementById("btnPrev").disabled=true;
	}
}

//go to older (last is oldest)
function previmage() {
	drawSmall();
	if (curimg > 0)
	{
		curimg--;
		document.getElementById("btnLast").disabled=false;
		document.getElementById("btnNext").disabled=false;
	}
	prepareImage();
	if (curimg == 0)
	{
		document.getElementById("btnFirst").disabled=true;
		document.getElementById("btnPrev").disabled=true;
	}
}


//Most recent image
function firstimage() {
	curimg = 0;
	prepareImage();
	document.getElementById("btnFirst").disabled=true;
	document.getElementById("btnPrev").disabled=true;
	document.getElementById("btnLast").disabled=false;
	document.getElementById("btnNext").disabled=false;
}

function prepareImage()
{
	imageWidth= galleryarray[curimg].width ;
	imageHeight= galleryarray[curimg].height ;

	var hscale = cheight / imageHeight;
	var wscale = cwidth / imageWidth;
	if (hscale < wscale)
	{
		cscale = hscale;
	}
	else
	{
		cscale = wscale;
	}

	fullWidth = maxWidth;
	fullHeight = (maxWidth/imageWidth)*imageHeight;
	alert ("full width:" + fullWidth + "\nfull height:" + fullHeight + "\ncwidth:" + cwidth + "\ncheight" + cheight);
	imageHeight = Math.floor(imageHeight * cscale);
	imageWidth = Math.floor(imageWidth * cscale);

	cview.clearRect(startx,starty,cwidth,cheight);
	startx=(cwidth/2)-(imageWidth/2);
	if (fullsize)
	{
		cview.drawImage(galleryarray[curimg],0,0,maxWidth,maxHeight);
	}
	else
	{
		cview.drawImage(galleryarray[curimg],startx,starty,imageWidth,imageHeight);
	}
	if ((loadct<5)&&(!fullsize))
	{
		timeoutID = window.setTimeout(prepareImage, (1000+loadct*200));
		loadct++;
	}




	//Change link for Comment button.
	document.getElementById("commentClick").setAttribute("onclick","location.href='addcomment.php?fileName=" + galleryarray[curimg].id + ".jpg&image=" + curimg +"'");
	getComments();
}

//Oldest image
function lastimage() {
	curimg = galleryarray.length-1;
	prepareImage();
	document.getElementById("btnLast").disabled=true;
	document.getElementById("btnNext").disabled=true;
	document.getElementById("btnFirst").disabled=false;
	document.getElementById("btnPrev").disabled=false;
}

function setsize()
{
	//Info can be found at: http://www.javascripter.net/faq/browserw.htm
	if (document.body && document.body.offsetWidth) {
	 cwidth = document.body.offsetWidth;
	 cheight = document.body.offsetHeight;
	}
	if (document.compatMode=='CSS1Compat' &&
	    document.documentElement &&
	    document.documentElement.offsetWidth ) {
	 cwidth = document.documentElement.offsetWidth;
	 cheight = document.documentElement.offsetHeight;
	}
	if (window.innerWidth && window.innerHeight) {
	 cwidth = window.innerWidth  ;
	 cheight = window.innerHeight ;
	}
	maxWidth = cwidth;
	maxHeight = 2 * cheight;
	alert("cwidth: " + cwidth + " cheight: " + cheight);
	cwidth = Math.floor(cwidth *(picRedSize/100.0));
	cheight = Math.floor(cheight * (picRedSize/100.0));
	canvas.setAttribute('height', cheight);
	canvas.setAttribute('width', cwidth);
}

function canvasonclick(e)
{

	if (!(fullsize))
	{
		//Find out where the click happened
		var x;
		var y;
		if (e.pageX != undefined && e.pageY != undefined)
		{
			x = e.pageX;
			y = e.pageY;
		}
		else
		{
			x = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
			y = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
    	}
    	x -= canvas.offsetLeft;
    	y -= canvas.offsetTop;
    	x -= startx;
    	if ((x>=0)&&(x<=imageWidth)&&(y<=imageHeight)&&(y>=0))
    	{
			// make fullsize
			fullsize= !fullsize;
			canvas.setAttribute('height', galleryarray[curimg].height );
			canvas.setAttribute('width', galleryarray[curimg].width );
			prepareImage();
			//cview.clearRect(startx,starty,galleryarray[curimg].width,galleryarray[curimg].height);
			//cview.drawImage(galleryarray[curimg],0,0,galleryarray[curimg].width,galleryarray[curimg].height);
		}
	}
	else
	{
		//make small
		fullsize = !fullsize;

		cview.clearRect(startx,starty,galleryarray[curimg].width,galleryarray[curimg].height);
		canvas.setAttribute('height', cheight);
		canvas.setAttribute('width', cwidth);
		cview.drawImage(galleryarray[curimg],startx,starty,imageWidth,imageHeight);
	}
}

function canvasonkey(e)
{
	if (!e) e=event;
	if (!fullsize)
	{
		if (e.keyCode==37)
		{
			previmage();
		}
		else if (e.keyCode==39)
		{
			nextimage();
		}
	}
}


function init()
{
	//Create canvas to draw image to:
	canvas = document.getElementById('canvas');
	setsize();
	if (canvas.addEventListener)
	{	canvas.addEventListener("click", canvasonclick, false); }
	else
	{	canvas.attachEvent("onclick", canvasonclick ); }

	if (document.addEventListener)
	{	document.addEventListener("keydown", canvasonkey, false); }
	else
	{	document.attachEvent("onkeydown", canvasonkey); }
	cview = canvas.getContext("2d");
	<?php if ($curimage) echo 'curimg = ' .$curimage ?>;

	//Draw initial image:
	if (curimg==0)
	{
		firstimage();
	}
	else
	{
		loadimage(curimg);
	}
}
