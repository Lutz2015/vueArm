<?php
// +----------------------------------------------------------------------
// | Description: 用户
// +----------------------------------------------------------------------
// | Author: linchuangbin <linchuangbin@honraytech.com>
// +----------------------------------------------------------------------

namespace app\admin\model;

use think\Db;
use app\admin\model\Common;
use com\verify\HonrayVerify;

class Contract extends Common
{

    /**
     * 为了数据库的整洁，同时又不影响Model和Controller的名称
     * 我们约定每个模块的数据表都加上相同的前缀，比如微信模块用weixin作为数据表前缀
     */
	protected $name = 'admin_contract';
    protected $createTime = 'create_time';
    protected $updateTime = false;
	protected $autoWriteTimestamp = true;
	protected $insert = [
		'status' => 1,
        ];
    private $not_basic_param = array('hardware', 'software', 'install', 'serve', 'other', 'bill');
    private $service_list = array('hardware', 'software', 'install', 'serve', 'other');
    
    /**
	 * 创建合同
	 * @param  array   $param  [description]
	 */
	public function add($param)
	{
        $condition = array();
        $condition['number'] = $param['number'];
        $condition['status'] = $param['status'];
        $contract_data = Db::name('admin_contract')->where($condition)->select();
        if ($contract_data){
            $this->error = '合同号重复';
            return false;
        }
        $this->startTrans();
		try {
            $tmp_param = $param;
            foreach ($this->not_basic_param as $not_basic_item){
                unset($param[$not_basic_item]);
            }
            Db::name('admin_contract')->insert($param);

            $service_info = array();

            foreach ($this->service_list as $service){
                $item = $tmp_param[$service];
                if (strlen($item) <= 0){
                    continue;
                }
                $item = json_decode($item, true);
                foreach ($item as $tmp_item){
                    unset($tmp_item['name']);
                    $tmp_item['type'] = $service;
                    $tmp_item['check_time'] = $param['check_time'];
                    $tmp_item['begin_time'] = $param['begin_time'];
                    $tmp_item['end_time']   = $param['end_time'];
                    $tmp_item['status']     = $param['status'];
                    $service_info[] = $tmp_item;
                }
            }
            Db::name('admin_contract_service')->insertAll($service_info);
            
            $bill_info = array();
            if (isset($tmp_param['bill']) && strlen($tmp_param['bill']) > 0){
                $item = json_decode($tmp_param['bill'], true);
                foreach ($item as $target){
                    $target['number'] = $param['number'];
                    $target['status'] = $param['status'];
                    unset($target['timeText']);
                    unset($target['date']);
                    unset($target['quarter']);
                    $bill_info[] = $target;
                }
            }
            Db::name('admin_contract_tax')->insertAll($bill_info);
            $this->commit();
			return true;
		} catch(\Exception $e) {
			$this->rollback();
			$this->error = '添加失败';
			return false;
		}
	}

