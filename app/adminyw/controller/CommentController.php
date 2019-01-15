<?php
/**
 * 剧种标签
 */
namespace app\adminyw\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;
use app\adminyw\model\CommentModel;

class CommentController extends AdminBaseController
{
	protected $rule = [

        'content'  => 'require',
    ];
    protected $mess = [

        'content.require' => '回复内容必须',
    ];
    /**
     * 列表主页
    */
    public function index(Request $request)
    {
        $search = $request->post();
        $where = $this->searchData($search);

        $comment = Db::name('f_comment c')
        	->field('c.*,p.name as pname')
        	->join('play p','p.id=c.play_id','left')
            ->where($where)
            ->order('c.id DESC')
            ->paginate(20);
        
		
        $this->assign('list',$comment);
        return $this->fetch();
    }
    /**
     * 搜索提交处理
     */
    protected function searchData($search)
    {
        $where = [];
        isset($search['name'])&&!empty($search['name'])? $where['c.name'] = ['like',"%$search[name]%"] : '';
        $where['c.is_deleted'] = 0;
        return $where;
    }
    
    /**
     * 删除
     */
    public function delete(CommentModel $CommentModel)
    {
        $id = input('id/d');

        if($id === null) {
            $this->error('参数错误');
        }
		$CommentModel->where(array("id"=>$id))->update(array("is_deleted"=>1));
        $this->success('删除成功');
    }
    
    /**
     * 回复详情
     */
    public function detail(CommentModel $CommentModel)
    {
        $id = input('id/d');
        if($id){
        	//基本信息
        	$comment = Db::name('f_comment')->where("id = $id")->find();
    		//先根据当前的comment_id返回他的顶级comment_id
    		$top_comment_id = $CommentModel->get_top_comment_id($id);
    		$comment_div = '';
    		$comment_div = $CommentModel->get_comment($top_comment_id,$comment_div,$id);
    		
        }
        $admin = cmf_get_current_admin_name();
		$this->assign('play_id',$comment['play_id']);
		$this->assign('admin',$admin);
		$this->assign('comment_div',$comment_div);
        return $this->fetch();
    }
    /**
     * 回复评论
     */
    public function save_reply(Request $request,CommentModel $CommentModel){
        if($this->request->isPost()){
        	$data = $request->post();
        	$data['create_time'] = date('Y-m-d H:i:s');
        	$data['user_id'] = 0;
        	$data['name'] = '管理员-'.cmf_get_current_admin_name();
        	
        	$result = $CommentModel->validate(
	            $this->rule,$this->mess
	        )->save($data);
	        
			if($result === false) {
	            $this->error($CommentModel->getError());
	        }
	
	        $this->success('回复成功');
        }else{
        	$this->success('缺少参数');
        }
    }
}
?>