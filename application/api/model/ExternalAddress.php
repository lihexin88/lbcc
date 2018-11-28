<?php

namespace app\api\model;

use think\Model;
use think\Validate;

class ExternalAddress extends Model
{

    /**
     * 添加锁仓总信息
        $address 提笔地址
        $number 数量
        $id 用户ID
        $cur_id 币种ID
     */
    public function inserts($id,$cur_id,$ps,$address)
    {
        $ins['uid'] = $id;
        $ins['cur_id'] = $cur_id;
        $ins['address'] = $address;
        $ins['ps'] = $ps;
        $ins['create_time'] = time();
        return $this->insert($ins);
    }
}
