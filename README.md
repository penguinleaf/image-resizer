image-resizer
=============

Download images from the web and resize them on the fly, then send them to the client

How it works
------------
Set up a virtual host (eg http://img.example.com/) and have it serve the contents of the web/ folder.

Download and resize images from a url by passing that url as a u GET parameter, or encoded as a ue parameter (recommended).

So setting the img src on a `<img>` tag to `http://img.example.com/?w=100&h=100&ue=http%3A%2F%2Fexample.com%2Fimg.png` will embed http://example.com/img.png, but at a max size of 100x100.

A practical use for this is to make images that fit in a `background-size:cover;`(CSS) over the entire screen, which can mean huge images on large desktops that would be far too large for a mobile connection. A soultion is to implement client side code like this to load images:

```javascript
function createImageURL(imgsrc, imghost){
	if (imgsrc != undefined){
		h = window.screen.availHeight;
		w = window.screen.availWidth;
		if (!String.prototype.startsWith) {
  		Object.defineProperty(String.prototype, 'startsWith', {
    		enumerable: false,
    		configurable: false,
    		writable: false,
    		value: function (searchString, position) {
      		position = position || 0;
      		return this.lastIndexOf(searchString, position) === position;
    		}
  		});
		}
		if (!imgsrc.startsWith("http")) // Dirty check for absolute imgsrc and add host if needed
			u = encodeURIComponent(window.location.protocol+"//"+window.location.host+imgsrc);
		else
			u = encodeURIComponent(imgsrc);
		if (window.location.hostname != "localhost" ){
			console.log(imghost+"/?w="+w/scale+"&h="+h+"&ue="+u);
			return imghost+"/?w="+w/scale+"&h="+h+"&ue="+u;
		}
		else{
			console.log("Not resizing img because localhost: "+imghost+"/?w="+w/scale+"&h="+h+"&ue="+u);
			return imgsrc;
		}
	}
}
```

`imgsrc` is a publicly accessible url for the full-sized image, and imghost is the host at which this resize script lives. The function will return a url that can be used in an image tag that will minimize the size of the image that can fill the screen resolution, which can often mean a 10x reduction in data sent over-the-wire.
