<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use think\Request;
use think\Db;

class Admin extends AdminBase
{
	/**
	 * 管理员列表
	 * @param  integer $p 页码
	 */
 	public function index($p = 1){
		$map = [];
		$keywords = input('get.keywords')?input('get.keywords'):null;
		if($keywords){
			$map['username']  = array('like', '%'.trim($keywords).'%');
		}
 		$this->assign("info",model('Admin')->adminList($p,$map));
 		return $this->fetch();
 	}

 	/**
 	 * 新增管理员
 	 */
 	public function add_user()
    {
    	if(Request::instance()->isPost()){
           $map = input('post.');
           if(empty($map['username'])){
              return json(array('status' => 0, 'info' => '请输入用户名'));
           }
           if(!array_key_exists('group_id',$map)) {
              return json(array('status' => 0, 'info' => '请选择用户组'));
           }else{
             $map['user_type'] = $map['group_id'];
             return json(model('Admin')->saveInfo($map));
           }
          
    	}
        $info = ['id'=>null,'username'=>null,'description'=>null,'group_id'=>2,'user_type'=>1,'status'=>1];
        $this->assign("group_list", model("AuthGroup")->getUserGroup());
        $this->assign('info',$info);
        return $this->fetch();
    }	
	
	/**
	 * 修改管理员信息
	 * @param  string $id 管理员ID
	 */
	public function edit_user($id)
	{
		$this->assign("group_list", model("AuthGroup")->getGroup());
    	$this->assign('info',model("Admin")->adminInfo($id));
        return $this->fetch('add_user');
	}

	/**
	 * 用户组列表
	 */
	public function group(){
	    $this->assign("list", model("AuthGroup")->groupList());
        return $this->fetch();
    }

    /**
     * 新增用户组
     */
    public function add_group()
    {
    	if(Request::instance()->isPost()){
			return json(model('AuthGroup')->saveInfo(input('post.')));
		}
		$this->assign('info',array('id'=>null,'title'=>null,'description'=>null));
		return $this->fetch();
	}

    /**
     * 修改用户组信息
     * @param  string $id 用户组ID
     */
    public function edit_group($id)
    {
    	$this->assign('info',db('AuthGroup')->find($id));
        return $this->fetch('add_group');
    }

    /**
     * 用户组授权
     * @param  string $id 用户组ID
     */
    public function group_auth($id)
    {
        $request= Request::instance();
    	if(Request::instance()->isPost()){
    		$data['id'] = input('post.id');
            $rules = $request->post('rules/a');
    		if($rules){
    			sort($rules);
    			$data['rules']  = implode( ',' , array_unique($rules));
                $result = db('AuthGroup')->where('id',$data['id'])->update(['rules' => $data['rules']]);
    			if($result){
    				$this->success('更新完成');
    			}else{
    				$this->error('权限表未变动');
    			}
    		}else{
    			$this->error('权限表未提交成功！');
    		}
    	}
    	$this->assign("info",model("AuthGroup")->getGroupRules($id));
    	$this->assign("list", model("AuthRule")->ruleList());
    	return $this->fetch();
    }

    /**
     * 权限列表
     */
    public function rule(){
	    $this->assign("list", model("AuthRule")->ruleList());
	    $this->assign('is_show',dict_list('is_show'));
	    $this->assign('status',dict_list('common_state'));
        return $this->fetch();
    }

    /**
     * 新增权限
     */
    public function add_rule()
    {	
    	if(Request::instance()->isPost()){
            $map = input('post.');
            $map['module'] = 'admin';
    		return json(model('AuthRule')->saveInfo($map));
    	}
    	$this->assign('is_show',dict_list('is_show'));
    	$this->assign('pidlist',model('AuthRule')->pidList(0));
    	$this->assign('info',['id'=>null,'title'=>null,'name'=>null,'pid'=>0,'condition'=>null,'is_show'=>1,'icon'=>null]);
    	return $this->fetch();
    }

    /**
     * 修改权限信息
     * @param  string $id 权限ID
     */
    public function edit_rule($id)
    {
    	if(Request::instance()->isPost()){
    		return json(model('AuthRule')->saveInfo(input('post.')));
    	}
    	$this->assign('is_show',dict_list('is_show'));
    	$this->assign('pidlist',model('AuthRule')->pidList(0));
    	$this->assign('info',model('AuthRule')->infodata(array('id'=>$id)));
        return $this->fetch('add_rule');
    }
	
}