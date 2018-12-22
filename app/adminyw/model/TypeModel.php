<?php
namespace app\adminyw\model;

use think\Model;
use think\Cache;
use app\common\traits\ModelLogTrait;
class TypeModel extends Model
{
	protected $table = "yw_type";
	//操作记录
    use ModelLogTrait;
	/*
	 * 获取分类select的option,在前端显示
	 */
	public function typeselect($type_id=""){
		
		$list = $this->where('is_deleted = 0')->select();
		$select = "";
		$options = "";
		foreach($list as $k=>$v){
			if(isset($type_id) && $v['id'] == $type_id){
				$select = "selected";
			}
			$options .= "<option value='"+$v['id']+"' $select >"+$v['name']+"</option>";
		}
		return $options;
	}
}
?>