    /**
     * 根据合同号和状态修改合同基本信息
     *
     */
    public function modify($param){
        $number = $param['number'];
        $status = $param['status'];
        unset($param['number']);
        unset($param['status']);
        $basic_list = array();
        $service_list = array();
        $bill_list = array();
        foreach($param as $key=>$value){
            if(!in_array($key, $this->not_basic_param)){
                $basic_list[$key] = $value;
            }elseif(in_array($key, $this->service_list)){
                $service_list[$key] = $value;
            }else{
                $bill_list[$key] = $value;
            }
        }
        $condition = array();
        $condition['number'] = $number;
        if (intval($status != 0)){
            $condition['status'] = $status;
        }
        $contract_data = Db::name('admin_contract')->where($condition)->select();
        if (!$contract_data){
            $this->error = '合同不存在';
            return false;
        }

        $this->startTrans();
        try {
            $flag = 0;
            if ($basic_list){
                $data = array();
                foreach ($basic_list as $key=>$value){
                    if ($key == 'check_time'){
                        if (intval($contract_data[0][$key]) == 0 && intval($value) > time()){
                            $data['status'] = 1;
                            $contract_data[0]['status'] = 1;
                            $flag = 1;
                        }
                    }
                    if ($key == 'stop_time'){
                        if (intval($value) != 0 && $value <= time()){
                            $data['status'] = 2;
                            $contract_data[0]['status'] = 2;
                            $flag = 1;
                        }else{
                            $data['status'] = 1;
                            $contract_data[0]['status'] = 1;
                            $flag = 1;
                        }
                    }
                    $data[$key] = $value;
                    $contract_data[0][$key] = $value;
                }
                Db::name('admin_contract')->where($condition)->update($data);
                if ($flag == 1){
                    $data = array();
                    $data['status'] = $contract_data[0]['status'];
                    Db::name('admin_contract_service')->where($condition)->update($data);
                    Db::name('admin_contract_tax')->where($condition)->update($data);
                }
            }
            if ($service_list){
                $type = '';
                foreach ($service_list as $key=>$value){
                    $type .= $key . ',';
                }
                $type = rtrim($type, ',');
                
                Db::name('admin_contract_service')->where($condition)->where('type', 'in', $type)->delete();
                
                $service_info = array();

                foreach ($service_list as $key=>$value){
                    if (strlen($value) <= 0){
                        continue;
                    }
                    $tmp_service = json_decode($value, true);
                    foreach ($tmp_service as $tmp_item){
                        $tmp_item['type'] = $key;
                        $tmp_item['check_time'] = $contract_data[0]['check_time'];
                        $tmp_item['begin_time'] = $contract_data[0]['begin_time'];
                        $tmp_item['end_time']   = $contract_data[0]['end_time'];
                        $tmp_item['status']     = $contract_data[0]['status'];
                        $service_info[] = $tmp_item;
                    }
                }
                Db::name('admin_contract_service')->insertAll($service_info);
            }
            if ($bill_list){
                $bill_info = array();
                Db::name('admin_contract_tax')->where($condition)->delete();
                foreach ($bill_list as $key=>$value){
                    if (strlen($value) <= 0){
                        continue;
                    }
                    $tmp_bill = json_decode($value, true);
                    foreach ($tmp_bill as $target){
                        $target['number'] = $contract_data[0]['number'];
                        $target['status'] = $contract_data[0]['status'];
                        unset($target['timeText']);
                        unset($target['date']);
                        unset($target['quarter']);
                        $bill_info[] = $target;
                    }
                }
                Db::name('admin_contract_tax')->insertAll($bill_info);
            }

            $this->commit();
			return true;
        } catch(\Exception $e) {
            $this->rollback();
			$this->error = '修改失败';
			return false;
        }
    }

    /*
     *显示指定的合同
     */
    public function showDetail($param){
        if (isset($param['number']) && $param['number']){
            try {
                $condition = array();
                $condition['number'] = $param['number'];
                $info = Db::name('admin_contract')->where($condition)->select();
                $service = Db::name('admin_contract_service')->where($condition)->select();
                $bill = Db::name('admin_contract_tax')->where($condition)->select();
                foreach ($info as &$item){
                    foreach ($service as $service_item){
                        if ($service_item['status'] == $item['status']){
                            $item[$service_item['type']][] = $service_item;
                        }
                    }
                    foreach ($bill as $bill_item){
                        if ($bill_item['status'] == $item['status']){
                            $item['bill'][] = $bill_item;
                        }
                    }
                }
                
                $this->commit();
                return $info;
            } catch(\Exception $e) {
                $this->rollback();
                $this->error = '查询失败';
                return false;
            }
        }
    }

