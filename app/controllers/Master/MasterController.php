<?php

class MasterController extends Controller
{

    protected $navs = array();

    protected $side = '';

    protected $cur = '';

    protected $nav = '';

    protected $menu = '';

    protected $_webconfig = array();

    protected $allow_app_id_key = 'allow_app_id';

    public function __construct()
    {

        if (App::environment() !== 'testing')
            $this->beforeFilter('auth', array('except' => array('loginStore', 'login')));

    }

    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

    protected function render($view, $data = array(), $header = array())
    {
        $header['navs'] = $this->navs;
        $header['leftMenu'] = $this->getSide($this->nav);
        $header['menu'] = $this->menu;
        $header['nav'] = $this->nav;
        return View::make($view, $data)
            ->nest('header', 'layout.header', $header)
            ->nest('footer', 'layout.footer')
            ->nest('setup', 'layout.setup_guid');
    }

    /**
     *
     * @param array $data  返回的ajax的json数据
     * @param string $status 返回的状态
     * @param string $message 需要返回的信息
     * @param string $redirect 是否需要跳转，如是要跳转需设置这个值
     */
    protected function ajaxResponse($data = array(), $status = 'success', $message = '', $successRedirect = '', $failRedirect = '')
    {
        $return = array(
            'status'          => $status,
            'message'         => $message,
            'data'            => $data,
            'successRedirect' => $successRedirect,
            'failRedirect'    => $failRedirect
        );
        if ($successRedirect || $failRedirect)
            \Utils\Env::messageTip("messageTip", $status, $message);

        echo json_encode($return);

        //在强制退出支 触发结束事件
        App::shutdown();

        exit(1);
    }
}