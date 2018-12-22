<?php
namespace app\adminyw\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;
use app\adminyw\model\CountryModel;

class CountryController extends AdminBaseController{
	protected $rule = [

        'name'  => 'require',
        'language'  => 'require',
    ];
    protected $mess = [

        'name.require' => '国家名称必须',
        'language.require' => '语种名称必须',
    ];
    
    /**
     * 列表主页
    */
    public function index(Request $request)
    {
        $search = $request->post();
        $where = $this->searchData($search);

        $country = Db::name('country c1')
        	->field('c1.*,c2.name as pname')
        	->join('country c2','c2.id=c1.pid','left')
            ->where($where)
            ->order('c1.listorder ASC,c1.id DESC')
            ->paginate(20);
        //dump(Db::name('country c1')->getLastSql());
            
        $this->assign('list',$country);
        return $this->fetch();
    }
    
    /**
     * 搜索提交处理
     */
    protected function searchData($search)
    {
        $where = [];
        isset($search['name'])&&!empty($search['name'])? $where['c1.name'] = ['like',"%$search[name]%"] : '';
        isset($search['pid'])&&!empty($search['pid'])? $where['c1.pid'] = ['=',"$search[pid]"] : '';
        isset($search['language'])&&!empty($search['language'])? $where['c1.language'] = ['like',"%$search[language]%"] : '';
        $where['c1.is_deleted'] = 0;
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
    public function save_add(Request $request, CountryModel $CountryModel)
    {
    	if ($this->request->isPost()) {
    		$data = $request->post();
	        $result = $CountryModel->validate(
	            $this->rule,$this->mess
	        )->save($data);
	
	        if($result === false) {
	            $this->error($CountryModel->getError());
	        }
	
	        $this->success('添加成功',url('adminyw/country/index'));
    	}
    }
    
    /**
     * 编辑页面
     */
    public function edit(CountryModel $CountryModel)
    {
        $id = input('id/d');

        if($id == null) {
            $this->error('参数错误');
        }
        $value = $CountryModel->where('id',$id)->find();

        $this->assign('value',$value);
        return $this->fetch();
    }
    
    /**
     * 提交编辑
     */
    public function save_edit(Request $request, CountryModel $CountryModel)
    {
    	if ($this->request->isPost()) {
    		$data = $request->post();

	        $result = $CountryModel->validate(
	            $this->rule,$this->mess
	        )->save($data,['id'=>$data['id']]);
	
	        if(false === $result) {
	            $this->error($CountryModel->getError());
	        }
	
	        $this->success('修改成功',url('adminyw/country/index'));
    	}
    }
    
    
	/**
     * 删除
     */
    public function delete(CountryModel $CountryModel)
    {
        $id = input('id/d');

        if($id === null) {
            $this->error('参数错误');
        }
		$CountryModel->where(array("id"=>$id))->update(array("is_deleted"=>1));
        $this->success('删除成功');
    }
}
?>