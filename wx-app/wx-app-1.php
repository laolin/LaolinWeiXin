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
        $this->_showSomePost('ID=4168','联系方式');
        break;
      
      case 'projects':
        $this->_showSomePost('ID=4161','工程项目');
        break;
      case 'awards':
        $this->_showSomePost('ID=4163','获奖情况');
        break;
      case 'publications':
        $this->_showSomePost('ID=4165','发表论文');
        break;
      case 'hobbies':
        $this->_showSomePost('ID=4180','兴趣爱好');
      
      default:      
        //使用lazyRest的API，直接读wordpress的指定page的全部子页面的数据
        //我的Wordpress的简历页面的ID为4132,这个页面内容没有用
        //所有的子页面对应简历的一个内容, 这些会由LazyREST api返回给本页面JSON数据
        $this->_showSomePost('post_parent=4132&post_status=publish','林建萍(LaoLin) 同济大学建筑设计研究院（集团）有限公司 高级工程师 一级注册结构工程师');
    }  
  }

  function _showSomePost($query,$title){  
    $url='http://api.laolin.com/rest/api/wp4_posts/list/'.$query;

    $rest=file_get_contents($url);
    $res=json_decode($rest,true);
    if($res['err_code']!=0) {
      $data['about_laolin'][]=
          array('post_content'=>'Error: ['.$res['err_code'].'] '.$res['err_msg']);
    } else {
      $data['about_laolin']=$res['data']['items'];
      foreach ($data['about_laolin'] as $key => $row) {
        $menu_order[$key]  = +$row['menu_order'];
        $data['about_laolin'][$key]['post_content']=nl2br($row['post_content']);
      }
    }
  }
}
    
include_once ( dirname( __FILE__ ).'/../wx-inc/'.'wx-inc.php');
