<?php


class wechatLaolin {
  
  function __construct()
  {
  }
  
  public function run($b) {  
  
     // error_log( "  ## b=$b, ", 
     // 3, dirname( __FILE__ ).'/../'.'logwx-'.TOKEN.'.log');
    switch($b) {
        //使用lazyRest的API，直接读wordpress的指定 页面的数据
      
      case '1':
      case 'r1':
      case 'rq':
        return self::_showSomePost('ID=4158','');
      case '2':
      case 'r2':
      case 'rw':
        return self::_showSomePost('ID=4138','');
      case '3':
      case 'r3':
      case 're':
        return self::_showSomePost('ID=4147','');
      case '4':
      case 'r4':
      case 'rr':
        return self::_showSomePost('ID=4153','');
      
      case '5':
      case 'r5':
      case 'rt':
      case 'projects':
        return self::_showSomePost('ID=4161','工程项目');
        break;
      case '6':
      case 'r6':
      case 'ry':
      case 'awards':
        return self::_showSomePost('ID=4163','获奖情况');
        break;
      case '7':
      case 'r7':
      case 'ru':
      case 'publications':
        return self::_showSomePost('ID=4165','发表论文');
        break;
      case '8':
      case 'r8':
      case 'ri':
      case 'hobbies':
        return self::_showSomePost('ID=4180','兴趣爱好');
      
      case '9':
      case 'r9':
      case 'ro':
      case 'contact':
        return self::_showSomePost('ID=4168','联系方式');
        break;
        
      case 'laolin':      
        //使用lazyRest的API，直接读wordpress的指定page的全部子页面的数据
        //我的Wordpress的简历页面的ID为4132,这个页面内容没有用
        //所有的子页面对应简历的一个内容, 这些会由LazyREST api返回给本页面JSON数据
        return  self::_showSomePost('post_parent=4132&post_status=publish','林建萍(LaoLin) 同济大学建筑设计研究院（集团）有限公司 高级工程师 一级注册结构工程师');
      default: 
        return array();
    }  
  }
  public function welcomeStr(){
    return "欢迎关注老林的微信公众帐号。很高兴能通过这个平台与你交流。发送‘1’了解老林简历，发送‘2’至‘9’了解老林的其他介绍。";
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
  public function _showSomePost($query,$title){  
  
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

if(isset( $_GET["echostr"])) {
  $wechatObj->valid();
} else {
  $wechatObj->responseMsg();
}

