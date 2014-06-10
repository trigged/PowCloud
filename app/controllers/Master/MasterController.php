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

        if (!Auth::guest()) {
            $this->setNavs();

//            if ($this->nav && !isset($this->navs[$this->nav]))
//                header('Location:' . URL::action('DashBoardController@index'));

            if (method_exists($this, 'getDbSide'))
                $this->setSide($this->getDbside());
            else
                $this->setSide();
        }

    }


    public function getOption()
    {
        $user = Auth::user();
        if (!isset($user->group_id)) {
            return false;
        }
        $group_option = GroupOperation::where('group_id', $user->group_id)->get()->toArray();

        $options = array();
        $options['no_right'] = true;
        foreach ($group_option as $option) {
            $options[$option['models_id']] = $option;
            if ($option['read'] == 2 or $option['edit'] == 2) {
                $options['no_right'] = false;
            }
        }
        return $options;
    }

    public function getNavs()
    {

        return $this->navs;

    }

    public function setNavs()
    {

        if ((int)Auth::user()->roles === 3) {
            $this->navs = Config::get('menu.nav');
        } else {
            $this->navs['cms'] = Config::get('menu.nav.cms');
        }

    }

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */

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
        $header['webconfig']['picture_upload_url'] = Config::get('app.picture_upload.url');
        return View::make($view, $data)
            ->nest('header', 'layout.header', $header)
            ->nest('footer', 'layout.footer')
            ->nest('setup', 'layout.setup_guid');
    }

    public function getSide($where)
    {

        if (isset($this->navs[$where]) && isset($this->side[$where]))
            return $this->side[$where];
        return array();
    }

    public function setSide($side = array())
    {

        $this->side = array_merge_recursive(Config::get('menu.side'), $side);
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

        if($successRedirect || $failRedirect)
            \Utils\Env::messageTip("messageTip", $status, $message);
        echo json_encode($return);

        //在强制退出支 触发结束事件
        App::shutdown();

        exit(1);
    }
}