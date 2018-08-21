<?php

namespace app\admin\controller;

use think\Controller;
use app\common\controller\Common;
use think\Request;

class Contract extends Common
{
    /**
     * 新增合同
     *
     * @return \think\Response
     */
    public function add()
    {
        $param = $this->param;
        $param['status'] = 1;
        if ($param['stop_time'] < time()){
            $param['status'] = 2;
        }elseif (!isset($param['check_time']) || strlen($param['check_time']) <= 0){
            $param['status'] = 3;
        }
        $userModel = model('Contract');
        $data = $userModel->add($param);
        if (!$data) {
            return resultArray(['error' => $userModel->getError()]);
        }
        return resultArray(['data' => $data]);
    }

    /**
     * 修改合同内容
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function modify()
    {
        $param = $this->param;
        $userModel = model('Contract');
        $data = $userModel->modify($param);
        if (!$data){
            return resultArray(['error' => $userModel->getError()]);
        }
        return resultArray(['data' => $data]);
    }

    /**
     * 显示指定的合同
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function showDetail()
    {
        $param = $this->param;
        $userModel = model('Contract');
        $data = $userModel->showDetail($param);
        if (!$data){
            return resultArray(['error' => $userModel->getError()]);
        }
        return resultArray(['data' => $data]);
    }

    /**
     * 根据时间等参数 筛选合同，最终结果展示
     */
    public function showResult()
    {
        $param = $this->param;
        $userModel = model('Contract');
        $data = $userModel->showResult($param);
        if (!$data){
            return resultArray(['error' => $userModel->getError()]);
        }
        return resultArray(['data' => $data]);
    }

    /*
     * 根据货号、品名查询货号相关信息
     */
    public function findGoodsInfo()
    {
        $param = $this->param;
        $userModel = model('Contract');
        $data = $userModel->findGoodsInfo($param);
        if (!$data){
            return resultArray(['error' => $userModel->getError()]);
        }
        return resultArray(['data' => $data]);
    }
    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
