<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 11/13/13
 * Time: 2:17 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Utils;

use Illuminate\Support\Facades\Config;
use Utils\IOHelper;

class CreateVirtualHost
{
    protected static $cmd_index_content = '<?php echo "it`s works !" ?>';
    protected static $cmd_index = 'index.php';

    public static function checkHost()
    {
        return Config::get('app.nginx');
    }

    public static function createHost($host)
    {
        $path = Config::get('app.nginx') . $host;
        return IOHelper::Store($path, self::$cmd_index, self::$cmd_index_content);
    }
}