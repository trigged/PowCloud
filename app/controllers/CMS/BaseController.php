<?php



class BaseController extends Controller
{

    static $SUCCESS = 1;

    static $_SUCCESS_TEMPLATE = 10;

    static $FAILED = -1;

    static $_FAILED_TEMPLATE = -10;

    static $MESSAGE_NOT_EXISTS = '数据不存在';

    static $MESSAGE_HAS_EXISTS = '数据已经存在';

    static $MESSAGE_DO_SUCCESS = '操作成功';

    static $MESSAGE_DO_FAILED = '操作失败,请检查填写数据';

    static $MESSAGE_DATA_ERROR = '数据出错';

    protected $navs = array();

    protected $side = '';

    protected $cur = '';

    protected $nav = '';

    protected $menu = '';

    protected $app_id = '';

    protected $atu_model = null;

    protected $_webconfig = array();

    protected $allow_app_id_key = 'allow_app_id';

    protected $_current_user_id = null;

    public function __construct()
    {

        $app_id = (int)Input::get('app_id');
        if ($app_id && $app_id !== 0) {
            Session::set('app_id', $app_id);
            $this->app_id = $app_id;
        }
        if (!$this->userHasAppRight()) {
            \Utils\CMSLog::debug('userHasAppRight false exit');
//            Auth::logout();
//            exit(1);
        }

        \Utils\AppChose::updateConf();
        if (!Auth::guest()) {

            $this->setNavs();
            if ($this->nav && !isset($this->navs[$this->nav]))
                header('Location:' . URL::action('DashBoardController@index'));

            if (method_exists($this, 'getDbSide'))
                $this->setSide($this->getDbside());
            else
                $this->setSide();
        }
    }

    /**
     * @return bool
     * make sure user has access the user right
     */
    public function userHasAppRight()
    {
        if (!Session::has('app_id')) {
            return false;
        }
        $app_id = Session::get('app_id');
        $this->app_id = $app_id;
        if (Session::has($this->allow_app_id_key)) {
            $app_ids = Session::get($this->allow_app_id_key);
            if ($app_ids && array_key_exists($this->app_id, $app_ids)) {
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * @param $status 请求是否处理成功
     * @param string $message 需要返回的信息
     * @param string $data 返回的ajax的数据
     * @param string $redirect 是否需要跳转，如是要跳转需设置这个值
     */
    public static function ajaxResponse($status, $message = '', $data = '', $redirect = '')
    {
        if($redirect == 'index'){
            $redirect = URL::action('DashBoardController@index');
        }
        if ($status == self::$_SUCCESS_TEMPLATE) {
            $return = array(
                'status'   => self::$SUCCESS,
                'message'  => self::$MESSAGE_DO_SUCCESS,
                'data'     => $data,
                'redirect' => $redirect,
            );
        } elseif ($status == self::$_FAILED_TEMPLATE) {
            $return = array(
                'status'   => self::$FAILED,
                'message'  => self::$MESSAGE_DO_FAILED,
                'data'     => $data,
                'redirect' => $redirect,
            );
        } else {
            $return = array(
                'status'   => $status,
                'message'  => $message,
                'data'     => $data,
                'redirect' => $redirect,
            );
        }

        echo json_encode($return);

        //在强制退出支 触发结束事件
        App::shutdown();

        exit(1);
    }

    public function location($code = 1, $message = '', $url = '' )
    {
        if(empty($url)){
            $url = URL::action('DashBoardController@index');
        }
        return self::render('layout.location', array('code' => $code, 'message' => $message, 'url' => $url));
    }

    protected function render($view, $data = array(), $header = array())
    {
        $header['navs'] = $this->navs;
        $header['leftMenu'] = $this->getSide($this->nav);
        $header['menu'] = $this->menu;
        $header['nav'] = $this->nav;
        return View::make($view, $data)
            ->nest('header', 'layout.header', $header)
            ->nest('footer', 'layout.footer');
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

    public function dispatch()
    {
        return Response::json(array(
            'code'    => -1,
            'message' => 'request not found!',
            'data'    => array()), 404);
    }

    public function getRoles()
    {
        return (int)$this->AtuModel()->roles;
    }

    public function AtuModel()
    {
        if ($this->atu_model == null) {
            $this->atu_model = ATURelationModel::where('app_id', $this->app_id)->where('user_id', Auth::user()->id)->first();
        }
        return $this->atu_model;
    }

    public function getCurrentUserID()
    {
        if ($this->_current_user_id == null) {
            $this->_current_user_id = Auth::user()->id;
        }
        return $this->_current_user_id;
    }

    public function getOption()
    {
        $group_option = GroupOperation::where('group_id', $this->getGroupID())->get()->toArray();

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

    public function getGroupID()
    {
        return (int)$this->AtuModel()->group_id;
    }

    public function getNavs()
    {

        return $this->navs;

    }

    public function setNavs()
    {

        if ($this->getRoles() === 3) {
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
}