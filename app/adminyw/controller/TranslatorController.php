<?php
/**
 * 字幕组成员
 */
namespace app\adminyw\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;
use app\adminyw\model\TranslatorModel;

class TranslatorController extends AdminBaseController
{
	protected $rule = [

        'name'  => 'require',
        'description'  => 'require',
    ];
    protected $mess = [

        'name.require' => '人员名称必须',
        'description.require' => '人员描述必须',
    ];
    
    /**
     * 列表主页
    */
    public function index(Request $request)
    {
        $search = $request->post();
        $where = $this->searchData($search);

        $type = Db::name('translator')
            ->where($where)
            ->order('id DESC')
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
    public function save_add(Request $request, TranslatorModel $TranslatorModel)
    {
    	if ($this->request->isPost()) {
    		$data = $request->post();
    		$data['create_time'] = date('Y-m-d H:i:s');
	        $result = $TranslatorModel->validate(
	            $this->rule,$this->mess
	        )->save($data);
	        
	        if($result === false) {
	            $this->error($TranslatorModel->getError());
	        }
	
	        $this->success('添加成功',url('adminyw/translator/index'));
    	}
    }
    
    /**
     * 编辑页面
     */
    public function edit(TranslatorModel $TranslatorModel)
    {
        $id = input('id/d');

        if($id == null) {
            $this->error('参数错误');
        }
        $value = $TranslatorModel->where('id',$id)->find();

        $this->assign('value',$value);
        return $this->fetch();
    }
    
    /**
     * 提交编辑
     */
    public function save_edit(Request $request, TranslatorModel $TranslatorModel)
    {
    	if ($this->request->isPost()) {
    		$data = $request->post();

	        $result = $TranslatorModel->validate(
	            $this->rule,$this->mess
	        )->save($data,['id'=>$data['id']]);
	
	        if(false === $result) {
	            $this->error($TranslatorModel->getError());
	        }
	
	        $this->success('修改成功',url('adminyw/translator/index'));
    	}
    }
    
    /**
     * 删除
     */
    public function delete(TranslatorModel $TranslatorModel)
    {
        $id = input('id/d');

        if($id === null) {
            $this->error('参数错误');
        }
		$TranslatorModel->where(array("id"=>$id))->update(array("is_deleted"=>1));
        $this->success('删除成功');
    }
    
    /**
     * 弹框选择标签
     */
    public function select(TranslatorModel $TranslatorModel)
    {
        $ids                 = $this->request->param('ids');
        $name                = $this->request->param('name');
        $selectedIds         = explode(',', $ids);
	
		$where      = ['is_deleted' => 0];
		if(isset($name)){
			$where['name'] = ['like',"%$name%"];
		}
        $tranlist = $TranslatorModel->where($where)->select();
		
        $this->assign('tranlist', $tranlist);
        $this->assign('selectedIds', $selectedIds);
        return $this->fetch();
    }
}
?>