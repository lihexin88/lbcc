<?php
namespace app\api\controller;
use app\common\controller\ApiBase;
use think\Session;
use think\Cookie;
use think\Request;
use think\Captcha;
use think\Db;

/**
 * 公告功能
 *
 * @remark 
 */


class News extends ApiBase
{
    public function _initialize() 
    {
        parent::_initialize();
    }

    public function News_limit()
    {
      	$data = model('News')->homePageList($lang);
      	foreach ($data as $k=>$v){
        	if(cookie('think_var') == 'zh-cn'){
               $array[$k] =	array_diff_key($v, ["en_title" => "xy"]);
            }else{
               $array[$k] = array_diff_key($v, ["title" => "xy"]);
            }
        }
       $r = rtn(1,'',$array);
       return $r;
    }


    /**
     * 公告页面信息
     * @param string @p [页数]
     */
    public function NewsListPage()
    {
        $p = input('p') ? input('p'): 1;
        if(false == ($data = model('News')->newsListPage($p))){
            $r = $this->rtn(-1,lang("null"));
        }else{
          	foreach ($data['list'] as $k=>$v){
                if(cookie('think_var') == 'zh-cn'){
               		$array[$k] =	array_diff_key($v, ["en_title" => "xy"]);
                }else{
                   	$array[$k] = array_diff_key($v, ["title" => "xy"]);
                }
            }
          	$array['count'] = $data['count'];
           	$r = rtn(1,'success',$array);
        }
        return $r;
    }

    /**
     * 公告详情
     * @param string @id [公告ID]
     */
    public function newsInfo()
    {
        $id = trim(input('id'));
        if(!$id) {
            $r = $this->rtn(-1,lang("parameter"));
        }else{
            if(false == ($data = model('News')->NewsInfo($id))){
                $r = $this->rtn(-1,lang("null"));
            }else{
                if(cookie('think_var') == 'zh-cn'){
                   unset($data["en_title"]);
                   unset($data["en_content"]);
                }else{
                   unset($data["title"]);
                   unset($data["content"]);
                }
              	$r = rtn(1,'success',$data);
            }    
        }
         return $r;
    }
   
}