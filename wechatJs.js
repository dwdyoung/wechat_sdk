
var readyFun = new Array();
var is_ready = false;
var jsApiList = new Array();


/**
 * 开始初始化
 */
function wx_beginInit(){
    readyFun = new Array();
    is_ready = false;
}

/**
 * 初始化结束时调用，正式申请接口
 * @param debug
 * @param appid
 * @param timestamp
 * @param noncestr
 * @param signature
 */
function wx_endInit(debug, appid, timestamp, noncestr, signature){
    wx.config({
        debug: debug, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: appid, // 必填，公众号的唯一标识
        timestamp: timestamp, // 必填，生成签名的时间戳
        nonceStr: noncestr, // 必填，生成签名的随机串
        signature: signature,// 必填，签名，见附录1
        jsApiList: jsApiList // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
    });
    wx.ready(function(){
        // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，
        // config是一个客户端的异步操作，所以如果需要在页面加载时就调用相关接口，则须把相
        // 关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，则可以直接调用，不需要放在ready函数中。
        var count = readyFun.length;
        for(var i = 0; i < count; i ++){
            var f = readyFun.pop();
            f();
        }

    });
}





/**
 * 申请接口
 * @param fun
 * @param jsApi
 */
function wx_ready(fun, jsApi) {
    readyFun.push(fun);
    jsApiList.push(jsApi);
}


/**
 * 关闭当前页面
 */
function wx_closeWindow(){
    wx.closeWindow();
}


/**
 * 获取地理位置
 * 获取成功后从fun返回
 * function (res) {
        var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
        var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
        var speed = res.speed; // 速度，以米/每秒计
        var accuracy = res.accuracy; // 位置精度
    }
 * @param fun
 */
function wx_initLocation(fun){
    wx_ready(function () {
        wx.getLocation({
            type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
            success: fun,
        });
    }, "getLocation");
}



/**
 * 预先设置好分享的内容
 * @param title
 * @param link
 * @param imgUrl
 * @param success
 * @param cancel
 */
function wx_initShare(title, link, imgUrl, desc, success, cancel){
    wx_ready(function () {
        wx.onMenuShareTimeline({
            title: title, // 分享标题
            link: link, // 分享链接
            imgUrl: imgUrl, // 分享图标
            success: success,
            cancel: cancel
        });
    }, "onMenuShareTimeline");

    wx_ready(function () {
        wx.onMenuShareAppMessage({
            title: title, // 分享标题
            desc: desc, // 分享描述
            link: link, // 分享链接
            imgUrl: imgUrl, // 分享图标
            type: 'link', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: success,
            cancel: cancel
        });
    }, "onMenuShareAppMessage");


    wx_ready(function () {
        wx.onMenuShareWeibo({
            title: title, // 分享标题
            desc: desc, // 分享描述
            link: link, // 分享链接
            imgUrl: imgUrl, // 分享图标
            type: 'link', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: success,
            cancel: cancel
        });
    }, "onMenuShareWeibo");

    wx_ready(function () {
        wx.onMenuShareQZone({
            title: title, // 分享标题
            desc: desc, // 分享描述
            link: link, // 分享链接
            imgUrl: imgUrl, // 分享图标
            type: 'link', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: success,
            cancel: cancel
        });
    }, "onMenuShareQZone");

}


function uniencode(text)
{
    text = escape(text.toString()).replace(/\+/g, "%2B");
    var matches = text.match(/(%([0-9A-F]{2}))/gi);
    if (matches)
    {
        for (var matchid = 0; matchid < matches.length; matchid++)
        {
            var code = matches[matchid].substring(1,3);
            var asc = parseInt(code, 16);
            if (asc < 48)
            {
                text = text.replace(matches[matchid], '%' + code);
            }
        }
    }
    text = text.replace('%25', '%u0025');

    return text;
}


function url2ScopeBase(url){
    wechatUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx520c15f417810387";
    wechatUrl = wechatUrl + "?redirect_uri=" + uniencode(url);
    wechatUrl = wechatUrl + "&response_type=code";
    wechatUrl = wechatUrl + "&scope=snsapi_base";
    wechatUrl = wechatUrl + "&state=123#wechat_redirect";
    return $wechatUrl;
}