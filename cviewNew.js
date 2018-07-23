/*Script found @ http://www.javascriptkit.com/javatutors/externalphp2.shtml
var cview;                           //Canvas Viewer
var canvas;                          //Canvas
var curimg = galleryarray.length -1; //Initialize counter for array.
var picRedSize = 80;                 //Percent of reduced size as whole number for %
var degrees = 0;
var startx=0;
var starty=0;
var cwidth=630;
var cheight=460;
var resizeTimeoutId;
var timeoutId;
var imageLoaders=new Array();
var imageWidth=0;
var imageHeight=0;
var maxWidth=0;
var maxHeight=0;
var cscale = 1.0;
var fullsize = false;
var loadct=0;
  */
function ImageViewer(canvasId)
{
	this.cview = document.getElementById(canvas).getContext("2d");
	this.curimg = galleryarray.length -1;
	this.degrees = 0;
	this.startx = 0;
	this.starty = 0;
	this.cwidth = 630;
	this.cheight = 460;
	this.imageLoaders = new Array();
	this.imageWidth = 0;
	this.imageHeight = 0;
	this.maxWidth = 0;
	this.maxHeight = 0;
	this.cscale = 1.0;
	this.fullsize = false;
	this.loadct = 0;
}

ImageViewer.prototype.prepareImage = function() {
	this.imageWidth = galleryarray[curimg].width ;
	this.imageHeight = galleryarray[curimg].height ;
	
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
	while ((fullWidth > maxWidth) || (fullHeight > maxHeight))
	{
		fullWidth = fullWidth * .98;
		fullHeight = fullHeight * .98;
	}
	fullWidth= Math.floor(fullWidth);
	fullHeight=Math.floor(fullHeight);
	imageHeight = Math.floor(imageHeight * cscale);
	imageWidth = Math.floor(imageWidth * cscale);
	
	cview.clearRect(startx,starty,cwidth,cheight);
	cview.restore();
	startx=(cwidth/2)-(imageWidth/2);
	if (fullsize)
	{
		canvas.setAttribute('height', fullHeight);
		canvas.setAttribute('width', fullWidth);
	
		cview.drawImage(galleryarray[curimg],0, 0,fullWidth,fullHeight);	
	}
	else
	{
		canvas.setAttribute('height',imageHeight);
		canvas.setAttribute('width',imageWidth);
		cview.drawImage(galleryarray[curimg],0,0,imageWidth,imageHeight);	
	}
	if ((loadct<5)&&(!fullsize))
	{
		timeoutID = window.setTimeout(prepareImage, (1000+loadct*200));
		loadct++;
	}
		
	if (loadct>=5)
	{
		getComments();
		getLicks();		
	}
	document.getElementById("filedesc").innerHTML= "<strong>" + nameString[curimg] + "</strong>  <span class='lickspan'><a class='lickbutton' href='#' onclick='lick(); return false;'></a></span> "
	if (descString[curimg].length > 0)
	{	document.getElementById("filedesc").innerHTML += "</td></tr><tr><td><br />" + descString[curimg] + "" ; }
	document.getElementById("filedesc").innerHTML += "<br><span class='username'> " + submittedBy[curimg] + " </span>";
	cview.save();
}
ImageViewer.prototype.drawSmall = function(){
	fullsize=false;
	this.prepareImage();		
}

ImageViewer.prototype.drawFull = function()
{
	fullsize=true;
	this.prepareImage();
}

function rotClock()
{
	degrees = (degrees - 90) % 360;
	drawRotate(degrees);
}

function rotCounter()
{
	degrees = (degrees + 90) % 360;
	drawRotate(degrees);
}

function drawRotate(newDegrees)
{
	degrees = newDegrees % 360;
	if (fullsize)
	{
		cview.restore();
		switch(degrees)
		{
			case 0:
				canvas.setAttribute('height', fullHeight);
				canvas.setAttribute('width', fullWidth);
				cview.restore();
				break;

			case 90:
			case -270:
				canvas.setAttribute('height', fullWidth);
				canvas.setAttribute('width', fullHeight);
				cview.translate(fullHeight,0);				
				break;
			case 180:
			case -180:
				canvas.setAttribute('height', fullHeight);
				canvas.setAttribute('width', fullWidth);
				cview.translate(fullWidth,fullHeight);
				break;
			case 270:
			case -90:
				canvas.setAttribute('height', fullWidth);
				canvas.setAttribute('width', fullHeight);
				cview.translate(0,fullWidth);
				break;
		}
		
		cview.rotate(Math.PI * degrees / 180);
		cview.drawImage(galleryarray[curimg],0,0, fullWidth, fullHeight);
	}
	else
	{
		cview.restore();
		var oriX = 0;
		var	oriY = 0;
		switch(degrees)
		{
			case 0:
				canvas.setAttribute('height', imageHeight);
				canvas.setAttribute('width', imageWidth);
				cview.translate(0,0);
				cview.restore();
				break;
			case 90:
			case -270:
				canvas.setAttribute('height', imageWidth);
				canvas.setAttribute('width', imageHeight);				
				cview.translate(imageHeight,0);								
				break;
			case 180:
			case -180:
				canvas.setAttribute('height', imageHeight);
				canvas.setAttribute('width', imageWidth);
				cview.translate(imageWidth,imageHeight);				
				break;
			case 270:
			case -90:
				canvas.setAttribute('height', imageWidth);
				canvas.setAttribute('width', imageHeight);
				cview.translate(0,imageWidth);				
				break;
		}				
		cview.rotate(Math.PI * degrees / 180);
		cview.drawImage(galleryarray[curimg],0,0, imageWidth, imageHeight);
	}	
}

