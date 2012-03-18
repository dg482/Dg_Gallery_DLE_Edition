gallery.eff = {
    pack: {
        run: function(obj,eff){
            $(obj).hide(eff,{},this.delay);
        },
        runShow:function(obj,eff,speed){
            $(obj).show(eff,{},speed);
        }
    },
    currentImage: null,
    dataList: new Array(),
    timeOut: 5000,
    delay: 1000,
    timer: null,
    param: null,
    callback: function(obj){},
    start: function(eff, parentCon){
        switch (eff) {
            case 'none':
                $(parentCon).html(this.currentImage);
                $(this.currentImage).css({
                    display: 'block',
                    opacity: '0.00'
                }).animate({
                    opacity: '1'
                },500, function(){
                    gallery.eff.callback(this);
                });
                return;
            default:
                break;
        }

    },
   aHover: function(obj){
 
    },
    fade: function(obj,speed){
        var i = speed,j=0;
        $(obj).each(function(){
            j++;
            var objeff = this;
            setTimeout(function(){
                gallery.eff.pack.run(objeff,'fade');
            },i*j);
        });
    },
    fadeIn: function(obj){
        gallery.eff.pack.runShow(obj,'fade');
    },
    fadeElement: function(obj,speed ){
        setTimeout(function(){
            gallery.eff.pack.run(obj,'fade');
        },speed);
    },
    blindElementsShow: function(obj,speed){
        var i = speed,j=0;
        $(obj).each(function(){
            j++;
            var objeff = this;
            setTimeout(function(){
                $(objeff).show('blind',{},speed)
            },i*j);
        })
    },
    blindElementsHide: function(obj,speed){
        var i = speed,j=0;
        $(obj).each(function(){
            j++;
            var objeff = this;
            setTimeout(function(){
                $(objeff).hide('blind',function(){
                    $(this).remove()
                },speed)
            },i*j);
        })
    },
    setCurrentObject: function(obj){
        this.currentImage = obj;
    },
    getEffect: function(state, width, height){
        var eff = {};

        switch (state) {
            case 'horizontalLine':
                eff.start = {
                    width: width,
                    height: 0
                }, eff.end = {
                    width: width,
                    height: height,
                    opacity: '1.00'
                }
                break;
            case 'verticalLine':
                eff.start = {
                    width: 0,
                    height: height
                }, eff.end = {
                    width: width,
                    height: height,
                    opacity: '1.00'
                }
                break;
            case 'opcity':
                eff.start = {
                    width: width,
                    height: height,
                    opacity: '0.00'
                }, eff.end = {
                    width: width,
                    height: height,
                    opacity: '1.00'
                }
                break;
            default:
                return null;
        }
        return eff;

    }



}
