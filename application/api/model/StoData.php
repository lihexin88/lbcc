<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/27
 * Time: 17:03
 */

namespace app\api\model;


use think\Model;
use think\Validate;

class StoData extends Model
{

    /**
     * ���������Ϣ
        $map['uid'] �û�ID
        $map['cur_id'] ����ID
        $number ����
        $time ���ֳ���ʱ��
        $num sto�������
     */
    public function implement($map,$number,$time,$create)
    {
        $total_number = $this->where($map)->value('total_number');
      	$create = $this->where($map)->value('create');
        $edit['number'] = $number;//sto�������+������������
        $edit['total_number'] = $number+$total_number;//sto�������+������������
      	if($create){
        	$edit['create'] = $create;
        }else{
         	$edit['create'] = time();
        }
        $edit['time'] = $time;//��ǰʱ��+45��
        $edit['status'] = 1;//״̬Ϊ1
        return $this->where($map)->update($edit);
    }
    //��ѯ��Ϣ
    public function data($id,$cur_id){
        $map['uid'] = $id;
        $map['cur_id'] = $cur_id;
        return $this->where($map)->find();
    }
    //����
    public function edit($id,$curid)
    {
        $map['uid'] = $id;
        $map['cur_id'] = $curid;
        $edit['number'] = 0;//0����
        $edit['time'] = time();//ִ��ʱ��
        $edit['status'] = 0;//0�ر�
        return $this->where($map)->update($edit);
    }
}