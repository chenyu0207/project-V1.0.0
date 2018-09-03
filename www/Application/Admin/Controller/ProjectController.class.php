<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: chenyu
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 物流后台管理
 * @author chenyu
 */

class ProjectController extends AdminController
{

    /**
     * 物流后台主页
     * @author chenyu
     */
    public function index()
    {
        if (I('logId')) {
            $this->assign('logId', I('logId'));
            $where['status'] = 1;
            $where['logistics_id'] = I('logId');
            $logistics_info =  M('Logistics')->where($where)->order('id desc')->group('logistics_id')->select();
        } else {
            $logistics_info =  M('Logistics')->where('status = 1')->group('logistics_id')->order('id desc')->select();
        }
        $this->assign('_list', $logistics_info);
        $this->display();
    }

    /**
     * 物流数据新增
     * @author chenyu
     */
    public function add()
    {
        if (I('logistics_id')) {
            $logistics_info =  M('Logistics')->where(['logistics_id = '. I('logistics_id')])->select();
            if ($logistics_info) {
                $this->success('已存在！', U('Project/index'));
            } else {
                $hooks = M('Logistics')->add(I());
                $this->success('新增成功！', U('Project/index'));
            }
        }
        $this->display();
    }

    /**
     * 物流数据修改状态
     * @author chenyu
     */
    public function update()
    {

        $this->assign('id', I('id'));
        $logistics_id =  M('Logistics')->where(['id = '. I('id')])->select();
        $this->assign('logistics_id', $logistics_id[0]['logistics_id']);
        if (I('logistics_id')) {
            $where['logistics_id'] = trim(I('logistics_id'));
            $where['logistics_msg'] = trim(I('logistics_msg'));
            $logistics_info =  M('Logistics')->where($where)->select();
            if ($logistics_info) {
                $this->success('已存在！', U('Project/index'));
            } else {
                $save['status'] = 0;
                M('Logistics')->where('logistics_id = ' . $where['logistics_id'])->save($save);
                $add['logistics_id'] = I('logistics_id');
                $add['logistics_msg'] = I('logistics_msg');
                $add['status'] = I('status');
                $add['time'] = I('time');
                $hooks = M('Logistics')->add($add);
                $this->success('新增成功！', U('Project/index'));
            }
        }
        $this->display();
    }


