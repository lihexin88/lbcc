<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use think\Request;
use think\Db;

class Order extends Admin
{
    /**
     * 列表
     * @param  integer $p 页码
     */
    public function index($p = 1)
    {
        $map = [];
        $keywords = input('get.keywords') ? input('get.keywords') : null;
        if ($keywords) {
            $map['order_sn'] = array('like', '%' . trim($keywords) . '%');
        }
        if (is_numeric(input('get.order_status'))) {
            $map['order_status'] = input('get.order_status');
        }
    
        // pre($map);exit;
        $this->assign("info", model('Order')->infoList($map, $p));
        $this->assign("order_status", model("Common/Dict")->showList('order_status'));//状态
        $this->assign("userlist", model("User")->showList());
        return $this->fetch();
    }

    /**
     * 修改信息
     * @param  string $id ID
     */
    public function edit($id)
    {
        if (Request::instance()->isPost()) {
            return json(model('Order')->changeState(input('post.')));
        }
        $this->assign("info", model('Order')->infodata(array('id'=>$id)));
        $this->assign('pagename','修改用户');
        return $this->fetch('add');
    }

    /**
     * 撤单
     * @param  string $id ID
     */
    public function cancel($order_id)
    {
        if(Request::instance()->isPost()){
            return json(model('Order')->saveInfo(input('post.')));
        }
        $this->assign("info", model('Order')->infodata(array('id'=>$order_id)));

        $this->assign("person", model('Order')->person($order_id));
        $this->assign('pagename','修改用户');

        return $this->fetch();

    }

    public function cancelactive()
    {
        if(Request::instance()->isPost()){
            if(!input('post.canceler_id')){
                return json(array('status' => 0, 'info' => '请选择撤单人'));
            }else{
                $result = model('Order')->cancel(input('post.'));
                if($result['status'] == 0){
                    return json(array('status' => 0, 'info' => $result['info']));
                }else{
                    $result = model('Order')->saveInfo(input('post.'));
                    $orderinfo = model('Order')->infodata(array('id'=>input('post.id')));
                    return json($result); 
                }
                
            }
            
        }
    }

    /**
     * 删除信息
     * @param  string $id ID
     */
    public function delete()
    {
        if (Request::instance()->isPost()) {

            return json(model('Order')->deleteInfo(input('post.id')));
        }
    }
}