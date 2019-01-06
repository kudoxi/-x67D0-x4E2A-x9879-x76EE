<?php
namespace app\adminyw\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;
use app\adminyw\model\CountryModel;

class SelectController extends AdminBaseController{
	
	/**
	 * ajax load all country
	 */
	function allcountry(){
		$keyword = isset($_POST['keyword']) ? trim($_POST['keyword']) : "";
		$default_id = isset($_POST['default_id']) ? trim($_POST['default_id']) : "";
		
		$where = array();
		$list = array();
		$where['is_deleted'] = array("eq","0");
		$limit = "";
		if($default_id){
			$where['id'] =  array('eq',$default_id);
		}else{
			if($keyword){
				$where['name'] =  array('like',"%{$keyword}%");
			}else{
				$limit = 5;//default show number
			}
		}
		$list = Db::name('country')->where($where)->limit($limit)->order('listorder ASC,id DESC')->select();
		return array("data"=>$list);
	}	
	/**
	 * ajax load country
	 */
	function country(){
		$keyword = isset($_POST['keyword']) ? trim($_POST['keyword']) : "";
		$default_id = isset($_POST['default_id']) ? trim($_POST['default_id']) : "";
		
		$where = array();
		$list = array();
		$where['is_deleted'] = array("eq","0");
		$where['parent_id'] = array("eq","0");
		$limit = "";
		if($default_id){
			$where['id'] =  array('eq',$default_id);
		}else{
			if($keyword){
				$where['name'] =  array('like',"%{$keyword}%");
			}else{
				$limit = 5;//default show number
			}
		}
		$list = Db::name('country')->where($where)->limit($limit)->order('listorder ASC,id DESC')->select();
		return array("data"=>$list);
	}
}
?>