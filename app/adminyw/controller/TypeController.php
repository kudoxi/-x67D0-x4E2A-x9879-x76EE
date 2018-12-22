<?php
/**
 * 剧种分类
 */
namespace app\adminyw\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;
use app\adminyw\model\TypeModel;

class TypeController extends AdminBaseController
{
	protected $rule = [

        'name'  => 'require',
    ];
    protected $mess = [

        'name.require' => '分类名称必须',
    ];
    
    /**
     * 列表主页
    */
    public function index(Request $request)
    {
        $search = $request->post();
        $where = $this->searchData($search);

        $type = Db::name('type')
            ->where($where)
            ->order('listorder ASC,id DESC')
            ->paginate(20);
		
        $this->assign('list',$type);
        return $this->fetch();
    }
    /**
     * 搜索提交处理
     */
    protected function searchData($search)
    {
        $where = [];
        isset($search['name'])&&!empty($search['name'])? $where['name'] = ['like',"%$search[name]%"] : '';
        $where['is_deleted'] = 0;
        return $where;
    }
    
    /**
     * 添加页面
     */
    public function add()
    {
        return $this->fetch();
    }
    
    /**
     * 提交添加
     */
    public function save_add(Request $request, TypeModel $TypeModel)
    {
    	if ($this->request->isPost()) {
    		$data = $request->post();
	        $result = $TypeModel->validate(
	            $this->rule,$this->mess
	        )->save($data);
	
	        if($result === false) {
	            $this->error($TypeModel->getError());
	        }
	
	        $this->success('添加成功',url('adminyw/type/index'));
    	}
    }
    
    /**
     * 编辑页面
     */
    public function edit(TypeModel $TypeModel)
    {
        $id = input('id/d');

        if($id == null) {
            $this->error('参数错误');
        }
        $value = $TypeModel->where('id',$id)->find();

        $this->assign('value',$value);
        return $this->fetch();
    }
    
    /**
     * 提交编辑
     */
    public function save_edit(Request $request, TypeModel $TypeModel)
    {
    	if ($this->request->isPost()) {
    		$data = $request->post();

	        $result = $TypeModel->validate(
	            $this->rule,$this->mess
	        )->save($data,['id'=>$data['id']]);
	
	        if(false === $result) {
	            $this->error($TypeModel->getError());
	        }
	
	        $this->success('修改成功',url('adminyw/type/index'));
    	}
    }
    
    /**
     * 删除
     */
    public function delete()
    {
        $id = input('id/d');

        if($id === null) {
            $this->error('参数错误');
        }

        ProductModel::destroy($id);
        $this->success('删除成功');
    }
}
?>