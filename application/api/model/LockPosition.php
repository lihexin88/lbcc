<?php

namespace app\api\model;

use think\Model;
use think\Validate;

class LockPosition extends Model
{

    /**
     * 添加锁仓信息
     */
    public function implement($uid,$number,$time)
    {
    	$ins['uid'] = $uid;
    	$ins['lock_time'] = time();
    	$ins['number'] = $number;
    	$ins['expiry_time'] = time()+$time;
        $list = $this->where('uid',$uid)->insert();
        return $list;
    }
}
