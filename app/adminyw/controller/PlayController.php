<?php
/**
 * 剧目
 */
namespace app\adminyw\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;
use app\adminyw\model\PlayModel;
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
        	->field('p.*,t.name as tname')
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
        isset($search['description'])&&!empty($search['description'])? $where['p.description'] = ['like',"%$search[description]%"] : '';
        $where['p.is_deleted'] = 0;
        return $where;
    }
    
    /**
     * 添加
     */
    public function add(TypeModel $TypeModel){
    	$selectType = $TypeModel->typeselect("");
    	
    	$this->assign('selectType',$selectType);
    	return $this->fetch();
    }
    
    /**
     * 提交添加
     */
    public function save_add(Request $request, PlayModel $PlayModel)
    {
    	if ($this->request->isPost()) {
    		$data = $request->post();
    		//添加剧目
    		$playdata['name'] = $data['name'];
    		$playdata['type_id'] = $data['type_id'];
    		$playdata['listorder'] = $data['listorder'];
    		$playdata['cover_img'] = $data['cover_img'];
    		$playdata['description'] = $data['description'];
    		$playdata['content'] = $data['content'];
    		$playdata['is_login'] = $data['is_login'];
    		$playdata['is_show'] = $data['is_show'];
    		$playdata['is_top'] = $data['is_top'];
    		$playdata['is_lunbo'] = $data['is_lunbo'];
    		$playdata['create_time'] = date('Y-m-d H:i:s');
    		
	        $result = $PlayModel->validate(
	            $this->rule,$this->mess
	        )->save($playdata);
			
	        if($result === false) {
	            $this->error($PlayModel->getError());
	        }
	        
	        $play_id = $PlayModel->id;
			//关联国家
			$countryids = $data['country'];
			$countryids_arr = explode(",",$countryids);
			$pc_data['play_id'] = $play_id;
			if(!empty($countryids_arr)){
				foreach($countryids_arr as $k=>$v){
					$pc_data['country_id'] = $v;
					Db::name('play_country')->insert($pc_data);
				}
			}
			//关联标签
			$tagids = $data['tag'];
			$tagids_arr = explode(",",$tagids);
			$pt_data['play_id'] = $play_id;
			if(!empty($tagids_arr)){
				foreach($tagids_arr as $k=>$v){
					$pt_data['tag_id'] = $v;
					Db::name('play_tag')->insert($pt_data);
				}
			}
			//关联人员
			$translatorids = $data['translator'];
			$translatorids_arr = explode(",",$translatorids);
			$ptr_data['play_id'] = $play_id;
			if(!empty($translatorids_arr)){
				foreach($translatorids_arr as $k=>$v){
					$ptr_data['translator_id'] = $v;
					Db::name('play_translator')->insert($ptr_data);
				}
			}
			
	        $this->success('添加成功',url('adminyw/play/index'));
    	}
    }
    
    /**
     * 编辑页面
     */
    public function edit(PlayModel $PlayModel,TypeModel $TypeModel)
    {
        $id = input('id/d');

        if($id == null) {
            $this->error('参数错误');
        }
        $value = $PlayModel->where('id',$id)->find();
        $selectType = $TypeModel->typeselect($value['type_id']);
    	$value['content'] =  htmlspecialchars_decode($value['content']);
        //国家
        $pc = Db::name('play_country')->where("play_id = $id")->select();
        $countryids = "";
        $countrynames = "";
        foreach($pc as $k=>$v){
        	$countryids .= $v['country_id'].",";
        	if($v['country_id']){
        		$c = Db::name('country')->where("id = ".$v['country_id'])->find();
        		$countrynames .= $c['name']."-".$c['language'].",";
        	}
        }
        $countryids = trim($countryids,",");
        $countrynames = trim($countrynames,",");
		//标签
		$pt = Db::name('play_tag')->where("play_id = $id")->select();
        $tagids = "";
        $tagnames = "";
        foreach($pt as $k=>$v){
        	$tagids .= $v['tag_id'].",";
        	if($v['tag_id']){
        		$t = Db::name('tag')->where("id = ".$v['tag_id'])->find();
        		$tagnames .= $t['name'].",";
        	}
        }
        $tagids = trim($tagids,",");
        $tagnames = trim($tagnames,",");
        //贡献人员
        $ptr = Db::name('play_translator')->where("play_id = $id")->select();
        $translatorids = "";
        $translatornames = "";
        foreach($ptr as $k=>$v){
        	$translatorids .= $v['translator_id'].",";
        	if($v['translator_id']){
        		$tr = Db::name('translator')->where("id = ".$v['translator_id'])->find();
        		$translatornames .= $tr['name'].",";
        	}
        }
        $translatorids = trim($translatorids,",");
        $translatornames = trim($translatornames,",");


    	$this->assign('selectType',$selectType);
        $this->assign('translatornames',$translatornames);
        $this->assign('translatorids',$translatorids);
        $this->assign('tagnames',$tagnames);
        $this->assign('tagids',$tagids);
        $this->assign('countrynames',$countrynames);
        $this->assign('countryids',$countryids);
        $this->assign('value',$value);
        return $this->fetch();
    }
    
    /**
     * 提交修改
     */
    public function save_edit(Request $request, PlayModel $PlayModel)
    {
    	if ($this->request->isPost()) {
    		$data = $request->post();
    		//添加剧目
    		$playdata['name'] = $data['name'];
    		$playdata['type_id'] = $data['type_id'];
    		$playdata['listorder'] = $data['listorder'];
    		$playdata['cover_img'] = $data['cover_img'];
    		$playdata['description'] = $data['description'];
    		$playdata['content'] = $data['content'];
    		$playdata['is_login'] = $data['is_login'];
    		$playdata['is_show'] = $data['is_show'];
    		$playdata['is_top'] = $data['is_top'];
    		$playdata['is_lunbo'] = $data['is_lunbo'];
    		$playdata['id'] = $data['id'];
	        $result = $PlayModel->validate(
	            $this->rule,$this->mess
	        )->save($playdata,['id'=>$data['id']]);
			
	        if($result === false) {
	            $this->error($PlayModel->getError());
	        }
	        
	        $play_id = $data['id'];
			//关联国家
			Db::name('play_country')->where("play_id = $play_id")->delete();
			
			$countryids = $data['country'];
			$countryids_arr = explode(",",$countryids);
			$pc_data['play_id'] = $play_id;
			if(!empty($countryids_arr)){
				foreach($countryids_arr as $k=>$v){
					$pc_data['country_id'] = $v;
					Db::name('play_country')->insert($pc_data);
				}
			}
			//关联标签
			Db::name('play_tag')->where("play_id = $play_id")->delete();
			
			$tagids = $data['tag'];
			$tagids_arr = explode(",",$tagids);
			$pt_data['play_id'] = $play_id;
			if(!empty($tagids_arr)){
				foreach($tagids_arr as $k=>$v){
					$pt_data['tag_id'] = $v;
					Db::name('play_tag')->insert($pt_data);
				}
			}
			//关联人员
			Db::name('play_translator')->where("play_id = $play_id")->delete();
			
			$translatorids = $data['translator'];
			$translatorids_arr = explode(",",$translatorids);
			$ptr_data['play_id'] = $play_id;
			if(!empty($translatorids_arr)){
				foreach($translatorids_arr as $k=>$v){
					$ptr_data['translator_id'] = $v;
					Db::name('play_translator')->insert($ptr_data);
				}
			}
			
	        $this->success('修改成功',url('adminyw/play/index'));
    	}
    }
    /**
     * 详情页
     */
    public function detail(PlayModel $PlayModel,TypeModel $TypeModel){
    	$id = input('id/d');

        if($id == null) {
            $this->error('参数错误');
        }
        $value = $PlayModel->where('id',$id)->find();
        $typelist = Db::name('type')->where("id = ".$value['type_id'])->find();
        
    	$value['content'] =  htmlspecialchars_decode($value['content']);
        //国家
        $pc = Db::name('play_country')->where("play_id = $id")->select();
        $countrynames = "";
        foreach($pc as $k=>$v){
        	if($v['country_id']){
        		$c = Db::name('country')->where("id = ".$v['country_id'])->find();
        		$countrynames .= $c['name']."-".$c['language'].",";
        	}
        }
        $countrynames = trim($countrynames,",");
		//标签
		$pt = Db::name('play_tag')->where("play_id = $id")->select();
        $tagnames = "";
        foreach($pt as $k=>$v){
        	if($v['tag_id']){
        		$t = Db::name('tag')->where("id = ".$v['tag_id'])->find();
        		$tagnames .= $t['name'].",";
        	}
        }
        $tagnames = trim($tagnames,",");
        //贡献人员
        $ptr = Db::name('play_translator')->where("play_id = $id")->select();
        $translatornames = "";
        foreach($ptr as $k=>$v){
        	if($v['translator_id']){
        		$tr = Db::name('translator')->where("id = ".$v['translator_id'])->find();
        		$translatornames .= $tr['name'].",";
        	}
        }
        $translatornames = trim($translatornames,",");


    	$this->assign('typename',$typelist['name']);
        $this->assign('translatornames',$translatornames);
        $this->assign('tagnames',$tagnames);
        $this->assign('countrynames',$countrynames);
        $this->assign('value',$value);
        return $this->fetch();
    }
    /**
     * 删除剧目
     * 
     */
    public function delete(PlayModel $PlayModel)
    {
        $id = input('id/d');

        if($id === null) {
            $this->error('参数错误');
        }
		$PlayModel->where(array("id"=>$id))->update(array("is_deleted"=>1));
        $this->success('删除成功');
    }
}
?>