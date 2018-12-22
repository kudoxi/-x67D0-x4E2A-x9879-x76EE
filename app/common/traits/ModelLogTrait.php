<?php
namespace app\common\traits;
use app\adminyw\model\JiluModel;

trait ModelLogTrait
{
    protected static function init()
    {
        $jiluModel = new JiluModel();

        self::event('after_insert', function ($model) use ($jiluModel) {
            $jiluModel->user_id = cmf_get_current_admin_id();
            $jiluModel->remark = "管理员".cmf_get_current_admin_name()."在表".
                $model->table.'添加了一条数据';
            $jiluModel->c_time = date('Y-m-d H:m:i',time());
            $jiluModel->object = $model->table;
            $jiluModel->object_id = $model->id;
            $jiluModel->save();
        });

        self::event('before_update', function ($model) use ($jiluModel) {
            $key = $model->getPk();
            $before_data = $model->get($model->$key)->toArray();
            $new_data = $model->toArray();
            $changed_before = "";
            $changed_after = "";

            foreach($before_data as $k=>$v) {
                if(!isset($new_data[$k])) {
                    continue;
                }

                if($k == 'password') {
                    continue;
                }

                if($v != $new_data[$k]) {
                    $changed_before = $k . ":" . $v . ",";
                    $changed_after = $k . ":" . $new_data[$k] . ",";
                }
            }

            $jiluModel->user_id = cmf_get_current_admin_id();
            $jiluModel->remark = "管理员".cmf_get_current_admin_name()."在表".
                $model->table.'更新了一条数据';
            $jiluModel->c_time = date('Y-m-d H:m:i',time());
            $jiluModel->object = $model->table;
            $jiluModel->update_before = $changed_before;
            $jiluModel->update_after = $changed_after;
            $jiluModel->object_id = $model->id;
            $jiluModel->save();
        });

        self::event('after_delete', function ($model) use ($jiluModel) {
            $jiluModel->user_id = cmf_get_current_admin_id();
            $jiluModel->remark = "管理员".cmf_get_current_admin_name()."在表".
                $model->table.'删除了一条数据';
            $jiluModel->c_time = date('Y-m-d H:m:i',time());
            $jiluModel->object = $model->table;
            $jiluModel->object_id = $model->id;
            $jiluModel->save();
        });
    }


}