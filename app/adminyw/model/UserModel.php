<?php
namespace app\common\models;

use think\Model;
use app\common\traits\ModelLogTrait;

class UserModel extends Model
{

    protected $table = "yw_f_user";

    use ModelLogTrait;

    /**
     * 用户所属的公司
     */
    public function company()
    {
        return $this->belongsTo(CompanyModel::class,'company_id');
    }

    /**
     * 用户使用的产品
     */
    public function product()
    {
        return $this->belongsTo(ProductModel::class,'using_product_id');
    }


}