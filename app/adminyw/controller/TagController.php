<?php
/**
 * 剧种标签
 */
namespace app\adminyw\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;
use app\adminyw\model\TagModel;

class TagController extends AdminBaseController
{
	protected $rule = [

        'name'  => 'require',
    ];
    protected $mess = [

        'name.require' => '标签名称必须',
    ];
    
    /**
     * 列表主页
    */
    public function index(Request $request)
    {
        $search = $request->post();
        $where = $this->searchData($search);

        $type = Db::name('tag')
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
    public function save_add(Request $request, TagModel $TagModel)
    {
    	if ($this->request->isPost()) {
    		$data = $request->post();
	        $result = $TagModel->validate(
	            $this->rule,$this->mess
	        )->save($data);
	
	        if($result === false) {
	            $this->error($TagModel->getError());
	        }
	
	        $this->success('添加成功',url('adminyw/tag/index'));
    	}
    }
    
    /**
     * 编辑页面
     */
    public function edit(TagModel $TagModel)
    {
        $id = input('id/d');

        if($id == null) {
            $this->error('参数错误');
        }
        $value = $TagModel->where('id',$id)->find();

        $this->assign('value',$value);
        return $this->fetch();
    }
    
    /**
     * 提交编辑
     */
    public function save_edit(Request $request, TagModel $TagModel)
    {
    	if ($this->request->isPost()) {
    		$data = $request->post();

	        $result = $TagModel->validate(
	            $this->rule,$this->mess
	        )->save($data,['id'=>$data['id']]);
	
	        if(false === $result) {
	            $this->error($TagModel->getError());
	        }
	
	        $this->success('修改成功',url('adminyw/tag/index'));
    	}
    }
    
    /**
     * 删除
     */
    public function delete(TagModel $TagModel)
    {
        $id = input('id/d');

        if($id === null) {
            $this->error('参数错误');
        }
		$TagModel->where(array("id"=>$id))->update(array("is_deleted"=>1));
        $this->success('删除成功');
    }
    
    /**
     * 弹框选择标签
     */
    public function select(TagModel $TagModel)
    {
        $ids                 = $this->request->param('ids');
        $name                = $this->request->param('name');
        $selectedIds         = explode(',', $ids);
	
		$where      = ['is_deleted' => 0];
		if(isset($name)){
			$where['name'] = ['like',"%$name%"];
		}
        $taglist = $TagModel->where($where)->select();
		
        $this->assign('taglist', $taglist);
        $this->assign('selectedIds', $selectedIds);
        return $this->fetch();
    }
}
?>