function nextimage()
{
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
function previmage()
{		
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

// Get Next Preload Image
function getNextImage(prev)
{
	
	if (curimg < galleryarray.length/2)
	{
		var i = prev;
		if (i < 0) i++;
		for (i; i<galleryarray.length-1; i++)
		{
			if (galleryarray[i].src.indexOf(imageString[i]) < 0 )
			{				
				return i;
			}
		}
		return -1;
	}
	else 
	{
		var i = prev;
		if (i < 0) i=galleryarray.length-1;
		for (i; i>=0; i--)
		{
			if (galleryarray[i].src.indexOf(imageString[i]) < 0 )
			{
				return i;
			}
		}
		return -1;
	}
	return -999;
}

//Preload the image
function preloadImage()
{	
	if (pimg>=0)
	{
		galleryarray[pimg].src=imageString[pimg];
	}
}

//Timer for preloading images
function preloadImagesTimer()
{
	var timersCT = 0;
	pimg = getNextImage(-1);
	while(pimg>0)
	{
		try {
			imageLoaders[timersCT] = window.setTimeout(preloadImage(), 900);
		} catch(e) {			
		}
		timersCT++;
		pimg = getNextImage(pimg);		
	}	
}

function lickit()
{
	if (document.getElementById("lickimg").src.indexOf("lickit2.jpg") < 0)
	{
		document.getElementById("lickimg").src="lickit2.jpg";
	}
	if (document.getElementById("lickimg").src.indexOf("lickit2.jpg") >= 0)
	{
		document.getElementById("comments").innerHTML = "lickit2.jpg";
	}
}

function lickthis()
{
	if (document.getElementById("lickimg").src.indexOf("lickit.jpg") < 0)
	{
		document.getElementById("lickimg").src='lickit.jpg';
	}
	if (document.getElementById("lickimg").src.indexOf("lickit.jpg") >= 0)
	{
		document.getElementById("comments").innerHTML = "lickit.jpg";
	}
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
		//cview.drawImage(galleryarray[curimg],0, 0,galleryarray[curimg].width,galleryarray[curimg].height);
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

function toggleZoom()
{
	fullsize = !fullsize;
	if (fullsize)
	{
		canvas.setAttribute('height', galleryarray[curimg].height );
		canvas.setAttribute('width', galleryarray[curimg].width );
		prepareImage();
	}
	else 
	{
		cview.clearRect(startx,starty,galleryarray[curimg].width,galleryarray[curimg].height);		
		canvas.setAttribute('height', cheight);
		canvas.setAttribute('width', cwidth);
		cview.drawImage(galleryarray[curimg],startx,starty,imageWidth,imageHeight);		
	}
}

function canvasonkey(e)
{

	if (!e) e=event;

	if (e.keyCode==37)

	{
		previmage();
	}
	else if (e.keyCode==39)
	{
		nextimage();
	}	
}


function init(cimg)
{	
	if (cimg!=-1)
	{
		curimg = cimg;
	}
	//Create canvas to draw image to:
	canvas = document.getElementById('canvas');
	if (canvas.addEventListener)
	{	
		canvas.addEventListener("click", canvasonclick, false); 
	}
	else //IE
	{
		canvas.attachEvent("onclick", canvasonclick );
	}
    setsize();	
	
	if (document.addEventListener)
	{	
		document.addEventListener("keydown", canvasonkey, false); 
	}
	else
	{	
		document.attachEvent("onkeydown", canvasonkey); 
	}
	cview = canvas.getContext("2d");
	
	preloadImagesTimer();
	
	//Draw initial image:
	if (curimg==galleryarray.length-1)
	{
		lastimage();
	}
	else if (curimg==0)
	{
		firstimage();
	}
	else
	{
		loadimage(curimg);
	}
}