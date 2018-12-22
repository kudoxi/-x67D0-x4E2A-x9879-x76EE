<?php
namespace app\adminyw\model;

use think\Model;
use think\Cache;
use app\common\traits\ModelLogTrait;
class CountryModel extends Model
{
	protected $table = "yw_country";
	//操作记录
    use ModelLogTrait;
    
    /*
	 * 获取语种select的option,在前端显示
	 */
	public function countryselect($country_id="",$pid=""){
		
		$where = "is_deleted = 0";
		if(isset($pid)){
			$where .= " and pid = $pid";
		}
		$list = $this->where($where)->select();
		$select = "";
		$options = "";
		foreach($list as $k=>$v){
			if(isset($country_id) && $v['id'] == $country_id){
				$select = "selected";
			}
			$options .= "<option value='"+$v['id']+"' $select >"+$v['name']+"</option>";
		}
		return $options;
	}
	
}
?>