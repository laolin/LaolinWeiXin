<?php
/**
  * wechat php test
  */

//define your token
if( !defined('TOKEN') )die('pls define TOKEN first.');
//  define("TOKEN", "defWeixinToken");//should be defined in 'wx-token.php'

class wechatCallbackapiTest
{
  //构造函数需要传入一个对象，来完成实际工作。
  //这个对象需要定义三个函数
  //1,showWebPage() 显示直接打开网址时显示的页面内容
  //2,welcomeStr() 返回一个字符串，用于新用户关注时发送给新用户的欢迎消息
  //3,run($content) 处理用户发来的消息内容，返回一个非空数组（表示回复news型消息）或空数组（表示回复欢迎消息）、字符串（表示回复text型消息）。
  public  $workerObj;
  public function __construct($workerObj) {
     $this->workerObj = $workerObj;
  }

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
      $contents = trim($postObj->Content);
      $inType= trim($postObj->MsgType);
      $Event=($inType=='event')?trim($postObj->Event):'xE';
      
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
      
      
      
      error_log(date("[Y-m-d H:i:s")." ".$_SERVER['REQUEST_URI']."],".
      "u:$fromUsername,t:$inType,k:$contents,e:$Event\n", 
      3, dirname( __FILE__ ).'/../'.'logwx-'.TOKEN.'.log');
      
      
        
      $resultStr='';
      $welcomeTxt = $this->workerObj->welcomeStr();
      if($inType=='event') {
        $msgType = "text";
        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $welcomeTxt);
      } else if( !empty( $contents ) ) {
        $msgType = "text";
        $resData=$this->workerObj->run($contents);
        if(is_array ($resData)){
          $newsCount=count($resData);
          if($newsCount>0){
            $newsStr= $this->itemsXMLString($resData);
            $resultStr= sprintf($tplNews, $fromUsername, $toUsername, $time, $newsCount, $newsStr);
          }
        }
        if ($resultStr=='' && is_string($resData)&& $resData!=='') {
          $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $resData);
        } else {
          $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $welcomeTxt);
        }
        
      //error_log( "  #A# res=$resultStr\n", 
      //3, dirname( __FILE__ ).'/../'.'logwx-'.TOKEN.'.log');
      } else { //contents is empty
          $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, "Speech is silver, silence is gold.\n ----LaoLin :-)");
      }
      echo $resultStr;

    }else { //no post data: 说明不是来自微信服务器的请求，而是来自人工的请求==>showWebPage
        $this->workerObj->showWebPage();
    }
  }
    
  private function itemsXMLString($items) {
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
      $strItem.=sprintf($tplNewsItem,
        $value['Title'],$value['Description'],
        $value['PicUrl'],$value['Url']);
    }
    
     // error_log( "  #A# str=$strItem\n", 
     // 3, dirname( __FILE__ ).'/../'.'logwx-'.TOKEN.'.log');
    return $strItem;

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