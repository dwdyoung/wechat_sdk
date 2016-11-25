<?php
/**
 * Created by PhpStorm.
 * User: MONDAY
 * Date: 2016/11/15
 * Time: 8:50
 */

namespace Home\Common;


class MyWechat
{

    /**
     * 创建菜单
     * @param $menu
     * @return bool|mixed
     */
    public static function createMenu($menu){
        $wechatUrl = 'https://api.weixin.qq.com/cgi-bin/menu/create';
        $wechatUrl = $wechatUrl."?access_token=".MyWechat::getAccessToken();


        $dataJson = json_encode($menu, JSON_UNESCAPED_UNICODE);

        $result = MyWechat::http_post_data($wechatUrl, $dataJson);

        if($result[0] == 200){
            $shortJson = $result[1];
            $shortObj = json_decode($shortJson);
            return $shortObj;
        } else {
            return false;
        }
    }


    /**
     * 获取客服聊天记录
     * @param $starttime    起始时间，unix时间戳
     * @param $endtime      结束时间，unix时间戳，每次查询时段不能超过24小时
     * @param $msgid        消息id顺序从小到大，从1开始
     * @param $number       每次获取条数，最多10000条
     * @return 例子
     * {
        "recordlist" : [
            {
                "openid" : "oDF3iY9WMaswOPWjCIp_f3Bnpljk",
                "opercode" : 2002,
                "text" : " 您好，客服test1为您服务。",
                "time" : 1400563710,
                "worker" : "test1@test"
            },
            {
                "openid" : "oDF3iY9WMaswOPWjCIp_f3Bnpljk",
                "opercode" : 2003,
                "text" : "你好，有什么事情？",
                "time" : 1400563731,
                "worker" : "test1@test"
            }
        ],
        "number":2,
        "msgid":20165267
    }
     */
    public static function getMsgList($starttime, $endtime, $msgid, $number){
        $wechatUrl = 'https://api.weixin.qq.com/customservice/msgrecord/getmsglist';
        $wechatUrl = $wechatUrl."?access_token=".MyWechat::getAccessToken();

        $data = array(
            "starttime"=> $starttime,
            "endtime"=> $endtime,
            "msgid"=> $msgid,
            "number"=> $number,
        );

        $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE);

        $result = MyWechat::http_post_data($wechatUrl, $dataJson);

