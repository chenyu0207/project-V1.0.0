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
        if (I()) {
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
}