    /**
     * 文件上传
     * @author chenyu
     */
    public function excel()
    {
        if ($_FILES) {
            $data = $this->excelto();
            if (!empty($data)) {
                $this->getPdf();
                $this->assign('data', $data);
            } else {
                $this->assign('data', false);
            }
            // var_dump($data);
        }
        $this->display();
    }
    /**
     * Excel导入函数
     * @author crx349
     */
    public function excelto()
    {
        if (!empty($_FILES)) {
            // 允许上传的图文件后缀
            $allowedExts = array("xls", "xlsx", "csv");
            $temp = explode(".", $_FILES["import"]["name"]);
            $extension = end($temp);     // 获取文件后缀名
            $filePath = '';
            if (($_FILES["import"]["size"] < 2048000)   // 小于 2 MB
                && in_array($extension, $allowedExts)
            ) {
                if ($_FILES["import"]["error"] > 0) {
                    echo "错误：: " . $_FILES["import"]["error"] . "<br>";
                } else {
                    // 判断当期目录下的 upload 目录是否存在该文件
                    // 如果没有 upload 目录，你需要创建它，upload 目录权限为 777
                    if (file_exists(ORIGIN_PATH."/Uploads/Excel/"  . $_FILES["import"]["name"])) {
                        echo $_FILES["import"]["name"] . " 文件已经存在。 ";
                    } else {
                        //判断是否创建Upload/Excel
                        if (!file_exists(ORIGIN_PATH."/Uploads/Excel")) {
                            mkdir(ORIGIN_PATH."/Uploads/Excel");
                        }
                        //重命名文件名
                        $filename = date('YmdHis' . rand(1000, 9999)) ."." .$extension;
                        // 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
                        move_uploaded_file($_FILES["import"]["tmp_name"], ORIGIN_PATH."/Uploads/Excel/" . $filename);
                        $filePath = ORIGIN_PATH."/Uploads/Excel/" . $filename;
                        //引入PHPExcel类
                        vendor("Excel.PHPExcel");
                        $objPHPExcel = new \PHPExcel();
                        $extension = substr(strrchr($filePath, "."), 1);
                        if ($extension =='csv') {
                            $objReader = \PHPExcel_IOFactory::createReader('CSV');
                        } else {
                            if ($extension =='xlsx') {
                                $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
                            } else {
                                $objReader = \PHPExcel_IOFactory::createReader('Excel5');
                            }
                        }
                        $cellName = array('A','B','C','D','E','F','G','H','I','J','K',
                            'L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA',
                            'AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN',
                            'AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
                        $PHPExcel = $objReader->load($filePath);
                        $sheet = $PHPExcel->getSheet(0);
                        $allRow = $sheet->getHighestRow(); // 取得总行数
                        $allColumn = $sheet->getHighestColumn(); // 取得总列数
                        //从第3行开始插入,第2行是列名
                        for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
                            //获取A列的值
                            $rsv['sender_name'] = $PHPExcel->getActiveSheet()->getCell("A" . $currentRow)->getValue();
                            if (is_object($rsv['sender_name'])) $rsv['sender_name'] = $rsv['sender_name']->__toString();
                            //获取B列的值
                            $rsv['sender_phone'] = $PHPExcel->getActiveSheet()->getCell("B" . $currentRow)->getValue();
                            if (is_object($rsv['sender_phone'])) $rsv['sender_phone'] = $rsv['sender_phone']->__toString();
                            //获取C列的值
                            $rsv['sender_company'] = $PHPExcel->getActiveSheet()->getCell("C" . $currentRow)->getValue();
                            if (is_object($rsv['sender_company'])) $rsv['sender_company'] = $rsv['sender_company']->__toString();
                            //获取D列的值
                            $rsv['sender_address'] = $PHPExcel->getActiveSheet()->getCell("D" . $currentRow)->getValue();
                            if (is_object($rsv['sender_address'])) $rsv['sender_address'] = $rsv['sender_address']->__toString();
                            //获取E列的值
                            $rsv['date'] = $PHPExcel->getActiveSheet()->getCell("E" . $currentRow)->getValue();
                            if (is_object($rsv['date'])) $rsv['date'] = $rsv['date']->__toString();
                            //获取F列的值
                            $rsv['addressee_name'] = $PHPExcel->getActiveSheet()->getCell("F" . $currentRow)->getValue();
                            if (is_object($rsv['addressee_name'])) $rsv['addressee_name'] = $rsv['addressee_name']->__toString();
                            //获取G列的值
                            $rsv['addressee_phone'] = $PHPExcel->getActiveSheet()->getCell("G" . $currentRow)->getValue();
                            if (is_object($rsv['addressee_phone'])) $rsv['addressee_phone'] = $rsv['addressee_phone']->__toString();
                            //获取H列的值
                            $rsv['addressee_address'] = $PHPExcel->getActiveSheet()->getCell("H" . $currentRow)->getValue();
                            if (is_object($rsv['addressee_address'])) $rsv['addressee_address'] = $rsv['addressee_address']->__toString();
                            //获取I列的值
                            $rsv['waybill_number'] = $PHPExcel->getActiveSheet()->getCell("I" . $currentRow)->getValue();
                            if (is_object($rsv['waybill_number'])) $rsv['waybill_number'] = $rsv['waybill_number']->__toString();
                            //获取J列的值
                            $rsv['associated_number'] = $PHPExcel->getActiveSheet()->getCell("J" . $currentRow)->getValue();
                            if (is_object($rsv['associated_number'])) $rsv['associated_number'] = $rsv['associated_number']->__toString();
                            //获取K列的值
                            $rsv['desc'] = $PHPExcel->getActiveSheet()->getCell("K" . $currentRow)->getValue();
                            if (is_object($rsv['desc'])) $rsv['desc'] = $rsv['desc']->__toString();
                            // //获取L列的值
                            // $rsv['sender_name'] = $PHPExcel->getActiveSheet()->getCell("L" . $currentRow)->getValue();
                            // if (is_object($rsv['sender_name'])) $rsv['sender_name'] = $rsv['sender_name']->__toString();
                            // //获取M列的值
                            // $rsv['sender_phone'] = $PHPExcel->getActiveSheet()->getCell("M" . $currentRow)->getValue();
                            // if (is_object($rsv['sender_phone'])) $rsv['sender_phone'] = $rsv['sender_phone']->__toString();
                            // //获取N列的值
                            // $rsv['sender_prov'] = $PHPExcel->getActiveSheet()->getCell("N" . $currentRow)->getValue();
                            // if (is_object($rsv['sender_prov'])) $rsv['sender_prov'] = $rsv['sender_prov']->__toString();
                            // //获取O列的值
                            // $rsv['sender_city'] = $PHPExcel->getActiveSheet()->getCell("O" . $currentRow)->getValue();
                            // if (is_object($rsv['sender_city'])) $rsv['sender_city'] = $rsv['sender_city']->__toString();
                            // //获取P列的值
                            // $rsv['sender_area'] = $PHPExcel->getActiveSheet()->getCell("P" . $currentRow)->getValue();
                            // if (is_object($rsv['sender_area'])) $rsv['sender_area'] = $rsv['sender_area']->__toString();
                            // //获取Q列的值
                            // $rsv['sender_address'] = $PHPExcel->getActiveSheet()->getCell("Q" . $currentRow)->getValue();
                            // if (is_object($rsv['sender_address'])) $rsv['sender_address'] = $rsv['sender_address']->__toString();
                            if ($rsv['sender_name'] != "") {
                                $data[] = $rsv;
                                unset($rsv);
                            }
                        }
                        if ($data != '') {
                            return $data;
                        } else {
                            return false;
                        }
                    }
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    public function getPdf()
    {
        //引入PHPExcel类
        require_once ORIGIN_PATH . '/vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->WriteHTML('<h1>老桔子</h1>', 2);
        $mpdf->AddPage();
        $mpdf->WriteHTML('<h1>大家1!</h1>', 3);
        $mpdf->Output('hello.pdf', 'D');
    }
}