        if($result[0] == 200){
            $shortJson = $result[1];
            $shortObj = json_decode($shortJson);
            return $shortObj;
        } else {
            return false;
        }
    }


    /**
     * 获取用户基本信息
     * @param $openid
     * @return 例子
     *  {
            "subscribe": 1,             // 用户是否订阅该公众号标识，值为0时，代表此用户没有关注该公众号，拉取不到其余信息。
            "openid": "o6_bmjrPTlm6_2sgVt7hMZOPfL2M",       // 用户的标识，对当前公众号唯一
            "nickname": "Band",             // 用户的昵称
            "sex": 1,                       // 用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
            "language": "zh_CN",            // 用户的语言，简体中文为zh_CN
            "city": "广州",                   // 用户所在城市
            "province": "广东",               // 用户所在省份
            "country": "中国",                 // 用户所在国家
            "headimgurl":  "http://wx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0",
            // 用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。


            "subscribe_time": 1382694957,       // 用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间
            "unionid": " o6_bmasdasdsad6_2sgVt7hMZOPfL"     // 只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
            "remark": "",                       // 公众号运营者对粉丝的备注，公众号运营者可在微信公众平台用户管理界面对粉丝添加备注
            "groupid": 0,                       // 用户所在的分组ID（兼容旧的用户分组接口）
            "tagid_list":[128,2]                // 用户被打上的标签ID列表
        }
     */
    public static function getUserInfo($openid){
        $wechatUrl = 'https://api.weixin.qq.com/cgi-bin/user/info';
        $wechatUrl = $wechatUrl."?access_token=".MyWechat::getAccessToken();
        $wechatUrl = $wechatUrl."&openid=".$openid;
        $wechatUrl = $wechatUrl."&lang=zh_CN";
        $infoJson = file_get_contents($wechatUrl);
        return json_decode($infoJson);
    }


    /**
     * 批量获取已关注的用户的openid
     * @return
     *
     * {
        "total":23000,
        "count":10000,
        "data":{"
            openid":[
                "OPENID1",
                "OPENID2",
                ...,
                "OPENID10000"
            ]
        },
        "next_openid":"OPENID10000"
        }
     */
    public static function getOpenIdList($next_openid){
        $wechatUrl = 'https://api.weixin.qq.com/cgi-bin/user/get';
        $wechatUrl = $wechatUrl."?access_token=".MyWechat::getAccessToken();
        $wechatUrl = $wechatUrl."&next_openid=".$next_openid;
        $openidJson = file_get_contents($wechatUrl);
        return json_decode($openidJson);
    }




    /**
     * 批量获取用户基本信息
     * @param $user_list
     * "user_list": [
        {
            "openid": "otvxTs4dckWG7imySrJd6jSi0CWE",
            "lang": "zh-CN"
        },
        {
            "openid": "otvxTs_JZ6SEiP0imdhpi50fuSZg",
            "lang": "zh-CN"
        }
        ]
     *
     * @return
     * {
        "user_info_list": [
            {
                "subscribe": 1,
                "openid": "otvxTs4dckWG7imySrJd6jSi0CWE",
                "nickname": "iWithery",
                "sex": 1,
                "language": "zh_CN",
                "city": "Jieyang",
                "province": "Guangdong",
                "country": "China",
                "headimgurl": "http://wx.qlogo.cn/mmopen/xbIQx1GRqdvyqkMMhEaGOX802l1CyqMJNgUzKP8MeAeHFicRDSnZH7FY4XB7p8XHXIf6uJA2SCun
                TPicGKezDC4saKISzRj3nz/0",
                "subscribe_time": 1434093047,
                "unionid": "oR5GjjgEhCMJFyzaVZdrxZ2zRRF4",
                "remark": "",
                "groupid": 0,
                "tagid_list":[128,2]
            },
            {
                "subscribe": 0,
                "openid": "otvxTs_JZ6SEiP0imdhpi50fuSZg",
                "unionid": "oR5GjjjrbqBZbrnPwwmSxFukE41U",
            }
        ]
    }
     */
    public static function batchgetUserInfo($user_list){
        $wechatUrl = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget';
        $wechatUrl = $wechatUrl."?access_token=".MyWechat::getAccessToken();

        $data = array(
            'user_list' => $user_list
        );

        $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE);

        $result = MyWechat::http_post_data($wechatUrl, $dataJson);

        if($result[0] == 200){
            $shortJson = $result[1];
            $shortObj = json_decode($shortJson);
            return $shortObj;
        } else {
            return false;
        }
    }


    /**
     * 创建分组
     * @param $name  string
     */
    public static function createGroup($name){
        $wechatUrl = 'https://api.weixin.qq.com/cgi-bin/groups/create';
        $wechatUrl = $wechatUrl."?access_token=".MyWechat::getAccessToken();

        $data = array(
            "group" =>array(
                "name" => $name
            )
        );

        $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE);

        $result = MyWechat::http_post_data($wechatUrl, $dataJson);

        if($result[0] == 200){
            $shortJson = $result[1];
            $shortObj = json_decode($shortJson);
            return $shortObj;
        } else {
            return false;
        }
    }


    /**
     * 查询所有分组
     */
    public static function getGroup(){
        $wechatUrl = 'https://api.weixin.qq.com/cgi-bin/groups/get';
        $wechatUrl = $wechatUrl."?access_token=".MyWechat::getAccessToken();


        $result = MyWechat::http_post_data($wechatUrl, null);

        if($result[0] == 200){
            $shortJson = $result[1];
            $shortObj = json_decode($shortJson);
            return $shortObj;
        } else {
            return false;
        }
    }




    /**
     * 查询用户所在分组
     * @param $name  string
     */
    public static function getGroupIdByOpenId($openid){
        $wechatUrl = 'https://api.weixin.qq.com/cgi-bin/groups/getid';
        $wechatUrl = $wechatUrl."?access_token=".MyWechat::getAccessToken();

        $data = array(
            "openid" => $openid
        );

        $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE);

        $result = MyWechat::http_post_data($wechatUrl, $dataJson);

        if($result[0] == 200){
            $shortJson = $result[1];
            $shortObj = json_decode($shortJson);
            return $shortObj;
        } else {
            return false;
        }
    }


    /**
     * 修改分组名
     * @param $groupId
     * @param $name
     * @return bool|mixed
     */
    public static function updateGroup($groupId, $name){
        $wechatUrl = 'https://api.weixin.qq.com/cgi-bin/groups/update';
        $wechatUrl = $wechatUrl."?access_token=".MyWechat::getAccessToken();

        $data = array(
            "group" => array(
                "id" => $groupId,
                "name" => $name
            )
        );

        $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE);

        $result = MyWechat::http_post_data($wechatUrl, $dataJson);

        if($result[0] == 200){
            $shortJson = $result[1];
            $shortObj = json_decode($shortJson);
            return $shortObj;
        } else {
            return false;
        }
    }


    /**
     * 移动用户到分组
     * @param $openid
     * @param $to_groupid
     * @return bool|mixed
     */
    public static function updateMember($openid, $to_groupid){
        $wechatUrl = 'https://api.weixin.qq.com/cgi-bin/members/update';
        $wechatUrl = $wechatUrl."?access_token=".MyWechat::getAccessToken();

        $data = array(
            "openid" => $openid,
            "to_groupid" => $to_groupid
        );

        $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE);

        $result = MyWechat::http_post_data($wechatUrl, $dataJson);

        if($result[0] == 200){
            $shortJson = $result[1];
            $shortObj = json_decode($shortJson);
            return $shortObj;
        } else {
            return false;
        }
    }


    /**
     * 批量移动用户到分组
     * @param $openid_list
     * @param $to_groupid array
     * @return bool|mixed
     */
    public static function batchUpdateMember($openid_list, $to_groupid){
        $wechatUrl = 'https://api.weixin.qq.com/cgi-bin/members/batchupdate';
        $wechatUrl = $wechatUrl."?access_token=".MyWechat::getAccessToken();

        $data = array(
            "openid_list" => $openid_list,
            "to_groupid" => $to_groupid
        );

        $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE);

        $result = MyWechat::http_post_data($wechatUrl, $dataJson);

        if($result[0] == 200){
            $shortJson = $result[1];
            $shortObj = json_decode($shortJson);
            return $shortObj;
        } else {
            return false;
        }
    }


    /**
     * 删除分组
     * @param $openid_list
     * @param $to_groupid array
     * @return bool|mixed
     */
    public static function deleteGroup($groupid){
        $wechatUrl = 'https://api.weixin.qq.com/cgi-bin/groups/delete';
        $wechatUrl = $wechatUrl."?access_token=".MyWechat::getAccessToken();

        $data = array(
            "group" => array(
                "id" => $groupid
            ),
        );

        $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE);

        $result = MyWechat::http_post_data($wechatUrl, $dataJson);

        if($result[0] == 200){
            $shortJson = $result[1];
            $shortObj = json_decode($shortJson);
            return $shortObj;
        } else {
            return false;
        }
    }


    // 将网页转成微信的跳转链接
    // 如果用户同意授权，页面将跳转至 redirect_uri/?code=CODE&state=STATE。
    public static function url2SnsapiBase($url){
        $wechatUrl = "https://open.weixin.qq.com/connect/oauth2/authorize";
        $wechatUrl = $wechatUrl."?appid=".C("WECHAT_APPID");
        $wechatUrl = $wechatUrl."&redirect_uri=".MyWechat::unicode_encode($url);
        $wechatUrl = $wechatUrl."&response_type=code";
        $wechatUrl = $wechatUrl."&scope=snsapi_base";
        $wechatUrl = $wechatUrl."&state=123#wechat_redirect";
        return $wechatUrl;
    }



    // 将网页转成微信的跳转链接
    // 如果用户同意授权，页面将跳转至 redirect_uri/?code=CODE&state=STATE。
    public static function url2SnsapiUserInfo($url){
        $wechatUrl = "https://open.weixin.qq.com/connect/oauth2/authorize";
        $wechatUrl = $wechatUrl."?appid=".C("WECHAT_APPID");
        $wechatUrl = $wechatUrl."&redirect_uri=".MyWechat::unicode_encode($url);
        $wechatUrl = $wechatUrl."&response_type=code";
        $wechatUrl = $wechatUrl."&scope=snsapi_userinfo";
        $wechatUrl = $wechatUrl."&state=123#wechat_redirect";
        return $wechatUrl;
    }




    // 调用微信提供的借口，将长连接转为短连接
    public static function longUrl2short($longUrl){
        $wechatUrl = 'https://api.weixin.qq.com/cgi-bin/shorturl';
        $wechatUrl = $wechatUrl."?access_token=".MyWechat::getAccessToken();

        $data = array(
            'action' => 'long2short',
//            'access_token' => MyWechat::getAccessToken(),
            'long_url' => $longUrl,
        );
        $dataJson = json_encode($data);

        $result = MyWechat::http_post_data($wechatUrl, $dataJson);
        if($result[0] == 200){
            $shortJson = $result[1];
            $shortObj = json_decode($shortJson);
            return $shortObj->short_url;;
        } else {
            return false;
        }
    }


    /**
     * @param $action_info      二维码详细信息
     * @param $scene_id         场景值ID，临时二维码时为32位非0整型，永久二维码时最大值为100000（目前参数只支持1--100000）
     * @param $scene_str        场景值ID（字符串形式的ID），字符串类型，长度限制为1到64，仅永久二维码支持此字段
     * @return bool
     */
    public static function createQrcodeTicket($action_info, $scene_id, $scene_str){
        $wechatUrl = 'https://api.weixin.qq.com/cgi-bin/qrcode/create';
        $wechatUrl = $wechatUrl."?access_token=".MyWechat::getAccessToken();

        $data = array(
            'expire_seconds' => 604800,
            'action_name' => 'QR_SCENE',          // 二维码类型，QR_SCENE为临时,QR_LIMIT_SCENE为永久,QR_LIMIT_STR_SCENE为永久的字符串参数值
            'action_info' => $action_info,          // 二维码详细信息
            'scene_id' => $scene_id,          // 场景值ID，临时二维码时为32位非0整型，永久二维码时最大值为100000（目前参数只支持1--100000）
            'scene_str' => $scene_str,          // 场景值ID（字符串形式的ID），字符串类型，长度限制为1到64，仅永久二维码支持此字段
        );
        $dataJson = json_encode($data);

        $result = MyWechat::http_post_data($wechatUrl, $dataJson);

        if($result[0] == 200){
            $shortJson = $result[1];
            $shortObj = json_decode($shortJson);
            return $shortObj;
        } else {
            return false;
        }
    }



    /**
     * 发送post数据
     * @param $url
     * @param $data_string
     * @return array
     */
    private static function http_post_data($url, $data_string) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
        if($data_string){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . $data_string == null ? 0 : strlen($data_string))
        );
