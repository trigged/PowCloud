<?php
/**
 *
 * 所有要在内容管理里出现在的功能需 extend 这个类
 * User: troyfan
 * Date: 14-3-20
 * Time: 上午11:08
 */

class CmsBaeController extends BaseController
{

    public $nav = 'cms';

    /**
     * 从数据库动态拉取左侧菜单生成 不做action使用
     *
     * @return array
     */
    protected function getDbSide()
    {
        $tables = SchemaBuilder::all();
        $options = $this->getOption();
        if ($options === false || $options['no_right'] == 1) {
            return array();
        }

        $cmsSideFromDb = array();
        foreach ($tables as $table) {
            if (isset($options[$table->id]) && $options[$table->id]['read'] == 2) {
                if ($table->group_name) {
                    $group = $table->group_name;
                } else {
                    $group = '数据管理';
                }
                $cmsSideFromDb['cms'][$group][] = array(
                    'label' => $table->table_alias,
                    'url'   => URL::action('CmsController@index', array('id' => $table->id)),
                    'menu'  => 'cms.table.' . $table->id,
                );
            }

        }
        $cmsSideFromDb['cms']['数据级联管理'] = array(
            array('label' => '数据变更', 'url' => URL::action('DataLinkController@index'), 'menu' => 'data.change',),
        );
        return $cmsSideFromDb;
    }
} 