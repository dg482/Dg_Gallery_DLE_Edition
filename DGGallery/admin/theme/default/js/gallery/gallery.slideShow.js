(function($) {
    function GallerySlideShow(obj,options){
        var
        img = $(obj).children('img').eq(0),
        self = this;
        this.currentState = 'stop';
        this.currentIndex = 0;
        this._offset = [0,0];
        this._param = [0.0];
        this._lenght = 0;
        this._effect = ['fade','horizontalLine','verticalLine'];
        this._interval = null;
        this._images = [];
        this._initSlice = [];
        this._startId = 1;
        this._initImg = [];
        this._http = window.location.protocol + '//' + window.location.host;
        this._wrapper = null;
        this._isAnomated = false;
        this.settings = $.extend({}, $.fn.gallerySlideShow.defaults, options);
        this.start = function(){
            $(obj).children('img').each(function(){
                if($(this).index() > 0)
                    $(this).css('display','none');
            })
            this._lenght = this.getDataLenght(options.data);
            this.data = options.data;
            this.currentState = options.currentState;
            this.delay = settings.delay;
            this._startId = this.settings.dataId;
            this.wrap(img);

        }
        this.wrap = function(image){
            this.path = image.attr('src');
            this._wrapper =  $(image.wrap('<div />'));
            this._wrapper.parent().css({
                position: 'relative'
            });
            if(this.currentState == 'show'){
                this._offset = image.offset();
                self.getData(this._startId);


                var startIndex = this.currentIndex;

                this._startInterval(startIndex);
            }
            this._setControl();
        }
        // set Interval
        this._startInterval = function(startIndex){
            if(this.currentState == 'stop')
                return;
            if(self._isAnimated === true){
                setTimeout(function(){
                    self._setInterval();
                },100);
                return;
            }
            this._interval = setInterval(function(){
                this._next();
                self.load(this.data[this.currentIndex]);
            },this.delay)
        }
        //load
        this.load = function(data){
            clearInterval(this._interval);
            this.path = this._getPath(data.path);
            if(data.status == 'albom'){
                if(typeof this._initImg[data.id] == 'object'){
                    this._param[0] = this._initImg[data.id].width;
                    this._param[1] = this._initImg[data.id].height;
                    this.fillSlice(this._wrapper);
                    this._startInterval();
                }else{
                    this._initImg[data.id] = new Image;
                    this._initImg[data.id].src = this.path;
                    this._preload(data.id);
                }
            }
        }
        this._preload = function(id){
            if(this._initImg[id].complete){
                this._param[0] = this._initImg[id].width;
                this._param[1] = this._initImg[id].height;
                this.fillSlice(this._wrapper);
                this._startInterval();
            }else{
                setTimeout(function(){
                    self._preload(id);
                },100);
            }
        }
        this._next = function(){
            this.currentIndex  = ( parseInt(this.currentIndex ) + 1 );
            if(this.currentIndex > this._lenght){
                this.currentIndex = 1;
            }
        }

        this._prev = function(){
            this.currentIndex  = ( parseInt(this.currentIndex ) - 1 );
            if(this.currentIndex  < 1){
                this.currentIndex = self._lenght;
            }
        }
        this._getPath = function(str){
            return this._http + str.replace('%replace%/', '');
        }
        this._getThumb = function(str){
            return  this._http + str.replace('%replace%', 'thumbs');
        }
        this.fillSlice = function(wrapper){
            self._initSlice = [];
            var c = 'horizontalLine';
            $('.slice').remove();

            $('#file-box').animate({
                height: (this._param[1])
            },150)
            setTimeout(function(){
                var x = Math.round(( self._param[0] / self.settings.slice)),
                y = Math.round(( self._param[1] / self.settings.slice)),
                width =  self._param[0],
                height =  self._param[1],
                sX = Math.floor((width / x)),
                sY = Math.floor(height / y),
                horizontal = 0, vertical = 0,
                sliceWidth = 0, sliceHeight = 0, offsetBgLeft = 0,offsetBgTop = 0,
                startOffsetLeft = 0,
                startOffsetTop = 0 ;
                if(c == 'horizontalLine'){
                    horizontal = 1;
                    vertical =  sX;
                    sliceWidth = width;
                    sliceHeight = Math.floor(height / sY);
                }
                if(c == 'verticalLine'){
                    horizontal = sX;
                    vertical =  1;
                    sliceWidth =  Math.floor(width / sX);
                    sliceHeight = height;
                }
                wrapper.parent().children('img').css('display','none');
                var effect = self.settings.efx.getEffect(c,sliceWidth,sliceHeight);
                for(var i=0; i < horizontal; i++){
                    for(var j= 0; j < vertical; j++){
                        var slice = $('<div class="slice"></div>').css({
                            'background-image': 'url(' + self.path + ')',
                            'background-position': - (i * sliceWidth + offsetBgLeft) + 'px' + ' ' + - (j * sliceHeight + offsetBgTop) + 'px',
                            'background-repeat': 'no-repeat',
                            'left': i * sliceWidth + startOffsetLeft,
                            'top': j * sliceHeight + startOffsetTop,
                            'width': effect.start.width,
                            'height': effect.start.height,
                            'opacity': '0.00'
                        })
                        .appendTo(wrapper.parent());
                        self._initSlice.push(slice);
                    }
                }
                $(self._initSlice).each(function(){//start effect
                    $(this).animate(effect.end,self.settings.animateSpeed);
                })
            },155);

        }
        this.getData = function(i){
            var d = null;
            for (var key in self.data){
                if(self.data[key].id == i){
                    this.currentIndex = key;
                    d = self.data[key];
                }
            }
            return d;
        }
        this._setControl = function(){
            $('#PlayPayseSlideShow').addClass('pause').click(function(){
                if( self.currentState == 'show'){
                    self.currentState = 'stop';
                    $(this).removeClass('pause');
                    if(self._interval)
                        clearInterval(self._interval);
                }else{
                    self.currentState = 'show';
                    $(this).addClass('pause');
                    if(null == self._interval);
                    self._startInterval();
                }
            });
            $('#PrevSlideShow').click(function(){
                self.currentState = 'stop';
                $('#PlayPayseSlideShow').removeClass('pause')
                if(self._interval)
                    clearInterval(self._interval);
                self._prev();
                self.load(self.data[self.currentIndex]);
            })
            $('#NextSlideShow').click(function(){
                self.currentState = 'stop';
                $('#PlayPayseSlideShow').removeClass('pause')
                if(self._interval)
                    clearInterval(self._interval);
                self._next();
                self.load(self.data[self.currentIndex]);
            })
        }
        //start
        this.start();
    }
    this.getDataLenght = function(obj){
        var c = 0;
        for(var k in obj){
            if(obj.hasOwnProperty(k))
                c++;
        }
        return c;
    }
    $.fn.gallerySlideShow = function(options) {
        GallerySlideShow(this,options);

    }
    $.fn.gallerySlideShow.defaults =  {
        data: null,
        currentState: 'paused',
        delay: 5000,
        animateSpeed: 500,
        slice: 10,
        dataId: 0,
        efx: gallery.eff
    }
})(jQuery)