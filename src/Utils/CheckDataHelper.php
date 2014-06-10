<?php
namespace Utils;

use Operator\ReadApi;
use Operator\WriteApi;

class CheckDataHelper
{
    const VIDEO_TYPE_OTHER = 0;

    const VIDEO_TYPE = 1;

    const SPECIAL_TYPE = 2;

    const EPG_VIDEO = 'EpgVideo';

    const PAGE_COUNT = 100;

    public static function syncData($types, $isSave)
    {
        $modelsName = \Config::get('params.epgCheck');
        WriteApi::startState_check();
        try {
            foreach ($modelsName as $modelName) {
                if (!$isSave) {
                    WriteApi::flashVideoStateCheck($modelName);
                }
                $model = new \ApiModel($modelName);
                $content = array('title:title', 'img:imgurl', 'vip:vip');
                $video_count = $model->count();

                $current = 0;
                while (1) {
                    if ($modelName == 'special_video') {
                        $dataList = $model->newQueryWithDeleted()->whereRaw(\ApiModel::DELETED_AT . ' = "0000-00-00 00:00:00" AND parent is not null')->skip($current)->take(self::PAGE_COUNT)->get();
                    } else {
                        $dataList = $model->newQueryWithDeleted()->whereRaw(\ApiModel::DELETED_AT . ' = "0000-00-00 00:00:00" ')->skip($current)->take(self::PAGE_COUNT)->get();
                    }
                    foreach ($dataList as $data) {
                        if (isset($data->epg_id)) {
                            $epg_id = $data->epg_id;
                        } elseif (isset($data->content_id)) {
                            $epg_id = $data->content_id;
                        } else {
                            $epg_id = null;
                        }
                        \Log::debug($modelName . ' :' . $data->id);
                        echo $modelName . ' :' . $data->id . ' ,epg_id :' . $epg_id . "\r\n";
                        $state = '';
                        $hasChange = false;
                        //sub->type  items
                        foreach ($types as $subType) {
                            if ($modelName == 'special_video' || (isset($data->content_type) && ($data->content_type == self::VIDEO_TYPE || $data->content_type == self::VIDEO_TYPE_OTHER))) {
                                $dataProvider = \Library\DataSource\DataProvider::factory(self::EPG_VIDEO, $epg_id, '');
                                if (isset($dataProvider->error) && !empty($dataProvider->error)) {
                                    continue;
                                }
                                $mapLocal = $dataProvider->mapLocal($content);
                                if (empty($mapLocal)) {
                                    continue;
                                };
                                if ($subType == 'geo') {
                                    if (!self::hasGeoSame($data->geo, $mapLocal['geo'])) {
                                        \Log::debug(sprintf('geo error id %s,epg_id %s,
                             local geo data ：%s ,
                             epg geo data : %s ', $data->id, $epg_id, json_encode($data->geo), json_encode($mapLocal['geo'])));

                                        if ($isSave && in_array('geo', $types)) {
                                            if (isset($mapLocal['geo']['type']) && $mapLocal['geo']['type'] === 0) {
                                                $data->geo = $mapLocal['geo'];
                                            } elseif (empty($data['geo']) || $data['geo']['type'] === 1) {
                                                $geo['type'] = 1;
                                                $geo['data'] = array_keys(array_except(\Config::get('params.areaFilterList'), $mapLocal['geo']['data']));
                                                $geo['force'] = array_keys(array_except(\Config::get('params.areaFilterList'), $mapLocal['geo']['force']));
                                                $data->geo = $geo;
                                            } elseif (!empty($data['geo']) && !isset($data['geo']['type']) || $data['geo']['type'] === 2) {
                                                $geo['type'] = 2;
                                                $geo['data'] = $mapLocal['geo']['data'];
                                                $geo['force'] = $mapLocal['geo']['force'];
                                                $data->geo = $geo;
                                            }
                                            $hasChange = true;
                                            if ($isSave && empty($state)) {
                                                $state = ReadApi::getVideoStateCheckInfo($data['id'], $modelName);
                                            }
                                            $state = str_replace('geo不一致', '', $state);
                                        } else {
                                            $state .= 'geo不一致';
                                        }
                                    }
                                } elseif ($subType != 'geo') {
                                    if ($mapLocal[$subType] != $data->$subType) {
                                        if ($isSave && in_array($subType, $types)) {
                                            $data->$subType = $mapLocal[$subType];
                                            if ($isSave && empty($state)) {
                                                $state = ReadApi::getVideoStateCheckInfo($data['id'], $modelName);
                                            }
                                            $hasChange = true;
                                            $state = str_replace($subType . '不一致', '', $state);
                                        } else {
                                            $state .= $subType . '不一致';
                                        }
                                    }
                                }

                            } elseif ($data->content_type == self::SPECIAL_TYPE && $modelName != 'special_video') {
                                $special_model = new \ApiModel('special');
                                $special_model->setTable('special');
                                $data_special = $special_model->newQuery()->find($data->content_id);
                                if ($data_special && $data_special->exists) {
                                    if ($subType == 'geo') {
                                        if ((isset($data->geo['data']) && isset($data_special->geo['data']) && !array_diff($data->geo['data'], $data_special->geo['data']))
                                            || (isset($data->geo['force']) && isset($data_special->geo['force']) && !array_diff($data->geo['force'], $data_special->geo['force']))
                                            || $data->geo === $data_special->geo

                                        ) {

                                        } else {
                                            @\Log::debug(sprintf('GEO diff :%s,  LOCAL geo data ：%s ,special geo data : %s ', json_encode(array_diff($data_special->geo['data'], $data->geo['data'])), json_encode($data->geo['data']), json_encode($data_special->geo['data'])));
                                            if ($isSave && in_array('geo', $types)) {
                                                $data->geo = $data_special->geo;
                                                $hasChange = true;
                                                if ($isSave && empty($state)) {
                                                    $state = ReadApi::getVideoStateCheckInfo($data['id'], $modelName);
                                                }
                                                $state = str_replace('geo不一致', '', $state);
                                            } else {
                                                $state .= 'geo不一致';
                                            }
                                        }
                                    } else {
                                        if ($data_special->$subType != $data->$subType) {
                                            if ($isSave && in_array($subType, $types)) {
                                                $data->$subType = $data_special->$subType;
                                                if ($isSave && empty($state)) {
                                                    $state = ReadApi::getVideoStateCheckInfo($data['id'], $modelName);
                                                }
                                                $hasChange = true;
                                                $state = str_replace($subType . '不一致', '', $state);
                                            } else {
                                                $state .= $subType . '不一致';
                                            }
                                        }
                                    }
                                }
                                /**/
                            }
                        }

                        $current += 1;
                        WriteApi::writeCurrentState_check($current . '/' . $video_count);
                        if (!isset($types['geo'])) {
                            $data->processGeo = false;
                        }
                        if ($hasChange && $isSave) {
                            $data->setTable($modelName);
                            $data->save();
                        }
                        if (strpos($state, '不一致') !== false) {
                            //still have some  diff
                            WriteApi::addVideoStateCheck($epg_id, $data->id, $modelName);
                            if (strpos($state, ':') !== false) {
                                WriteApi::setVideoStateCheckInfo($modelName, $data->id, $data->id . $state);
                            } else {
                                WriteApi::setVideoStateCheckInfo($modelName, $data->id, $data->id . ':' . $data->title . ':' . $state);
                            }
                        } else {
                            WriteApi::delVideoStateCheck($data->id, $modelName);
                            WriteApi::delVideoStateCheckInfo($data['id'], $modelName);
                        }
                    }
                    if (count($dataList) < self::PAGE_COUNT) {
                        break;
                    }
                }

            }
        } catch (\Exception $e) {
            \Log::debug($e->getMessage());
            WriteApi::set('error', $e->getMessage());
        }
    }

    public static function hasGeoSame($data_geo, $mapLocal_geo)
    {
        if (empty($mapLocal_geo['data'])) {
            $mapLocal_geo['data'] = array();
        }
        if (empty($mapLocal_geo['force'])) {
            $mapLocal_geo['force'] = array();
        }
        $local_geo = $data_geo;
        if (empty($local_geo['data'])) {
            $local_geo['data'] = array();
        }
        if (empty($local_geo['force'])) {
            $local_geo['force'] = array();
        }
        $map_force_count = count($mapLocal_geo['force']);
        $local_force_count = count($local_geo['force']);
        if ((empty($data_geo) && $mapLocal_geo['type'] == 0) || ($map_force_count == count(\Config::get('params.areaFilterList')) && $map_force_count == $local_force_count) ||
            (!array_diff($local_geo['data'], $mapLocal_geo['data']) && count($mapLocal_geo['data']) == count($local_geo['data']) && $mapLocal_geo['type'] !== 0
                && $map_force_count == $local_force_count && !array_diff($local_geo['force'], $mapLocal_geo['force']))
        ) {
            return true;
        }
        return false;
    }
}
