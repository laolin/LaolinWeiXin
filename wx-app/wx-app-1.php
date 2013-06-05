<?php


class wechatLaolin {
  
  protected $_str_Welcome="欢迎关注老林的微信公众帐号。很高兴能通过这个平台与你交流。";
  protected $_str_Send0ForHelp='发送‘?’或‘0’获取帮助。';
  protected $_str_ByLaolin="\n By: LaoLin( http://laolin.com/ ).";
  
  protected $_str_CmdUnknow='亲，未能识别的命令。';
  protected $_str_CmdChg='不好意思，亲，操作命令已改变。';
  protected $_str_SilenceIsGold="Speech is silver, silence is gold.\n\t :-)";
  
  protected $_str_Help=
  "目前主要功能:
【0】本帮助信息
【1】随机显示老林的介绍内容
【TJAD】TJAD相关功能";
  protected $_str_AppWebPageURL="\n如有任何意见或建议请点击 http://laolin.com/lin/?p=4406 ，谢谢。";
    
  function __construct()
  {
  }
  
  public function run($content) {  
  
     // error_log( "  ## b=$b, ", 
     // 3, dirname( __FILE__ ).'/../'.'logwx-'.TOKEN.'.log');
    $laolinInfo=array(  
      'ID=4158',    'ID=4138',    'ID=4147',    'ID=4153',
      'ID=4161',    'ID=4163',    'ID=4165',    'ID=4180',
      'ID=4168'    );
    switch($content) {
      case '':
        return $this->_str_SilenceIsGold;
      case '?':
      case '0':
      case 'help':
      case 'f1': case 'F1':
        return $this->_str_Help.$this->_str_AppWebPageURL;
      
      //===============================================
      //使用lazyRest的API，直接读wordpress的指定 页面的数据
      case '1':
        $i=rand(0,8);
        return $this->_showSomePost($laolinInfo[$i]);
      case '2':
      
      case '3':
      
      case '4':
      
      
      case '5':
      
      case '6':
      
      case '7':
      
      case '8':
      
      case '9':
        return $this->_str_CmdChg.$this->_str_Send0ForHelp.$this->_str_AppWebPageURL ;
        
      case 'laolin':      
        //使用lazyRest的API，直接读wordpress的指定page的全部子页面的数据
        //我的Wordpress的简历页面的ID为4132,这个页面内容没有用
        //所有的子页面对应简历的一个内容, 这些会由LazyREST api返回给本页面JSON数据
        return  $this->_showSomePost('post_parent=4132&post_status=publish');
      default: 
        $ret=$this->run2($content);//复杂命令处理
        return $ret==='' ?
          $this->_str_CmdUnknow.$this->_str_Send0ForHelp.$this->_str_AppWebPageURL :
          $ret;
    }  
  }
  
  //复杂命令处理
  public function run2($content) {
  
    if(strncasecmp('tjad',$content,4)==0){
      return $this->_func_Tjad($content);
    }
  
    return '';
  }
  
  protected $_str_Tjad_Usage="欢迎使用TJAD工作号码查询。
**使用协议**
============
为方便大家工作设计了此号码查询功能。
所有查询系统后台均有记录。
请爱惜此功能，以确保此功能持续运行。
任何的滥用行为可能导致此功能被关闭。
请不要大量进行查询。
（大量查询请至集团EKP平台。）
继续查询表示你同意使用协议，否则请停止使用本功能。

**功能**
========
查询集团职能部门、同励院、一~四院的工作电话号码。
【TJAD+名字】查电话号码
【TJAD+电话号码】查名字

**继续查询表示你同意使用协议**";
  protected $_str_Tjad_TelTooShort="电话号码要求至少4位。";
  protected $_str_Tjad_NameTooShort="输入的名字太短。";
  protected $_str_Tjad_Error="查询结果出错。";
  protected $_str_Tjad_NoResult="查不到任何结果。";
  protected $_str_Tjad_QueryDataIs="你查询的是：";
  protected $_str_Tjad_TheResultIs="查询结果：";
  protected $_n_tjad_MaxResultCount=10;
  protected $_str_tjad_MaxResultGot="\n已达到系统返回的最多结果数，如有部分结果未能查到，请调整查询条件。";
  
  
  
