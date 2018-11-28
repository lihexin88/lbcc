<?php

namespace app\api\model;

use think\Model;
use think\Validate;

class News extends Model
{

    /**
     * 获取首页公告列表
     */
    public function homePageList($lang)
    {	
        $list = $this->order('create_time','desc')->limit(3)->field('id,url,title,en_title,create_time,update_time')->select()->toArray();
        return $list;
    }

     /**
     * 公告列表
     * @param string $p [页数]
     */
    public function newsListPage($p)
    {
        $list = $this->page($p, 8)->order('create_time desc')->field('id,url,title,en_title,create_time,update_time')->select()->toArray();
        foreach ($list as $k => $v) {
            $list[$k]['create_time'] = date('Y-m-d',strtotime($v['create_time']));
            $list[$k]['update_time'] = date('Y-m-d',strtotime($v['update_time']));
        }
        $data['list'] = $list;
        $data['count'] = ceil(($this->where($map)->count())/8);
        return $data;
    }

     /**
     * 公告详情
     * @param string $id [公告ID]
     */
    public function newsInfo($id)
    {
    	$map['id'] = $id;
    	$list = $this->where($map)->find();
        return $list;
    }

}
