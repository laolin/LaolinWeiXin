<?php


class wechatLaolin {
  
  function __construct()
  {
  }
  
  static public function About($b) {  
  
      error_log( "  ## b=$b, ", 
      3, dirname( __FILE__ ).'/../'.'logwx-'.TOKEN.'.log');
    switch($b) {
        //使用lazyRest的API，直接读wordpress的指定 页面的数据
      case 'r9':
      case 'contact':
        //我的Wordpress的联系页面的ID为4168
        return self::_showSomePost('ID=4168','联系方式');
        break;
      
      case 'r1':
        return self::_showSomePost('ID=4158','');
      case 'r2':
        return self::_showSomePost('ID=4138','');
      case 'r3':
        return self::_showSomePost('ID=4147','');
      case 'r4':
        return self::_showSomePost('ID=4153','');
      
      case 'r5':
      case 'projects':
        return self::_showSomePost('ID=4161','工程项目');
        break;
      case 'r6':
      case 'awards':
        return self::_showSomePost('ID=4163','获奖情况');
        break;
      case 'r7':
      case 'publications':
        return self::_showSomePost('ID=4165','发表论文');
        break;
      case 'r8':
      case 'hobbies':
        return self::_showSomePost('ID=4180','兴趣爱好');
      
      case 'laolin':      
        //使用lazyRest的API，直接读wordpress的指定page的全部子页面的数据
        //我的Wordpress的简历页面的ID为4132,这个页面内容没有用
        //所有的子页面对应简历的一个内容, 这些会由LazyREST api返回给本页面JSON数据
        return  self::_showSomePost('post_parent=4132&post_status=publish','林建萍(LaoLin) 同济大学建筑设计研究院（集团）有限公司 高级工程师 一级注册结构工程师');
      default: 
        return array();
    }  
  }

  
      /*
      Title	 图文消息标题
      Description	 图文消息描述
      PicUrl	 图片链接，支持JPG、PNG格式，较好的效果为大图640*320，小图80*80。
      Url	 点击图文消息跳转链接
      */
   static function _showSomePost($query,$title){  
  
      error_log( " q=$query\n", 
      3, dirname( __FILE__ ).'/../'.'logwx-'.TOKEN.'.log');
      
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

