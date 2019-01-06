<?php
namespace app\adminyw\model;

use think\Model;
use think\Cache;
use app\common\traits\ModelLogTrait;
class PlayModel extends Model
{
	protected $table = "yw_play";
	//操作记录
    use ModelLogTrait;
    
}
?>