//Album
	function ImageWrap() {
		this.image = new Image();
		this.imageWidth = 0;
		this.imageHeight = 0;
	}
	
	function Album() {
		this.images = new Array();
		this.imageLoaders = new Array();
	}

	Album.prototype.addImage = function (image) {
		image.add(image);
	}

	Album.prototype.getImage = function (index) {
		return this.images[index];
	}

	Album.prototype.addImages = function (imageArr) {
		for (int i=0; i<imageArr.size(); i++)
		{
			image.add(imageArr[i]);
		}
	}

//ImageViewer
	function ImageViewer(canvasObj) {
		//init
		this.canvas = canvasObj;
		this.curimg = -1;
		this.startX = 0;
		this.startY = 0;
		this.degrees = 0;
		this.cWidth = 630;
		this.cHeight = 460;
		this.resizeTimeoutId = null;  //it's here...
		this.timeoutId = null;
		this.maxWidth = 0;
		this.maxHeight = 0;
		this.cscale = 1.0;
		this.fullsize = false;
		this.loadct = 0;
		//=============================
		//Create canvas to draw image to:
		//canvas = document.getElementById('canvas');
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
	};

