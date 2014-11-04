<?php

class GeoController extends CmsBaeController
{

    public function shortcut()
    {

        $this->menu = 'geo.shortcut';

        $shortCuts = GeoShortcut::paginate();

        return $this->render('geo.shortcut', array('shortCuts' => $shortCuts));
    }

    public function shortcut_create()
    {

        $this->menu = 'geo.shortcut';

        return $this->render('geo.shortcut_create');
    }

    public function shortcut_store()
    {

        $shortcut = new GeoShortcut(Input::except('geo'));
        $shortcut->scene = 'create';
        if ($shortcut->save()) {

            $this->ajaxResponse('', 'success', '创建地域快捷键生效', URL::action('GeoController@shortcut'));
        }
        $this->ajaxResponse($shortcut->errors, 'fail', '创建地域快捷键生效', URL::action('GeoController@shortcut'));
    }

    public function shortcut_edit($id)
    {

        $this->menu = 'geo.shortcut';

        $shortCut = GeoShortcut::find($id);
        if (!$shortCut) App::abort(404);
        $short = explode(',', $shortCut->shortcut);
        return $this->render('geo.shortcut_edit', array(
            'short'    => $short,
            'shortCut' => $shortCut
        ));

    }

    public function shortcut_update($id)
    {

        $shortCut = GeoShortcut::find($id);
        if (!$shortCut)
            $this->ajaxResponse('', 'fail', '##' . $id . '##对应的数据被删除或者不存在');
        if ($shortCut->name === Input::get('name'))
            $shortCut->scene = 'update';
        if ($shortCut->update(Input::except('geo'))) {
            $this->ajaxResponse('', 'success', '更新成功', URL::action('GeoController@shortcut'));
        }
        $this->ajaxResponse($shortCut->errors, 'fail', '更新失败');
    }

    public function shortcut_destroy($id)
    {
        try {
            GeoShortcut::find($id)->forceDelete();
            $this->ajaxResponse('', 'success', '删除成功');
        } catch (Exception $e) {
            $this->ajaxResponse('', 'fail', '删除失败');
        }
    }

}