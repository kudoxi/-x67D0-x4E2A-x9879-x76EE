<?php
namespace app\adminyw\model;

use think\Model;
use think\Cache;
use app\common\traits\ModelLogTrait;
class TagModel extends Model
{
	protected $table = "yw_tag";
	//操作记录
    use ModelLogTrait;
    
    /*
	 * 获取标签select的option,在前端显示
	 */
	public function tagselect($tag_id=""){
		
		$where = "is_deleted = 0";
		$list = $this->where($where)->select();
		$select = "";
		$options = "";
		foreach($list as $k=>$v){
			if(isset($tag_id) && $v['id'] == $tag_id){
				$select = "selected";
			}
			$options .= "<option value='".$v['id']."' $select >".$v['name']."</option>";
		}
		return $options;
	}
	
}
?>