<?php
namespace app\adminyw\model;

use think\Model;

class JiluModel extends Model
{
    protected $table = "yw_jilu";

    //各个表代表的模块
    public $mudle = [
        'yw_type' => '剧种分类',
        'yw_country' => '国家语种',
        'yw_play' => '剧目',
        'yw_tag' => '标签',
        'yw_translator'=>'翻译人员',
        'yw_f_user'=>'前台用户',
        'yw_f_comment'=>'评论',
        'yw_ask_video'=>'求片',
        'yw_click_record'=>'点击量记录',
        
    ];


}