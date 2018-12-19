<?php
namespace app\admin_xiaorui\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        //'company_id' => 'gt:0',暂时不用
        //'using_product_id' => 'gt:0',
        'phone' => 'length:11',
        'email' => 'email',
        'password' => 'require|confirm:repassword|length:6',
        'repassword' => 'require',
    ];

    protected $msg = [
        //'company_id.gt' => '必须选择公司',
        //'using_product_id.gt' => '必须选择公司',
        'phone.length' => '手机格式不对',
        'email.email' => '邮箱格式不对',
        'password.require' => '请填写密码',
        'password.length' => '密码至少为6位',
        'password.confirm' => '确认密码不正确',
        'repassword.require' => '请填写确认密码',
    ];

    protected $scene = [
        'edit'  =>  ['phone','email','password'=>'confirm'],
    ];
}