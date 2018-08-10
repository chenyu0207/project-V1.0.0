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
                        if(!file_exists(ORIGIN_PATH."/Uploads/Excel")){
                            echo ORIGIN_PATH."/Uploads/Excel";
                            mkdir(ORIGIN_PATH."/Uploads/Excel");
                        }die;
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
                            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
                        }
                        $cellName = array('A','B','C','D','E','F','G','H','I','J','K',
                            'L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA',
                            'AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN',
                            'AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
                        $PHPExcel = $objReader->load($filePath);
                        print_r($PHPExcel);die;
                        $sheet = $PHPExcel->getSheet(0);
                        $allRow = $sheet->getHighestRow(); // 取得总行数
                        $allColumn = $sheet->getHighestColumn(); // 取得总列数
                        $this->success('导入成功！');
                    }
                }
            } else {
                $status = 0;
            }
        }


        if (!empty($_FILES)) {
            $config = array(
                'maxSize'    =>    3145728,
                'rootPath'   =>    './Uploads/',
                'savePath'   =>    '/Excel/',
                'saveName'   =>    array('uniqid',''),
                'exts'       =>    array('xlsx','xls'),
                'autoSub'    =>    true,
                'subName'    =>    array('date','Ymd'),
            );
            $upload = new \Think\Upload($config);
            $info = $upload->upload();
            if (!$info) {
                $this->error($upload->getError());
            } else {
                foreach ($info as $file) {
                    $file_name = $config['rootPath'].$file['savepath'].$file['savename'];
                }
            }
            vendor("PHPExcel.PHPExcel");
            $objReader = \PHPExcel_IOFactory::createReader('Excel5');
            $objPHPExcel = $objReader->load($file_name, $encode='utf-8');
            print_r($objPHPExcel);die;
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            $highestColumn = $sheet->getHighestColumn(); // 取得总列数
            //第三行B列起
            // for ($i=3; $i <= $highestRow; $i++) {
            //     $data['username']=  $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();//姓名
            //     $data['idcard']=  $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();//身份证
            //     $data['testdate']=  strtotime($objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue());//日期
            //     $data['course_id']=  $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();//科目
            //     $data['number']= '0';//默认号数
            //     M('personnel')->add($data);
            // }
            $this->success('导入成功！');
        } else {
            $this->error("请选择上传的文件");
        }
    }
}