//        $result = curl_setopt($ch, CURLOPT_HEADER, 1);
        ob_start();
        curl_exec($ch);
        $return_content = ob_get_contents();
        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        ob_end_clean();
        curl_close($ch);

        return array($return_code, $return_content);
    }


    /**
     * 将code转换成openid并写入session中
     * @param $code
     */
    public static function getOpenId($code){
        $tokenObj = MyWechat::code2AccessToken($code);
        if($tokenObj->openid){
            session("openid", $tokenObj->openid);
        }
        return $tokenObj->openid;
    }



    // 获取微信Access Token
    // 此方法需要访问微信后台，属于长方法
    /**
     * 正确返回{"access_token":"ACCESS_TOKEN","expires_in":7200}
     * 错误返回{"errcode":40013,"errmsg":"invalid appid"}
     */
    public static function getAccessToken(){

        $accessToken = S("access_token");
        // 预留5分钟时间
        if($accessToken && S("access_time") > time() + 300){
            return S("access_token");
        }

        $wechatUrl = "https://api.weixin.qq.com/cgi-bin/token";
        $wechatUrl = $wechatUrl."?grant_type=client_credential";
        $wechatUrl = $wechatUrl."&appid=".C("WECHAT_APPID");
        $wechatUrl = $wechatUrl."&secret=".C("WECHAT_APPSECRET");
        $tokenJson = file_get_contents($wechatUrl);
        $tokenObj = json_decode($tokenJson);
        if($tokenObj->access_token){
            S("access_token", $tokenObj->access_token);
            S("access_time", $tokenObj->expires_in + time());
        } else {
            // TODO 获取access token失败

        }

        return $tokenObj->access_token;
    }


    /**
     * 获取jsapi_ticket
     * jsapi_ticket是公众号用于调用微信JS接口的临时票据
     * @return mixed
     */
    public static function getJsApiTicket(){

        $jsapi_ticket = S("jsapi_ticket");


        // 预留5分钟时间
        if($jsapi_ticket && S("jsapi_ticket_time") > time() + 300){
            return $jsapi_ticket;
        }

        $wechatUrl = "https://api.weixin.qq.com/cgi-bin/ticket/getticket";
        $wechatUrl = $wechatUrl."?type=jsapi";
        $wechatUrl = $wechatUrl."&access_token=".MyWechat::getAccessToken();
        $apiJson = file_get_contents($wechatUrl);
        $apiObj = json_decode($apiJson);
        if($apiObj->ticket){
            S("jsapi_ticket", $apiObj->ticket);
            S("jsapi_ticket_time", $apiObj->expires_in + time());
        } else {
            // TODO 获取access token失败

        }

        return $apiObj->ticket;
    }


    /**
     * 对url以及jsapi进行签名，签名后才能在此网页中使用微信的js接口
     * @param $noncestr             // 随机字符串
     * @param $jsapi_ticket            // 有效的jsapi_ticket
     * @param $timestamp         // 时间戳
     * @param $url                  // url（当前网页的URL，不包含#及其后面部分）
     * @return $sign                // 签名后的字符串
     */
    public static function signJsApiTicket(&$timestamp){
        $timestamp = time();
        $url = 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        $noncestr = C("NONCESTR");
        $jsapi_ticket = MyWechat::getJsApiTicket();
        $string = "jsapi_ticket=".$jsapi_ticket
            ."&noncestr=".$noncestr
            ."&timestamp=".$timestamp
            ."&url=".$url;
        $sign = sha1($string);

        return $sign;
    }



    /**
     * TODO 发送模板消息
     * @param $openid 需要发送到的openid
     * @param $url 消息点击后的跳转地址
     * @param $data 事例
     * "data":{
            "first": {
                "value":"恭喜你购买成功！",
                "color":"#173177"
            },
            "keynote1":{
                "value":"巧克力",
                "color":"#173177"
            },
            "keynote2": {
                "value":"39.8元",
                "color":"#173177"
            },
            "keynote3": {
                "value":"2014年9月22日",
                "color":"#173177"
            },
            "remark":{
                "value":"欢迎再次购买！",
                "color":"#173177"
            }
        }
     * @return bool|mixed
     */
    public static function sendTemplate($templateId, $openid, $url, $data){

        // 发送消息
        $wechatUrl = 'https://api.weixin.qq.com/cgi-bin/message/template/send';
        $wechatUrl = $wechatUrl."?access_token=".MyWechat::getAccessToken();

        $data2 = array(
            'touser' => $openid,
            'template_id' => $templateId,
            'url' => $url,
            'data' => $data,
        );
        $dataJson = json_encode($data2);
        return MyWechat::http_post_data($wechatUrl, $dataJson);
    }




    // 通过code换取网页授权access_token
    // 此方法会访问微信服务器，是一个长方法
    /**
     * 正确时返回{ "access_token":"ACCESS_TOKEN",
     *              "expires_in":7200,
     *              "refresh_token":"REFRESH_TOKEN",
     *              "openid":"OPENID",
     *              "scope":"SCOPE" }
     *
     * 错误时返回{"errcode":40029,"errmsg":"invalid code"}
     *
     * @param $code
     * @return mixed
     */
    public static function code2AccessToken($code){
        $wechatUrl = "https://api.weixin.qq.com/sns/oauth2/access_token";
        $wechatUrl = $wechatUrl."?appid=".C('WECHAT_APPID');
        $wechatUrl = $wechatUrl."&secret=".C('WECHAT_APPSECRET');
        $wechatUrl = $wechatUrl."&code=".$code;
        $wechatUrl = $wechatUrl."&grant_type=authorization_code";
        $tokenJson = file_get_contents($wechatUrl);
        $tokenObj = json_decode($tokenJson);
        return $tokenObj;
    }


    // 将原始网页编码成ascii码
    private static function unicode_encode($name)
    {
        $name = iconv('UTF-8', 'UCS-2', $name);
        $len = strlen($name);
        $str = '';
        for ($i = 0; $i < $len; $i = $i + 1)
        {
            $c = $name[$i];
            if ((ord($c) >= 20 && ord($c) <= 47) || (ord($c) >= 58 && ord($c) <= 64) ||  ord($c) >= 123)
            {   //两个字节的文字
                $str .= '%'.base_convert(ord($c), 10, 16);
            }
            else
            {
                $str .= $c;
            }
        }
        return $str;
    }


    /**
     * 微信验证url
     * @return bool
     */
    public static function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = "weixin";
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}