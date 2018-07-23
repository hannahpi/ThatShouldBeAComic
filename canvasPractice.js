var cview ;
var picRedSize = 80;                 //Percent of reduced size as whole number for %
var shapes = new Array();
var shapeCT = 0;
var startx=0;
var starty=0;
var cwidth=630;
var cheight=460;

function CObject(x,y) {
	this.x = x;
	this.y = y;	
	//this.shape = shape;
	
	CObject.prototype.x;
	CObject.prototype.y;
	//CObject.prototype.shape;
	
	
	CObject.prototype.draw= function() {
		if (this.x < 10)
		{
			cview.fillRect(this.x, this.y, 20,20);
		}
		else
		{
			cview.fillRect(this.x-10, this.y-10, 20,20); 
		}		
	}
	
	
}

function canvasonclick(e)
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
	tmp = new CObject(x,y);
	tmp.draw();
	shapes[shapeCT]= tmp;
	shapeCT++;
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

function init()
{	
	//Create canvas to draw image to:
	canvas = document.getElementById('canvas');
	setsize();
	if (canvas.addEventListener)
	{	
		canvas.addEventListener("click", canvasonclick, false); 
		//canvas.addEventListener("mouseover", makeVisible, false);
		//canvas.addEventListener("mouseout", makeInvisible, false);
	}
	else
	{	
		canvas.attachEvent("onclick", canvasonclick );
		//canvas.attachEvent("onmouseover", makeVisible);
		//canvas.attachEvent("onmouseout", makeInvisible);
	}
	cview = canvas.getContext("2d");	
	var rect = new CObject(25,25);
	rect.draw();
	
}