    /*
     *结果展示页
     */
    public function showResult($param){
        $condition = array();
        if (isset($param['from_time']) && isset($param['to_time'])){
            $from_time = $param['from_time'];
            $to_time = $param['to_time'];
            $condition['check_time'] = array('<=', $to_time);
            $condition['check_time'] = array('>=', $from_time);
        }
        if (isset($param['status']) && $param['status']){
            $status = $param['status'];
            $condition['status'] = $status;
        }
        if (isset($param['product']) && $param['product']){
            $product = $param['product'];
        }
        if (isset($param['number']) && $param['number']){
            $number = $param['number'];
            $condition['number'] = $number;
        }

        $result = array();
        $result['list'] = array();
        $info = Db::name('admin_contract')->where($condition)->select();
        foreach ($info as $key=>$item){
            $number = $item['number'];
            $status = $item['status'];
            $result['list'][$number . '_' . $status] = $item;
        }
        $res = Db::name('admin_contract_tax')->where($condition)->select();
        foreach ($res as $key=>$item){
            $number = $item['number'];
            $status = $item['status'];
            $result['list'][$number . '_' . $status]['bill'][] = $item;
        }

        foreach ($result['list'] as $number_item=>&$info_item){
            $info_item['bill_info'] = array();
            $key_list = array('billAll17', 'billAll06', 'billAll0', 'billNoTax17', 'billNoTax06', 'billNoTax0', 'billTax17', 'billTax06', 'billTax0');
            foreach ($key_list as $key_item){
                $info_item['bill_info'][$key_item] = 0;
            }
            if (isset($info_item['bill'])){
                foreach ($info_item['bill'] as $bill_item){
                    foreach ($key_list as $key_item){
                        $info_item['bill_info'][$key_item] += $bill_item[$key_item];
                    }
                }
            }
        }
        
        if (isset($product) && $product){
            $condition['cate'] = $product;
        }
        $contract_list = array();
        $ret = Db::name('admin_contract_service')->where($condition)->select();
        foreach ($ret as $key=>$value){
            $type = $value['type'];
            $number = $value['number'];
            $status = $value['status'];
            $result['list'][$number . '_' . $status][$type][] = $value;
            if (!in_array($number . '_' . $status, $contract_list)){
                $contract_list[] = $number . '_' . $status;
            }
        }
        if (isset($product) && $product){
            foreach ($result['list'] as $key=>$result_item){
                if (!in_array($key, $contract_list)){
                    unset($result['list'][$key]);
                }
            }
        }
        $service_list = array('hardware', 'software', 'install', 'serve', 'other');
        foreach ($result['list'] as $key=>&$result_item){
            $contract_price = 0;
            foreach ($service_list as $service){
                $total = 0;
                if ($service != 'serve'){
                    if (isset($result_item[$service])){
                        foreach ($result_item[$service] as $tmp){
                            $total += $tmp['unit_price'] * $tmp['amount'];
                        }
                        $result_item[$service.'_price'] = $total;
                        $contract_price += $total;
                    }
                }else {
                    if (isset($result_item[$service])){
                        foreach ($result_item[$service] as $tmp){
                            if (isset($from_time) && isset($to_time)){
                                if ($from_time < $tmp['begin_time']){
                                    $from_time = $tmp['begin_time'];
                                }
                                if ($to_time > $tmp['end_time']){
                                    $to_time = $tmp['end_time'];
                                }
                                if ($from_time > $to_time){
                                    continue;
                                }
                                $total += $tmp['unit_price'] * $tmp['amount'] * ($to_time - $from_time) / ($tmp['end_time'] - $tmp['begin_time']);
                            }else{
                                $total += $tmp['unit_price'] * $tmp['amount'];
                            }
                        }
                        $result_item[$service.'_price'] = $total;
                        $contract_price += $total;
                    }
                }
            }
            $result_item['contract_price'] = $contract_price;
        }
        $result['list'] = array_values($result['list']);
        $result_num = count($result['list']);
        $start_num = $param['page'] * $param['page_num'];
        $result['list'] = array_slice($result['list'], $start_num, $param['page_num']);
        $result['page_total'] = $result_num;
        return $result;
    }

