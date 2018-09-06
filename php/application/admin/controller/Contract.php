<?php

namespace app\admin\controller;

use think\Controller;
use app\common\controller\Common;
use think\Request;
use PHPExcel_IOFactory;
use PHPExcel;

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
        //is_type 为1,表示暂存
        if ($param['is_type'] == 1){
            $param['status'] = 1;
        }else{
            $param['status'] = 3;//默认状态为未审核
        }
        unset($param['is_type']);
        if (!isset($param['check_time']) || strlen($param['check_time']) <= 0){
            $param['status'] = 2;//如果没填写项目验收时间，状态为未验收
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

        if ($param['is_excel'] == 1){
            vendor("PHPExcel.Classes.PHPExcel");
            vendor("PHPExcel.Classes.PHPExcel.Writer.IWriter");
            vendor("PHPExcel.Classes.PHPExcel.Writer.Abstract");
            vendor("PHPExcel.Classes.PHPExcel.Writer.Excel5");
            vendor("PHPExcel.Classes.PHPExcel.Writer.Excel2007");
            vendor("PHPExcel.Classes.PHPExcel.IOFactory");
            /* 浏览器本地保存
            $path = dirname(__FILE__);
            $PHPExcel = new \PHPExcel();
            $PHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'ID编号')->setCellValue('B1', '用户名');
            $PHPExcel->getActiveSheet()->settitle('信息'); //设置sheet的名称
            $PHPExcel->setActiveSheetIndex(0); //设置sheet的起始位置
            $PHPWriter = \PHPExcel_IOFactory::createWriter( $PHPExcel,"Excel2007");
            ob_end_clean();
            header('Content-Disposition: attachment;filename="test.xlsx"');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Cache-Control: max-age=0');
            $PHPWriter->save('php://output');
             */
        
            $path = dirname(__FILE__);
            $PHPExcel = new \PHPExcel();
            $PHPSheet = $PHPExcel->getActiveSheet();
            $PHPSheet->setTitle('contract_result');
            //设置表头信息
            $PHPExcel->getActiveSheet()->mergeCells('A1:A2');
            $PHPExcel->getActiveSheet()->mergeCells('B1:B2');
            $PHPExcel->getActiveSheet()->mergeCells('C1:C2');
            $PHPExcel->getActiveSheet()->mergeCells('D1:D2');
            $PHPExcel->getActiveSheet()->mergeCells('E1:E2');
            $PHPExcel->getActiveSheet()->mergeCells('F1:F2');
            $PHPExcel->getActiveSheet()->mergeCells('G1:M1');
            $PHPExcel->getActiveSheet()->mergeCells('N1:T1');
            $PHPExcel->getActiveSheet()->mergeCells('U1:W1');
            $PHPExcel->getActiveSheet()->mergeCells('X1:Z1');
            $PHPExcel->getActiveSheet()->mergeCells('AA1:AC1');
            $PHPExcel->setActiveSheetIndex(0) 
                ->setCellValue('A1', '合同年度') ->setCellValue('B1', '合同编号')
                ->setCellValue('C1', '合同类别') ->setCellValue('D1', '合同甲方')
                ->setCellValue('E1', '第三方')   ->setCellValue('F1', '酒店名字')
                ->setCellValue('G1', '分摊价')   ->setCellValue('G2', '合同总价')
                ->setCellValue('H2', '税金')     ->setCellValue('I2', '硬件')
                ->setCellValue('J2', '软件')     ->setCellValue('K2', '安装')
                ->setCellValue('L2', '服务')     ->setCellValue('M2', '其他')
                ->setCellValue('N1', '收入确认') ->setCellValue('N2', '硬件')
                ->setCellValue('O2', '软件')     ->setCellValue('P2', '安装')
                ->setCellValue('Q2', '服务')     ->setCellValue('R2', '其他')
                ->setCellValue('S2', '合计')     ->setCellValue('T2', '税金')
                ->setCellValue('U1', '发票开具(价税总额)') 
                ->setCellValue('U2', '17%')      ->setCellValue('V2', '6%')
                ->setCellValue('W2', '小计')
                ->setCellValue('X1', '发票(不含税额)')
                ->setCellValue('X2', '17%')      ->setCellValue('Y2', '6%')
                ->setCellValue('Z2', '小计')
                ->setCellValue('AA1', '发票(税额)')
                ->setCellValue('AA2', '17%')      ->setCellValue('AB2', '6%')
                ->setCellValue('AC2', '小计');

            foreach ($data['list'] as $key=>$item){
                $key = $key + 3;
                $PHPExcel->getActiveSheet()->setCellValue('A' . $key, date('Y', $item['year']));
                $PHPExcel->getActiveSheet()->setCellValue('B' . $key, $item['number']);
                $PHPExcel->getActiveSheet()->setCellValue('C' . $key, $item['category']);
                $PHPExcel->getActiveSheet()->setCellValue('D' . $key, $item['party_a']);
                $PHPExcel->getActiveSheet()->setCellValue('E' . $key, $item['thirdparty']);
                $PHPExcel->getActiveSheet()->setCellValue('F' . $key, $item['hotel']);
                $PHPExcel->getActiveSheet()->setCellValue('G' . $key, $item['total_price']);
                $PHPExcel->getActiveSheet()->setCellValue('H' . $key, $item['all_tax_price']);
                $PHPExcel->getActiveSheet()->setCellValue('I' . $key, $item['all_hardware_price']);
                $PHPExcel->getActiveSheet()->setCellValue('J' . $key, $item['all_software_price']);
                $PHPExcel->getActiveSheet()->setCellValue('K' . $key, $item['all_install_price']);
                $PHPExcel->getActiveSheet()->setCellValue('L' . $key, $item['all_serve_price']);
                $PHPExcel->getActiveSheet()->setCellValue('M' . $key, $item['all_other_price']);
                $PHPExcel->getActiveSheet()->setCellValue('N' . $key, $item['hardware_price']);
                $PHPExcel->getActiveSheet()->setCellValue('O' . $key, $item['software_price']);
                $PHPExcel->getActiveSheet()->setCellValue('P' . $key, $item['install_price']);
                $PHPExcel->getActiveSheet()->setCellValue('Q' . $key, $item['serve_price']);
                $PHPExcel->getActiveSheet()->setCellValue('R' . $key, $item['other_price']);
                $PHPExcel->getActiveSheet()->setCellValue('S' . $key, $item['contract_price']);
                $PHPExcel->getActiveSheet()->setCellValue('T' . $key, $item['tax_price']);
                $PHPExcel->getActiveSheet()->setCellValue('U' . $key, $item['bill_info']['billAll17']);
                $PHPExcel->getActiveSheet()->setCellValue('V' . $key, $item['bill_info']['billAll06']);
                $PHPExcel->getActiveSheet()->setCellValue('W' . $key, $item['bill_info']['billAll0']);
                $PHPExcel->getActiveSheet()->setCellValue('X' . $key, $item['bill_info']['billNoTax17']);
                $PHPExcel->getActiveSheet()->setCellValue('Y' . $key, $item['bill_info']['billNoTax06']);
                $PHPExcel->getActiveSheet()->setCellValue('Z' . $key, $item['bill_info']['billNoTax0']);
                $PHPExcel->getActiveSheet()->setCellValue('AA' . $key, $item['bill_info']['billTax17']);
                $PHPExcel->getActiveSheet()->setCellValue('AB' . $key, $item['bill_info']['billTax06']);
                $PHPExcel->getActiveSheet()->setCellValue('AC' . $key, $item['bill_info']['billTax0']);

            }
            $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
            $name = session_create_id();
            $PHPWriter->save($path."/../data/$name.xlsx");
            $data['url'] = "http://finance.aittgroup.com/php/application/admin/data/$name.xlsx";
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
    /*
     * 添加货号相关信息
     */
    public function addGoodsInfo()
    {
        $param = $this->param;
        $userModel = model('Contract');
        $data = $userModel->addGoodsInfo($param);
        if (!$data){
            return resultArray(['error' => $userModel->getError()]);
        }
        return resultArray(['data' => $data]);
    }
    /*
     * 添加货号相关信息
     */
    public function modifyGoodsInfo()
    {
        $param = $this->param;
        $userModel = model('Contract');
        $data = $userModel->modifyGoodsInfo($param);
        if (!$data){
            return resultArray(['error' => $userModel->getError()]);
        }
        return resultArray(['data' => $data]);
    }

    /*
     * 删除产品信息
     */
    public function deleteGoodsInfo()
    {
        $param = $this->param;
        $userModel = model('Contract');
        $data = $userModel->deleteGoodsInfo($param);
        if (!$data){
            return resultArray(['error' => $userModel->getError()]);
        }
        return resultArray(['data' => $data]);
    }
    /*
     * 合同审核
     */
    public function auditContract()
    {
        $param = $this->param;
        $userModel = model('Contract');
        $data = $userModel->auditContract($param);
        if (!$data){
            return resultArray(['error' => $userModel->getError()]);
        }
        return resultArray(['data' => $data]);
    }
    /*
     * 撤销审核
     */
    public function cancelContract()
    {
        $param = $this->param;
        $userModel = model('Contract');
        $data = $userModel->concelContract($param);
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
