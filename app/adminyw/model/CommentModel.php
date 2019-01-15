<?php
namespace app\adminyw\model;

use think\Model;
use think\Cache;
use app\common\traits\ModelLogTrait;
use think\Db;
class CommentModel extends Model
{
	protected $table = "yw_f_comment";
	//操作记录
    use ModelLogTrait;
    
    /**
     * 返回 当前回复id下的所有回复
     * $father_comment_id:当前回复的顶级评论id
     * $id:当前评论id
     */
    public function get_comment($father_comment_id,&$comment_div,$id){
    	$father_comment = Db::name('f_comment')->where("id = $father_comment_id")->find();
    	$father_comment_name = $father_comment['name'];
    	$comment_id = $father_comment['comment_id'];
    	$now_comment = "";
    	$replydiv = "";
    	//一级评论
    	if($comment_id == 0){
    		$father_comment_create_time = $father_comment['create_time'];
    		$father_comment_content = $father_comment['content'];
    		if($father_comment_id == $id){
    			$now_comment = "now_comment";
    		}
    		$comment_div .= '<div class="form-group '.$now_comment.'">
								<label for="input-name">'.$father_comment_name.'('.$father_comment_create_time.') <a href="javascript:void(0);" onclick="reply(\''.$father_comment_name.'\',\''.$father_comment_id.'\')" class="reply_a">回复</a></label>
								<div>
									'.$father_comment_content.'
								</div>
						</div>';
			$replydiv = "replydiv";
    	}else{
    		$replydiv = "replydiv2";
    	}
    	//回复一级评论的递归
    	$son_comment = Db::name('f_comment')->where("comment_id = $father_comment_id")->select();
    	if(count($son_comment) != 0){
    		foreach($son_comment as $k=>$v){
    			$son_comment_id = $v['id'];
    			$son_comment_name = $v['name'];
    			$son_comment_content = $v['content'];
    			$son_comment_createtime = $v['create_time'];
    			$now_comment = "";
    			if($son_comment_id == $id){
	    			$now_comment = "now_comment";
	    		}
    			$comment_div .= '<div class="form-group '.$replydiv.' '.$now_comment.'">
								<label for="input-name">'.$son_comment_name.' 回复：'.$father_comment_name.'('.$son_comment_createtime.') <a onclick="reply(\''.$son_comment_name.'\',\''.$son_comment_id.'\')"  href="javascript:void(0);" class="reply_a">回复</a></label>
								<div>
									'.$son_comment_content.'
								</div>
							</div>';
				$comment_div = $this->get_comment($son_comment_id,$comment_div,$id);
    		}
    		return $comment_div;
    	}else{
    		return $comment_div;
    	}
    }
    //返回顶级comment_id
    public function get_top_comment_id(&$comment_id){
    	if($comment_id){
    		$comment = Db::name('f_comment')->where("id = $comment_id")->find();
    		if(!empty($comment)){
    			if($comment['comment_id'] != 0){
		    		return $this->get_top_comment_id($comment['comment_id']);
		    	}else{
		    		return $comment_id;
		    	}
    		}
    	}
    }
    
}
?>