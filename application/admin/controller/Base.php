<?php
namespace app\admin\controller;

use think\Controller;

/**
 * 后台基础类库
 * Class Base
 * @package app\admin\controller
 */

class Base extends Controller
{
    public $page = '';

    public $size = '';
    /**
     * 查询条件的起始值
     * @var int
     */
    public $from = 0;

    public $model = '';
    /**
     * 初始化方法
     */
    public function _initialize() {
        $isLogin = $this->isLogin();
        if(!$isLogin) {
            return $this->redirect('login/index');
        }
    }

    public function isLogin() {
        $user = session(config('admin.session_user'), '', config('session_user_scope'));
        if($user && $user->id) {
            return true;
        }

        return false;
    }

    /**
     * 获取分页内容
     * @param $data
     */
    public function getPageAndSize($data) {
        $this->page = !empty($data['page']) ? $data['page'] : 1;
        $this->size = !empty($data['size']) ? $data['size'] : config('paginate.list_rows');
        $this->from = ($this->page - 1) * $this->size;
    }

    public function delete($id = 0) {
        if(!intval($id)) {
            return $this->result('', 0, 'ID不合法');
        }

        $model = $this->model ? $this->model : request()->controller();
        try {
            $res = model($model)->save(['status'=>-1], ['id'=>$id]);
        } catch (\Exception $e) {
            return $this->result('', 0, $e->getMessage());
        }

        if($res) {
            return $this->result(['jump_url' => $_SERVER['HTTP_REFERER']], 1, 'OK');
        }
        return $this->result('', 0, '删除失败');

    }

    /**
     * 通用化修改状态
     */
    public function status() {
        $data  = input('param.');
        // tp5  validate 机制 校验  小伙伴自行完成 id status

        // 通过id 去库中查询下记录是否存在
        //model('News')->get($data['id']);

        $model = $this->model ? $this->model : request()->controller();

        try {
            $res = model($model)->save(['status' => $data['status']], ['id' => $data['id']]);
        }catch(\Exception $e) {
            return $this->result('', 0, $e->getMessage());
        }

        if($res) {
            return $this->result(['jump_url' => $_SERVER['HTTP_REFERER']], 1, 'OK');
        }
        return $this->result('', 0, '修改失败');
    }
}
