<?php
/**
 * 剧目
 */
namespace app\adminyw\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;
use app\adminyw\model\TypeModel;

class PlayController extends AdminBaseController{
	protected $rule = [

        'name'  => 'require',
        'type_id' => 'require',
        'cover_img' => 'require',
        'description' => 'require',
        'content' => 'require',
        
    ];
    protected $mess = [

        'name.require' => '剧目名称必须',
        'type_id.require' => '剧目分类必须',
        'cover_img.require' => '封面图必须',
        'description.require' => '描述必须',
        'content.require' => '内容必须',
    ];
    /**
     * 初始加载
     */
    public function _initialize()
    {
        parent::_initialize();
    }
    
    /**
     * 列表主页
    */
    public function index(Request $request)
    {
        $search = $request->post();
        $where = $this->searchData($search);

        $play = Db::name('play p')
            ->join('type t','t.id=p.type_id','left')
            ->where($where)
            ->order('p.listorder ASC,p.id DESC')
            ->paginate(20);
		
		//获取分类select option 项
		$TypeModel = new TypeModel();
		$selectType = $TypeModel->typeselect(isset($search['type_id'])?$search['type_id']:"");
		
        $this->assign('list',$play);
        $this->assign('selectType',$selectType);
        return $this->fetch();
    }
    /**
     * 搜索提交处理
     */
    protected function searchData($search)
    {
        $where = [];
        isset($search['name'])&&!empty($search['name'])? $where['p.name'] = ['like',"%$search[name]%"] : '';
        isset($search['type_id'])&&!empty($search['type_id'])? $where['p.type_id'] = ['=',"$search[type_id]"] : '';
        isset($search['description'])&&!empty($search['description'])? $where['p.description'] = ['like',"%$search[name]%"] : '';
        $where['p.is_deleted'] = 0;
        return $where;
    }
}
?>