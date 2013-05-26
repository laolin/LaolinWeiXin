<?php
/**
  * wechat php test
  */

//define your token
if( !defined('TOKEN') )die('pls define TOKEN first.');
//  define("TOKEN", "defWeixinToken");//should be defined in 'wx-token.php'

$wechatObj = new wechatCallbackapiTest();

if(isset( $_GET["echostr"])) {
  $wechatObj->valid();
} else {
  $wechatObj->responseMsg();
}
class wechatCallbackapiTest
{
  public function valid() {
    $echoStr = $_GET["echostr"];

    //valid signature , option
    if($this->checkSignature()){
      echo $echoStr;
      exit;
    }
  }

  public function responseMsg() {
    //get post data, May be due to the different environments
    @$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

    //extract post data
    if (!empty($postStr)){
              
      $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
      $fromUsername = $postObj->FromUserName;
      $toUsername = $postObj->ToUserName;
      $keyword = trim($postObj->Content);
      $inType= trim($postObj->MsgType);
      $time = time();
      $textTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            <FuncFlag>0</FuncFlag>
            </xml>"; 
            
      /* see: http://mp.weixin.qq.com/wiki/index.php
      参数	描述
      ToUserName	 接收方帐号（收到的OpenID）
      FromUserName	 开发者微信号
      CreateTime	 消息创建时间
      (MsgType	 news)
      ArticleCount	 图文消息个数，限制为10条以内
      Articles	 多条图文消息信息，默认第一个item为大图
      */
      $tplNews = "
        <xml>
          <ToUserName><![CDATA[%s]]></ToUserName>
          <FromUserName><![CDATA[%s]]></FromUserName>
          <CreateTime>%s</CreateTime>

          <MsgType><![CDATA[news]]></MsgType>

          <ArticleCount>%d</ArticleCount>

          <Articles>
          %s
          </Articles>
          <FuncFlag>1</FuncFlag>
        </xml> 
      ";
      
      
      
      
      if($inType=='event') {
        $msgType = "text";
        $contentStr = "Thanks for your following (LaoLin-jg)!";
        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
        echo $resultStr;
      }else if(/*$inType=='event' && */ !empty( $keyword )) {
        //$msgType = "text";
        //$contentStr = "Welcome to wechat world!";
        //$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
        $news=$this->replyNews($wechatLaolinObj->About($keyword));
        $resultStr= sprintf($tplNews, $fromUsername, $toUsername, $time, $news['n'], $news['str']);
        echo $resultStr;
      }else {
        echo "Input something...";
      }
    }else {
        echo "<img src='http://files.laolin.com/images/qrcode_for_laolin-jg.jpg'/>";
        exit;
    }
  }
    
  private function replyNews($items) {
      /*
      Title	 图文消息标题
      Description	 图文消息描述
      PicUrl	 图片链接，支持JPG、PNG格式，较好的效果为大图640*320，小图80*80。
      Url	 点击图文消息跳转链接
      */
      $tplNewsItem = "
           <item>
           <Title><![CDATA[%s]]></Title> 
           <Description><![CDATA[%s]]></Description>
           <PicUrl><![CDATA[%s]]></PicUrl>
           <Url><![CDATA[%s]]></Url>
           </item>
       ";
    $n=count($items);
    $strItem='';
    foreach ($items as $value) {
      $strItem.=sprintf($tplNewsItem,$value.Title,$value.Description,$value.PicUrl,$value.Url);
    }
    $ret=array();
    $ret['n']=$n;
    $ret['str']=$strItem;
    return $ret;

  }
  private function checkSignature() {
    $signature = $_GET["signature"];
    $timestamp = $_GET["timestamp"];
    $nonce = $_GET["nonce"];  
            
    $token = TOKEN;
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

?>