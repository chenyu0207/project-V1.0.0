<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Logic;
use Think\Db;

class ProjectLogic{

    public function prossingSql($param)
    {
            print_r($param);

    }


    //导入函数
    /*
     *    find(999)  999为数据库的主键id
     *    find(999)  用于获得表的字段
     *    $_POST['modelid']
      */
    //$expTitle=>表的主题，$expCellName=>表头名(列名)，$expTableData=>数据(以下采用数字索引数组)
    public function importExcel($filePath)
    {
        /**
         * Excel导入函数
         *
         */
        //引入PHPExcel类
        vendor("Excel.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $extension = substr(strrchr($filePath,"."),1);
        if( $extension =='csv' )
        {
            $objReader = \PHPExcel_IOFactory::createReader('CSV');
        }
        else
        {
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
        }
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K',
            'L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA',
            'AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN',
            'AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
        $PHPExcel = $objReader->load($filePath);
        $sheet = $PHPExcel->getSheet(0);
        $allRow = $sheet->getHighestRow(); // 取得总行数
        $allColumn = $sheet->getHighestColumn(); // 取得总列数

        //清理临时表
        S('import',null);
        //        $sql = "delete from category_temporary";
        //        $data = self::$pgdb->query($sql);
        //从第3行开始插入,第2行是列名
        for ($currentRow = 3; $currentRow <= $allRow; $currentRow++) {

            //获取A列的值
            $befor = $PHPExcel->getActiveSheet()->getCell("A" . $currentRow)->getValue();
            if(is_object($befor))  $befor= $befor->__toString();
            //获取B列的值
            $after = $PHPExcel->getActiveSheet()->getCell("B" . $currentRow)->getValue();
            if(is_object($after))  $after= $after->__toString();
            //获取C列的值
            $displayname = $PHPExcel->getActiveSheet()->getCell("C" . $currentRow)->getValue();
            if(is_object($displayname))  $displayname= $displayname->__toString();
            //如果没有displayname可能是之前的分类 取以前的
            if($displayname == ''){
                $sql = "select display_name from list_rule inner join path_expression on list_rule.path_expression_id =  path_expression.id
                    where path_expression.name = '{$after}'";
                $arr = self::$pgdb->doSql($sql);
                if(!empty($arr)){
                    $displayname = $arr[0]['display_name'];
                }else{
                    $displayname = '';
                }
            }


            $refer = $PHPExcel->getActiveSheet()->getCell("D" . $currentRow)->getValue();
            if(is_object($refer))  $refer= $refer->__toString();

            if($befor != "" and $after != "" and $displayname != "" and $refer != ""){
                //插入数据
                $rsv['befor'] =$befor;
                $rsv['after'] =$after;
                if($rsv['after'] == ""){
                    $rsv['is_cla'] = "1";//表示未对应
                }else{
                    $rsv['is_cla'] = "0";
                }
                $rsv['refer'] =$refer;
                $rsv['displayname'] =$displayname;
                $data[] = $rsv;
                unset($rsv);
            }


        }
        S('import',$data,3600);
        return $data;
    }

}
