
gallery.player = {
    $div: $('<div id="player_gallery"/>'),
    attr:{
        id: "player_gallery"
    },
    src: '',
    home: window.location.protocol + '//' + window.location.host + '/',
    newSrc:  'engine/classes/flashplayer/media_player_v3.swf',
    oldSrc:  'engine/classes/flashplayer/media_player.swf',
    isYouTybe: false,
    width: 640,
    height: 360,
    init: function(data){

        if(data.status === 'youtube'){
            gallery.player.isYouTybe = true;

            gallery.player.src = 'http://www.youtube.com/v/' + data.other_dat.other_dat.name;
        }else{
            if(! data.other_dat.default_player ||  data.other_dat.default_player == 0){
                data.other_dat.default_player = '2';
            }
            gallery.player.src =   window.location.protocol + '//' + window.location.host + data.other_dat.other_dat.file_path;
        }
        $('#view-image').html(this.$div);
        switch (data.other_dat.default_player) {
            case '0':
            case 0:
                this.youTube();
                return;
            case '1':
            case 1:
                this.dleOld(data);
                break;
            case '2':
            case 2:
                this.dleNew(data);
                break;
            default:
                data.other_dat.default_player = '0';
                this.youTube();
                break;
        }
    },
    serviceInit: function(data){
        var player = '';
        console.dir(data);
        switch (data.status) {
            case 'smotri.com':
                player = '<object id="smotriCom" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="640" height="360">\
        <param name="movie" value="'+data.other_dat.path +'&bufferTime=3&autoStart=true&str_lang=rus&xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml" />\
        <param name="allowScriptAccess" value="always" />\
        <param name="allowFullScreen" value="true" />\
        <param name="wmode" value="opaque" />\
        <embed src="'+data.other_dat.path +'&bufferTime=3&autoStart=false&str_lang=rus&xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml" quality="high" allowscriptaccess="always" allowfullscreen="true" wmode="opaque"  width="640" height="360" type="application/x-shockwave-flash"></embed>\
</object>'
                break;
            case 'vimeo':
                player = '<iframe id="vimeoCom" width="640" height="360" src="' +data.other_dat.other_dat.path + '" frameborder="0" allowfullscreen></iframe>'
                break;
            case 'rutube':
                player = '<object width="640" id="rutube" height="360">\
                            <param name="movie" value="' +data.other_dat.path + '" />\
                            <param name="wmode" value="transparent" />\
                            <param name="allowFullScreen" value="true" />\
                            <embed src="' +data.other_dat.path + '" type="application/x-shockwave-flash" wmode="transparent" width="640" height="360" allowFullScreen="true" ></embed>\
                        </object>';
                break;
            case 'gametrailers':
                player = '<object   type="application/x-shockwave-flash" id="mtvn_player" name="mtvn_player" data="' +data.other_dat.path + '" width="640" height="360">\
                <param name="allowscriptaccess" value="always" />\
                <param name="allowFullScreen" value="true" />\
                <param name="wmode" value="opaque" />\
                <param name="flashvars" value="autoPlay=false" />\
                <embed src="' +data.other_dat.path + '" width="640" height="360" type="application/x-shockwave-flash" allowFullScreen="true" allowScriptAccess="always" base="." flashVars=""> </embed>\
            </object>';
                break;
            default:
                break;
        }



        this.$div.html(player)
        $("#view-image").html(this.$div);
        $('#player_gallery').css('visibility','visible')
    },
    youTube: function(){
        gallery.core.log(gallery.player.src );
        if(gallery.player.isYouTybe){
            var params={
                allowScriptAccess:"always",
                allowFullScreen: "true",
                wmode:'opaque'
            };
            swfobject.embedSWF(gallery.player.src +"?enablejsapi=1&playerapiid=ytplayer&showsearch=1",
                "player_gallery",this.width,this.height, '9', null,null, params, gallery.player.attr);
        }
    },
    _youTube: function(){
        var player = '<object style="height: 360px; width: 640px">\
        <param name="movie" value="http://www.youtube.com/v/'+gallery.player.src +'?version=3&feature=player_embedded">\
        <param name="allowFullScreen" value="true">\
        <param name="allowScriptAccess" value="always">\
        <embed src="http://www.youtube.com/v/'+gallery.player.src+'?version=3&feature=player_embedded" type="application/x-shockwave-flash" allowfullscreen="true" allowScriptAccess="always" width="640" height="385"></object>'
        this.$div.html(player)
        $("#view-image").html(this.$div)
    },
    dleOld: function(data){
        var so=new SWFObject(this.home + this.oldSrc,"player_gallery", this.width, this.height,'8',"#000000");
        so.addParam("allowFullScreen", true);
        so.addParam("wmode",'opaque' );
        so.addVariable("MediaLink", gallery.player.src);
        if(gallery.player.isYouTybe){
            so.addVariable("image", gallery.core.setPrevPath(data.path));
        }else{
            so.addVariable("image", gallery.core.setThumbPath(data.path));
        }
        so.addVariable("logo", this.home + 'uploads/gallery/assets/flv_watermark.png');
        so.addVariable("imageScaleType","0");
        so.addVariable("playOnStart","false");
        so.write('player_gallery');
        $('embed#player_gallery').css({
            visibility:'visible',
            display:'block'
        })
    },
    dleNew: function(data){
        // gallery.player.isYouTybe;
        // console.log('int player')
        var flashvars = {
            stageW: 640,
            stageH: 360,
            contentType: 'video',
            videoUrl: gallery.player.src,
            youTubePlaybackQuality: 'medium',
            isYouTube: gallery.player.isYouTybe,
            rollOverAlpha: 0.5,
            contentBgAlpha: 0.8,
            progressBarColor: '0xFFFFFF',
            defaultVolume: 1,
            fullSizeView: 2,
            showRewind: false,
            showInfo: false,
            showFullscreen: true,
            showScale: true,
            showSound: true,
            showTime: true,
            showCenterPlay: true,
            videoLoop: false,
            showWatermark: true,//
            watermarkMargin: 0,//
            watermarkAlpha: 1,//
            watermarkPosition: 'left',
            watermarkImageUrl: this.home + 'uploads/gallery/assets/flv_watermark.png',
            showPreviewImage: true,
            previewImageUrl: (gallery.player.isYouTybe) ? gallery.core.setPrevPath(data.path) : gallery.core.setThumbPath(data.path)
        };

        var params = {
            scale: "noscale",
            allowFullScreen: "true",
            wmode:'opaque',
            menu: "false",
            bgcolor: "#000000"
        };
        var attributes = {
            id: "player_gallery",
            name: "flvplayer_swf"
        };
        swfobject.embedSWF( this.home + this.newSrc, "player_gallery", this.width, this.height, '9', "swfobject/expressInstall.swf",
            flashvars, params, attributes);

    }

}