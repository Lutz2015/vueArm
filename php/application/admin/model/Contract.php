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

            $service_info = array();
            $service_price = 0;
            foreach ($this->service_list as $service){
                $item = $tmp_param[$service];
                if (strlen($item) <= 0){
                    continue;
                }
                $item = json_decode($item, true);
                foreach ($item as $tmp_item){
                    unset($tmp_item['name']);
                    $tmp_item['number'] = $param['number'];
                    $tmp_item['type'] = $service;
                    $tmp_item['check_time'] = $param['check_time'];
                    $tmp_item['begin_time'] = $param['begin_time'];
                    $tmp_item['end_time']   = $param['end_time'];
                    $tmp_item['status']     = $param['status'];
                    $service_info[] = $tmp_item;
                    $service_price += $tmp_item['amount'] * $tmp_item['unit_price'];
                }
            }
            $param['service_price'] = $service_price;
            Db::name('admin_contract')->insert($param);

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
                    foreach ($target as $tmp_key=>&$tmp_value){
                        if (strlen($tmp_value) <= 0){
                            $tmp_value = 0;
                        }
                    }
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
        $path = dirname(__FILE__);
        $number = $param['number'];
        unset($param['number']);
        if (isset($param['change_status'])){
            $change_status = $param['change_status'];
            unset($param['change_status']);
        }
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
        $contract_data = Db::name('admin_contract')->where($condition)->select();
        if (!$contract_data){
            $this->error = '合同不存在';
            return false;
        }

        $this->startTrans();
        try {
            $flag = 0;
            $tmp = 0;
            $tam = 0;
            if ($basic_list){
                $data = array();
                foreach ($basic_list as $key=>$value){
                    if ($key == 'check_time'){
                        if (strlen($value) <= 0){
                            continue;
                        }
                        $tmp = 1;
                    }
                    if ($key == 'stop_time'){
                        if (strlen($value) <= 0){
                            continue;
                        }
                        //如果修改合同终止时间，并且是过去的时间，合同状态变为5
                        if (intval($value) != 0 && $value <= time()){
                            $data['status'] = 5;
                            $contract_data[0]['status'] = 5;
                            $flag = 1;
                        }
                    }
                    $data[$key] = $value;
                    $contract_data[0][$key] = $value;
                    if ($key == 'begin_time' or $key == 'end_time'){
                        if (strlen($value) <= 0){
                            continue;
                        }
                        $tam = 1;
                    }
                }

                if ($flag != 1 and ($tmp == 1 and $tam == 1)){
                    $data['status'] = 3;
                    $contract_data[0]['status'] = 3;
                }
                Db::name('admin_contract')->where($condition)->update($data);
                if ($tmp == 1){
                    $tmp_data = array();
                    $tmp_data['check_time'] = $data['check_time'];
                    Db::name('admin_contract_service')->where($condition)->update($tmp_data);
                    //Db::name('admin_contract_tax')->where($condition)->update($tmp_data);//发票的时间与合同的check_time无关
                }
                if ($tam == 1){
                    $tmp_data = array();
                    if (isset($data['begin_time'])){
                        $tmp_data['begin_time'] = $data['begin_time'];
                    }
                    if (isset($data['end_time'])){
                        $tmp_data['end_time'] = $data['end_time'];
                    }
                    Db::name('admin_contract_service')->where($condition)->update($tmp_data);
                }
                if ($flag == 1){
                    $data = array();
                    $data['status'] = $contract_data[0]['status'];
                    Db::name('admin_contract_service')->where($condition)->update($data);
                    Db::name('admin_contract_tax')->where($condition)->update($data);
                }elseif ($tam == 1 and $tmp == 1){
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
                $service_price = 0;

                foreach ($service_list as $key=>$value){
                    if (strlen($value) <= 0){
                        continue;
                    }
                    $tmp_service = json_decode($value, true);
                    foreach ($tmp_service as $tmp_item){
                        $tmp_item['number'] = $number;
                        $tmp_item['type'] = $key;
                        $tmp_item['check_time'] = $contract_data[0]['check_time'];
                        $tmp_item['begin_time'] = $contract_data[0]['begin_time'];
                        $tmp_item['end_time']   = $contract_data[0]['end_time'];
                        $tmp_item['status']     = $contract_data[0]['status'];
                        $service_info[] = $tmp_item;
                        $service_price += $tmp_item['amount'] * $tmp_item['unit_price'];
                    }
                }
                Db::name('admin_contract_service')->insertAll($service_info);
                $data = array();
                $data['service_price'] = $service_price;
                Db::name('admin_contract')->where($condition)->update($data);
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
                        unset($target['id']);
                        unset($target['timeText']);
                        foreach ($target as $tmp_key=>&$tmp_value){
                            if (strlen($tmp_value) <= 0){
                                $tmp_value = 0;
                            }
                        }
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
            $this->startTrans();
            try {
                $condition = array();
                $condition['number'] = $param['number'];
                $info = Db::name('admin_contract')->where($condition)->select();
                $service = Db::name('admin_contract_service')->where($condition)->select();
                $bill = Db::name('admin_contract_tax')->where($condition)->select();
                foreach ($info as &$item){
                    foreach ($service as $service_item){
                        if ($service_item['status'] == $item['status']){
                            unset($service_item['id']);
                            unset($service_item['number']);
                            unset($service_item['check_time']);
                            unset($service_item['begin_time']);
                            unset($service_item['end_time']);
                            unset($service_item['status']);
                            $type = $service_item['type'];
                            if ($service_item['type'] == 'hardware'){
                                $service_item['type'] = '硬件';
                            } elseif ($service_item['type'] == 'software'){
                                $service_item['type'] = '软件';
                            } elseif ($service_item['type'] == 'install'){
                                $service_item['type'] = '安装';
                            } elseif ($service_item['type'] == 'serve'){
                                $service_item['type'] = '服务';
                            } elseif ($service_item['type'] == 'other'){
                                $service_item['type'] = '其他';
                            }
                            $item[$type][] = $service_item;
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
        $condition1 = array();
        $condition2 = array();
        $condition3 = array();
        $condition = array();
        if (isset($param['from_time']) && isset($param['to_time']) && $param['from_time'] && $param['to_time']){
            //项目验收时间在 所选时间段内  from_time <= check_time <= to_time
            //服务开始结束时间 与 所选时间 有交集，默认 to_time > from_time
            // begin_time <= to_time && end_time >= from_time
            $from_time = $param['from_time'];
            $to_time = $param['to_time'];
            $condition1['begin_time'] = array('elt', $to_time);// <=
            $condition1['end_time'] = array('egt', $from_time);// >=
            $condition2['check_time'] = array('between', array($from_time, $to_time));
        }
        if (isset($param['status'])){
            $status = $param['status'];
            if ($status == 6){
                //$condition3['status'] = ['like', 'aaa'];
                $condition3['number'] = array('like', '%#');
            }elseif ($status == 0){
                $condition3['status'] = array('in', '3,4,5');
            }else{
                $condition3['status'] = $status;
            }
        }
        if (isset($param['product']) && $param['product']){
            $product = $param['product'];
        }
        if (isset($param['number']) && $param['number']){
            $number = $param['number'];
            $condition3['number'] = $number;
        }

        $result = array();
        $result['list'] = array();
        if (isset($from_time) && isset($to_time)){
            $info = Db::name('admin_contract')->where($condition3)->where(function ($q) use($to_time, $from_time) {
                $q->where("begin_time <= $to_time and end_time >= $from_time")->whereOr('check_time',['<=',$to_time],['>=',$from_time], 'and');
            })->select();
        }else{
            $info = Db::name('admin_contract')->where($condition3)->select();
        }

        $contract_set = array();
        foreach ($info as $key=>$item){
            $number = $item['number'];
            $status = $item['status'];
            if (strlen($item['stop_time']) > 0 && isset($from_time)){
                if (intval($item['stop_time']) < intval($from_time)){
                    continue;
                }
            }
            $result['list'][$number . '_' . $status] = $item;
            $contract_set[] = $number . '_' . $status;
        }
        $res = Db::name('admin_contract_tax')->where($condition2)->where($condition3)->select();
        foreach ($res as $key=>$item){
            $number = $item['number'];
            $status = $item['status'];
            if (strlen($result['list'][$number . '_' . $status]['stop_time']) > 0 && isset($from_time)){
                if ($item['check_time'] > $result['list'][$number . '_' . $status]['stop_time']){
                    continue;
                }
            }
            $result['list'][$number . '_' . $status]['bill'][] = $item;
        }

        //将bill 的信息求和，存到 bill_info
        foreach ($result['list'] as $number_item=>&$info_item){
            if (isset($info_item['bill'])){
                $tmp_info_item = $info_item['bill'];
                if (!in_array($number_item, $contract_set)){
                    $condition4 = array();
                    $condition4['number'] = substr($number_item, 0, strpos($number_item, '_'));
                    $condition4['status'] = substr($number_item, strpos($number_item, '_') + 1);
                    $ret_info = Db::name('admin_contract')->where($condition4)->select();
                    $info_item = $ret_info[0];
                }
                $info_item['bill'] = $tmp_info_item;
            }
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
            $condition3['cate'] = $product;
        }

        $contract_list = array();
        if (isset($from_time) && isset($to_time)){
            $ret = Db::name('admin_contract_service')->where($condition3)->where(function ($q) use($to_time, $from_time) {
                $q->where("begin_time <= $to_time and end_time >= $from_time")->whereOr('check_time',['<=',$to_time],['>=',$from_time], 'and');
            })->select();
        }else{
            $ret = Db::name('admin_contract_service')->where($condition3)->select();
        }
        foreach ($ret as $key=>$value){
            $type = $value['type'];
            $number = $value['number'];
            $status = $value['status'];
            if (!in_array($number . '_' . $status, $contract_set)){
                continue;
            }
            if (strlen($result['list'][$number . '_' . $status]['stop_time']) > 0 && isset($from_time)){
                if ($value['check_time'] > $result['list'][$number . '_' . $status]['stop_time']){
                    continue;
                }
                if ($value['begin_time'] > $result['list'][$number . '_' . $status]['stop_time']){
                    continue;
                }
                if ($from_time > $result['list'][$number . '_' . $status]['stop_time']){
                    continue;
                }
            }
            $result['list'][$number . '_' . $status][$type][] = $value;
            if (!in_array($number . '_' . $status, $contract_list)){
                $contract_list[] = $number . '_' . $status;
            }
        }
        //如果有筛选产品线，某个合同没有产品线，就删除
        if (isset($product) && $product){
            foreach ($result['list'] as $key=>$result_item){
                if (!in_array($key, $contract_list)){
                    unset($result['list'][$key]);
                }
            }
        }
        //计算各个服务的价格
        $service_list = array('hardware', 'software', 'install', 'serve', 'other');
        foreach ($result['list'] as $key=>&$result_item){
            if ($result_item['service_price'] == 0){
                $result_item['service_price'] = 1000000;
            }
            $contract_price = 0;//某个合同的价格合计
            $result_item['tax_price'] = 0;//当期税金
            $result_item['all_tax_price'] = $result_item['total_price'] / (1+$result_item['tax_rate']) * $result_item['tax_rate'];//分摊税金
            foreach ($service_list as $service){
                $total = 0;
                $total_all = 0;
                if ($service != 'serve'){
                    if (isset($result_item[$service])){
                        if (!isset($from_time) or !isset($to_time)){
                            $flag = 1;
                        }else{
                            if (strlen($result_item['stop_time']) > 0){
                                if ($result_item['check_time'] >= $from_time && $result_item['check_time'] <= $result_item['stop_time'] && $result_item['check_time'] <= $to_time){
                                    $flag = 1;
                                }else{
                                    $flag = 0;
                                }
                            }elseif ($result_item['check_time'] >= $from_time && $result_item['check_time'] <= $to_time){
                                $flag = 1;
                            }else{
                                $flag = 0;
                            }
                        }//根据from_time和to_time判断是否是 当期
                        $tax_total_price = $result_item['total_price'] - $result_item['all_tax_price'];//合同分摊总额基数
                        //当期分摊额
                        foreach ($result_item[$service] as $tmp){
                            $total += $tmp['unit_price'] * $tmp['amount'] * $flag;
                        }
                        $result_item[$service.'_price'] = round($tax_total_price * $total * 100 / $result_item['service_price'] / 100, 2);
                        //分摊总额
                        foreach ($result_item[$service] as $tmp){
                            $total_all += $tmp['unit_price'] * $tmp['amount'];
                        }
                        $result_item['all_'.$service.'_price'] = round($tax_total_price * $total_all * 100 / $result_item['service_price'] / 100, 2);
                        $contract_price += round($tax_total_price * $total * 100 / $result_item['service_price'] / 100, 2);
                    }
                }else {
                    if (isset($result_item[$service])){
                        foreach ($result_item[$service] as $tmp){
                            if (isset($from_time) && isset($to_time)){
                                $from_time_2 = $from_time;
                                $to_time_2 = $to_time;
                                if (strlen($result_item['stop_time']) > 0 && $result_item['stop_time'] < $to_time_2){
                                    $to_time_2 = $result_item['stop_time'];
                                }
                                if ($from_time_2 < $tmp['begin_time']){
                                    $from_time_2 = $tmp['begin_time'];
                                }
                                if ($to_time_2 > $tmp['end_time']){
                                    $to_time_2 = $tmp['end_time'];
                                }
                                if ($from_time_2 > $to_time_2){
                                    $g_flag = 0;
                                }else{
                                    $g_flag = 1;
                                }
                                //当期分摊额
                                if ($g_flag == 0){
                                }else{
                                    $total += $tmp['unit_price'] * $tmp['amount'] * ($to_time_2 - $from_time_2) / ($tmp['end_time'] - $tmp['begin_time']);
                                }
                                //分摊总额
                                $total_all += $tmp['unit_price'] * $tmp['amount'];
                            }else{
                                $total += $tmp['unit_price'] * $tmp['amount'];
                                $total_all += $tmp['unit_price'] * $tmp['amount'];
                            }
                        }
                        $tax_total_price = $result_item['total_price'] - $result_item['all_tax_price'];
                        $result_item[$service.'_price'] = round($tax_total_price * $total * 100 / $result_item['service_price'] / 100, 2);
                        $result_item['all_'.$service.'_price'] = round($tax_total_price * $total_all * 100 / $result_item['service_price'] / 100, 2);
                        $contract_price += round($tax_total_price * $total * 100 / $result_item['service_price'] / 100, 2);
                    }
                }
            }
            $result_item['contract_price'] = $contract_price;
            $result_item['tax_price'] = $contract_price * $result_item['tax_rate'];
        }
        $result['list'] = array_values($result['list']);
        $result_num = count($result['list']);
        if (isset($param['page']) && isset($param['page_num'])){
            $start_num = $param['page'] * $param['page_num'];
            $result['list'] = array_slice($result['list'], $start_num, $param['page_num']);
        }
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

    /*
     * 添加货号相关信息
     */
    public function addGoodsInfo($param)
    {
        $condition = array();
        $condition['goods_num'] = $param['goods_num'];
        $ret = Db::name('admin_goods')->where($condition)->select();
        if ($ret){
            $this->error = '货号不能重复';
            return false;
        }
        $condition2 = array();
        $condition2['product'] = $param['product'];
        $ret = Db::name('admin_goods')->where($condition2)->select();
        if ($ret){
            $this->error = '品名不能重复';
            return false;
        }
        $data = Db::name('admin_goods')->insert($param);
        return $data;
    }
    /*
     * 修改货号信息
     */
    public function modifyGoodsInfo($param)
    {
        $this->startTrans();
        try {
            $condition = array();
            $condition['goods_num'] = $param['goods_num'];
            unset($param['goods_num']);
            $data = Db::name('admin_goods')->where($condition)->update($param);
            $ret = Db::name('admin_contract_service')->where($condition)->update($param);
            
            //货号单价改变后，去修改涉及到的合同的service_price
            $contract_array = array();
            $contract_info = Db::name('admin_contract_service')->field('number')->where($condition)->select();
            foreach ($contract_info as $contract){
                if (!in_array($contract['number'], $contract_array)){
                    $contract_array[] = $contract['number'];
                }
            }
            $contract_list = implode(',', $contract_array);
            $condition2 = array();
            $condition2['number'] = array('in', $contract_list);
            $ret = Db::name('admin_contract_service')->where($condition2)->select();
            $contract_service_price = array();
            foreach ($ret as $item){
                if (array_key_exists($item['number'], $contract_service_price)){
                    $contract_service_price[$item['number']] += $item['amount'] * $item['unit_price'];
                } else{
                    $contract_service_price[$item['number']] = $item['amount'] * $item['unit_price'];
                }
            }
            $condition3 = array();
            foreach ($contract_service_price as $number=>$service_price){
                $condition3 = array();
                $condition3['number'] = $number;
                $data = array();
                $data['service_price'] = $service_price;
                $ret = Db::name('admin_contract')->where($condition3)->update($data);
            }
            $this->commit();
            return true;
        }catch(\Exception $e) {
            $this->rollback();
            $this->error = '修改失败';
            return false;
        }
    }
    /*
     * 删除货号信息
     */
    public function deleteGoodsInfo($param)
    {
        $this->startTrans();
        try {
            $condition = array();
            $condition['goods_num'] = $param['goods_num'];
            unset($param['goods_num']);
            $data = Db::name('admin_goods')->where($condition)->delete();
            //$ret = Db::name('admin_contract_service')->where($condition)->update($param);
            $this->commit();
            return true;
        }catch(\Exception $e) {
            $this->rollback();
            $this->error = '删除失败';
            return false;
        }
    }

    /*
     *  合同审核
     */
    public function auditContract($param)
    {
        $condition = array();
        $number_array = array();
        $numbers = json_decode($param['numbers'], TRUE);
        foreach ($numbers as $number){
            $number_array[] = $number['number'];
        }
        $condition['status'] = 3;
        $data = array();
        $data['status'] = 4;
        $this->startTrans();
        try {
            $ret = Db::name('admin_contract')->whereIn('number', $number_array)->where($condition)->update($data);
            $ret = Db::name('admin_contract_service')->whereIn('number', $number_array)->where($condition)->update($data);
            $ret = Db::name('admin_contract_tax')->whereIn('number', $number_array)->where($condition)->update($data);
            $this->commit();
            return TRUE;
        }catch(\Exception $e) {
            $this->rollback();
            $this->error = '审核失败';
            return false;
        }
    }
    /*
     *  撤销合同审核
     */
    public function concelContract($param)
    {
        if ($param['username'] != 'admin'){
            return false;
        }
        $condition = array();
        $condition['number'] = $param['number'];
        $condition['status'] = 4;
        $data = array();
        $data['status'] = 3;
        $this->startTrans();
        try {
            $ret = Db::name('admin_contract')->where($condition)->update($data);
            $ret = Db::name('admin_contract_service')->where($condition)->update($data);
            $ret = Db::name('admin_contract_tax')->where($condition)->update($data);
            $this->commit();
            return TRUE;
        }catch(\Exception $e) {
            $this->rollback();
            $this->error = '撤销审核失败';
            return false;
        }
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