    /*
     * 根据货号、品名查询货号相关信息
     */
    public function findGoodsInfo($param)
    {
        $condition = array();
        if (isset($param['goods_num']) && $param['goods_num']){
            $condition['goods_num'] = $param['goods_num'];
        }elseif (isset($param['product']) && strlen($param['product']) > 0){
            $condition['product'] = ['like', '%' . $param['product'] . '%'];
        }
        $data = Db::name('admin_goods')->where($condition)->select();
        return $data;
    }

    /**
	 * 通过id修改用户
	 * @param  array   $param  [description]
	 */
	public function updateDataById($param, $id)
	{
		// 不能操作超级管理员
		if ($id == 1) {
			$this->error = '非法操作';
			return false;
		}
		$checkData = $this->get($id);
		if (!$checkData) {
			$this->error = '暂无此数据';
			return false;
		}
		if (empty($param['groups'])) {
			$this->error = '请至少勾选一个用户组';
			return false;
		}
		$this->startTrans();

		try {
			Db::name('admin_access')->where('user_id', $id)->delete();
			foreach ($param['groups'] as $k => $v) {
				$userGroup['user_id'] = $id;
				$userGroup['group_id'] = $v;
				$userGroups[] = $userGroup;
			}
			Db::name('admin_access')->insertAll($userGroups);

			if (!empty($param['password'])) {
				$param['password'] = user_md5($param['password']);
			}
			 $this->allowField(true)->save($param, ['id' => $id]);
			 $this->commit();
			 return true;

		} catch(\Exception $e) {
			$this->rollback();
			$this->error = '编辑失败';
			return false;
		}
	}

	/**
	 * [login 登录]
	 * @AuthorHTL
	 * @DateTime  2017-02-10T22:37:49+0800
	 * @param     [string]                   $u_username [账号]
	 * @param     [string]                   $u_pwd      [密码]
	 * @param     [string]                   $verifyCode [验证码]
	 * @param     Boolean                  	 $isRemember [是否记住密码]
	 * @param     Boolean                    $type       [是否重复登录]
	 * @return    [type]                               [description]
	 */
	public function login($username, $password, $verifyCode = '', $isRemember = false, $type = false)
	{
        if (!$username) {
			$this->error = '帐号不能为空';
			return false;
		}
		if (!$password){
			$this->error = '密码不能为空';
			return false;
		}
        if (config('IDENTIFYING_CODE') && !$type) {
            if (!$verifyCode) {
				$this->error = '验证码不能为空';
				return false;
            }
            $captcha = new HonrayVerify(config('captcha'));
            if (!$captcha->check($verifyCode)) {
				$this->error = '验证码错误';
				return false;
            }
        }

		$map['username'] = $username;
		$userInfo = $this->where($map)->find();
    	if (!$userInfo) {
			$this->error = '帐号不存在';
			return false;
    	}
    	if (user_md5($password) !== $userInfo['password']) {
			$this->error = '密码错误'.user_md5($password);
			return false;
    	}
    	if ($userInfo['status'] === 0) {
			$this->error = '帐号已被禁用';
			return false;
    	}
        // 获取菜单和权限
        $dataList = $this->getMenuAndRule($userInfo['id']);

        if (!$dataList['menusList']) {
			$this->error = '没有权限';
			return false;
        }

        if ($isRemember || $type) {
        	$secret['username'] = $username;
        	$secret['password'] = $password;
            $data['rememberKey'] = encrypt($secret);
        }

        // 保存缓存
        session_start();
        $info['userInfo'] = $userInfo;
        $info['sessionId'] = session_id();
        $authKey = user_md5($userInfo['username'].$userInfo['password'].$info['sessionId']);
        $info['_AUTH_LIST_'] = $dataList['rulesList'];
        $info['authKey'] = $authKey;
        cache('Auth_'.$authKey, null);
        cache('Auth_'.$authKey, $info, config('LOGIN_SESSION_VALID'));
        // 返回信息
        $data['authKey']		= $authKey;
        $data['sessionId']		= $info['sessionId'];
        $data['userInfo']		= $userInfo;
        $data['authList']		= $dataList['rulesList'];
        $data['menusList']		= $dataList['menusList'];
        return $data;
    }

}
