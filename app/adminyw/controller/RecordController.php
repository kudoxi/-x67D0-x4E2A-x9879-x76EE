<?php
namespace app\adminyw\controller;

use cmf\controller\AdminBaseController;
use app\adminyw\model\JiluModel;
use think\Request;

class RecordController extends AdminBaseController
{
    public function index(Request $request, JiluModel $jiluModel)
    {
        $search = $request->post();
        $where = $this->searchData($search);

        $records = $jiluModel->order("id DESC")->where($where)->paginate();

        $this->assign([
            'list' => $records,
            'mudle' => $jiluModel->mudle,
        ]);
        return $this->fetch();
    }

    protected function searchData($search)
    {
        $where = [];
        isset($search['table'])&&!empty($search['table'])? $where['object'] =  $search['table']: '';
        isset($search['s_time'])&&!empty($search['s_time'])? $where['c_time'] = ['>=',$search['s_time']]: '';
        isset($search['e_time'])&&!empty($search['e_time'])? $where['c_time'] = ['<=',$search['e_time']]: '';
        return $where;
    }
}