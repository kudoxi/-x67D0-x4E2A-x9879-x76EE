<?php
namespace app\adminyw\model;

use think\Model;
use think\Cache;
use app\common\traits\ModelLogTrait;
use tree\Tree;
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
			$where .= " and parent_id = $pid";
		}
		$list = $this->where($where)->select();
		$select = "";
		$options = "";
		foreach($list as $k=>$v){
			if(isset($country_id) && $v['id'] == $country_id){
				$select = "selected";
			}
			$options .= "<option value='".$v['id']."' $select >".$v['name']."</option>";
		}
		return $options;
	}
	/**
	 * @param int|array $currentIds
     * @param string $tpl
     * @return string
	 */
	public function adminCategoryTableTree($currentIds = 0, $tpl = '')
    {
        $where = ['is_deleted' => 0];
        $countries = $this->order("listorder ASC")->where($where)->select()->toArray();

        $tree       = new Tree();
        $tree->icon = ['&nbsp;&nbsp;│', '&nbsp;&nbsp;├─', '&nbsp;&nbsp;└─'];
        $tree->nbsp = '&nbsp;&nbsp;';

        if (!is_array($currentIds)) {
            $currentIds = [$currentIds];
        }

        $newCounries = [];
        foreach ($countries as $item) {
            $item['checked'] = in_array($item['id'], $currentIds) ? "checked" : "";
            $item['url']     = cmf_url('adminyw/Country/edit', ['id' => $item['id']]);
            array_push($newCounries, $item);
        }

        $tree->init($newCounries);

        if (empty($tpl)) {
            $tpl = "<tr>
                        <td>\$id</td>
                        <td>\$spacer <a href='\$url' target='_blank'>\$name-\$language</a></td>
                        <td>\$remark</td>
                    </tr>";
        }
        $treeStr = $tree->getTree(0, $tpl);

        return $treeStr;
    }
}
?>