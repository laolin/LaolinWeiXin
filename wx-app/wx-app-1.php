<?php

$wechatLaolinObj = new wechatLaolin();

class wechatLaolin {
  
  function __construct()
  {
  }
  
  public function About($b) {  
    switch($b) {
        //使用lazyRest的API，直接读wordpress的指定 页面的数据
      case 'contact':
        //我的Wordpress的联系页面的ID为4168
        return $this->_showSomePost('ID=4168','联系方式');
        break;
      
      case 'projects':
        return $this->_showSomePost('ID=4161','工程项目');
        break;
      case 'awards':
        return $this->_showSomePost('ID=4163','获奖情况');
        break;
      case 'publications':
        return $this->_showSomePost('ID=4165','发表论文');
        break;
      case 'hobbies':
        return $this->_showSomePost('ID=4180','兴趣爱好');
      
      default:      
        //使用lazyRest的API，直接读wordpress的指定page的全部子页面的数据
        //我的Wordpress的简历页面的ID为4132,这个页面内容没有用
        //所有的子页面对应简历的一个内容, 这些会由LazyREST api返回给本页面JSON数据
        return $this->_showSomePost('post_parent=4132&post_status=publish','林建萍(LaoLin) 同济大学建筑设计研究院（集团）有限公司 高级工程师 一级注册结构工程师');
    }  
  }

  
      /*
      Title	 图文消息标题
      Description	 图文消息描述
      PicUrl	 图片链接，支持JPG、PNG格式，较好的效果为大图640*320，小图80*80。
      Url	 点击图文消息跳转链接
      */
  function _showSomePost($query,$title){  
    $url='http://api.laolin.com/rest/api/wp4_posts/list/'.$query;

    $rest=file_get_contents($url);
    $res=json_decode($rest,true);
    $dataPost=array();
    if($res['err_code']!=0) {
      $dataPost[]=array('Title'=>'Error',
            'Description'=>'error code: ['.$res['err_code'].'] '.$res['err_msg']);
    } else {
      foreach ($res['data']['items'] as $key => $row) {
        $item=array();
        $item['Title']=$row['post_title'];
        $item['Description']=$row['post_content'];
        $dataPost[]=$item;
      }
    }
    return $dataPost;
  }
}
    
include_once ( dirname( __FILE__ ).'/../wx-inc/'.'wx-inc.php');

