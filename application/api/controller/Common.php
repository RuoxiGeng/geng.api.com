<?php
/**
 * Created by PhpStorm.
 * User: ruoxigeng
 * Date: 2019-06-12
 * Time: 23:10
 */
namespace app\api\controller;

use app\common\lib\exception\ApiException;
use think\Cache;
use think\Controller;
use app\common\lib\Aes;
use app\common\lib\IAuth;
use app\common\lib\Time;

/**
 * API模块公共控制器
 * Class Common
 * @package app\api\controller
 */
class Common extends Controller {

    /**
     * headers信息
     * @var string
     */
    public $headers = '';
    /**
     * 初始化的方法
     */
    public function _initialize() {
        $this->checkRequestAuth();
//        $this->testAes();
    }

    /**
     * 检查每次app请求的数据是否合法
     */
    public function checkRequestAuth() {
        $headers = request()->header();

        //基础参数校验
        if (empty($headers['sign'])) {
            throw new ApiException('sign不存在', 400);
        }
        if (!in_array($headers['app_type'], config('app.apptypes'))) {
            throw new ApiException('app_type不合法', 400);
        }

        if (!IAuth::checkSignPass($headers)) {
            throw new ApiException('授权码sign失败', 401);
        }

        Cache::set($headers['sign'], 1, config('app.app_sign_cache_time'));
        //验证sign唯一性：1:文件 2:mysql 3:redis
        $this->headers = $headers;
    }

    public function testAes() {
        $data = [
            'did' => '12345dg',
            'version' => 1,
            'time' => Time::get13TimeStamp(),
        ];
//        $str = 'xgpEeEGepBgyQKSfx6lZAVy9sopCRCzpQzyf4wHp2qQ=';
        echo IAuth::setSign($data); exit;
        echo (new Aes())->decrypt($str); exit;
    }
}