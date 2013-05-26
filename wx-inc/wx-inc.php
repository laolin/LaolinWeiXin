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
    $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

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
      if($inType=='event') {
        $msgType = "text";
        $contentStr = "Thanks for your following (LaoLin-jg)!";
        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
        echo $resultStr;
      }else if(/*$inType=='event' && */ !empty( $keyword )) {
        $msgType = "text";
        $contentStr = "Welcome to wechat world!";
        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
        echo $resultStr;
      }else {
        echo "Input something...";
      }
    }else {
        echo "";
        exit;
    }
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