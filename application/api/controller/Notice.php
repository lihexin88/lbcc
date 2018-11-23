<?php
namespace app\api\controller;
use app\common\controller\ApiBase;
use think\Session;
use think\Request;
use think\Captcha;
use think\Db;

/**
 * 公告功能
 *
 * @remark 
 */


class Notice extends ApiBase
{
    public function _initialize() 
    {
        parent::_initialize();
    }

    /**
     * 公告页面信息
     * @param string @p [页数]
     */
    public function noticeListPage()
    {
        $p = input('p') ? input('p'): 1;
        $map['state'] = 1;
        if(false == ($data = model('Notice')->noticeListPage($map,$p))){
            $r = $this->rtn(-1,lang("null"));
        }else{
            $r = $this->rtn(0,lang("success"),$data);
        }  
        return json($r);
    }

    /**
     * 公告详情
     * @param string @id [公告ID]
     */
    public function noticeInfo()
    {
        $id = trim(input('id'));
        if(!$id) {
            $r = $this->rtn(-1,lang("parameter"));
        }else{
            if(false == ($data = model('Notice')->noticeInfo($id))){
                $r = $this->rtn(-1,lang("null"));
            }else{
                $r = $this->rtn(0,lang("success"),$data);
            }    
        }

        return json($r);
    }
   
}