  //复杂命令处理 之 TJAD 系列
  protected function _func_Tjad($content) {  
  
    $kickCharList=array(" ","\t","\r","\n","\r","\v","\b",";","_",",",".","+","-","*","/",'\\',"'",'"');
    
    //'tjad99-_-林'  => get max to 99 results
    if(substr($content,6,3)=='-_-'){//an easter egg ^_^
      $this->_n_tjad_MaxResultCount=max(10,intval(substr($content,4,2)));
      $dat=str_replace($kickCharList,'',substr($content,9));
    } else {//正常情况都是这样：
      $dat=str_replace($kickCharList,'',substr($content,4));
    }
    $apiPath='http://api.laolin.com/rest/api/tjad_contact/list/count='.
      $this->_n_tjad_MaxResultCount.'&';
    
    if(strlen($dat)==0){
      return $this->_str_Tjad_Usage.$this->_str_AppWebPageURL;
    }
    if(is_numeric($dat)){//纯数字
      if(strlen($dat)<4){
        return $this->_str_Tjad_TelTooShort;
      }
      $apiUrl=$apiPath."tel=$dat";
    } else {
      if(mb_strlen($dat,'utf8')<=1&& $this->_n_tjad_MaxResultCount<=10){
        return $this->_str_Tjad_NameTooShort;
      }
      $apiUrl=$apiPath."name=$dat";
    }
    
    $ret=$this->getDataFromApi($apiUrl);
    if($ret['err_code']=='0'){
      if(! isset($ret['data']['items'])||($numberResult=count($ret['data']['items']))<=0){
        return $this->_str_Tjad_NoResult."($dat)";
      }
      $txt=$this->_str_Tjad_QueryDataIs.$dat;
      $i=0;
      foreach ($ret['data']['items'] as $key => $row) {
        $i++;
        $txt.="\n$i.".$row['dep'].','.$row['jg'].','.$row['name'].':'.$row['tel'];
      }
      if($numberResult>=$this->_n_tjad_MaxResultCount) {
        $txt.=$this->_str_tjad_MaxResultGot;
      }
    } else {
      return $this->_str_Tjad_Error.'('.$ret['err_msg'].')';
    }
    return $txt.$this->_str_AppWebPageURL;
  }
  
  //
  public function getDataFromApi($url) {  
    $rest=file_get_contents($url);
    return json_decode($rest,true);
  }
  
  
  public function welcomeStr(){
    return $this->_str_Welcome.$this->_str_Send0ForHelp.$this->_str_ByLaolin;
  }

  public function showWebPage(){
    include_once ( dirname( __FILE__ ).'/'.'first-page.html');
  }
  
      /*
      Title	 图文消息标题
      Description	 图文消息描述
      PicUrl	 图片链接，支持JPG、PNG格式，较好的效果为大图640*320，小图80*80。
      Url	 点击图文消息跳转链接
      */
  public function _showSomePost($query){  
  
     // error_log( " q=$query\n", 
     // 3, dirname( __FILE__ ).'/../'.'logwx-'.TOKEN.'.log');
      
    $url='http://api.laolin.com/rest/api/wp4_posts/list/'.$query;

    $rest=file_get_contents($url);
    $res=json_decode($rest,true);
    $dataPost=array();
    $menu_order=array();
    if($res['err_code']!=0) {
      $dataPost[]=array('Title'=>'Error',
            'Description'=>'error code: ['.$res['err_code'].'] '.$res['err_msg']);
    } else {
      foreach ($res['data']['items'] as $key => $row) {
        $item=array();
        $item['Title']= $row['post_title'];
        $item['Description']=strip_tags($row['post_content'] );
        $item['PicUrl']='http://files.laolin.com/images/linjp-2012.9.3-180x180.jpg';
        $item['Url']= 'http://laolin.com/lin/?page_id='.$row['ID'];
        $dataPost[]=$item;
        $menu_order[$key]  = +$row['menu_order'];
      }
      
      array_multisort($menu_order, SORT_ASC, $dataPost);
    }
    return $dataPost;
  }
}
    
include_once ( dirname( __FILE__ ).'/../wx-inc/'.'wx-inc.php');

$workerObj = new wechatLaolin();
$wechatObj = new wechatCallbackapiTest($workerObj);

//(isset( $_GET["echostr"])) {
//  $wechatObj->valid();
//} else {
  $wechatObj->responseMsg();
//}

