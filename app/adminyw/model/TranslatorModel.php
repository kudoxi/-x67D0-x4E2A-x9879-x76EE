<?php
namespace app\adminyw\model;

use think\Model;
use think\Cache;
use app\common\traits\ModelLogTrait;
class TranslatorModel extends Model
{
	protected $table = "yw_translator";
	//操作记录
    use ModelLogTrait;
    
    /*
	 * 获取字幕组成员select的option,在前端显示
	 */
	public function translatorselect($translator_id=""){
		
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