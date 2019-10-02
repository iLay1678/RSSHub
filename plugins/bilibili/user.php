<?php
/**
 * Created by PhpStorm.
 * User: XiaoLin
 * Date: 2018-11-03
 * Time: 6:18 PM
 */

namespace RSSHub\Plugins\bilibili;

use Curl\Curl;
use RSSHub\Lib\Cache;
use RSSHub\Lib\Exception;
use RSSHub\Lib\XML;

class user
{
    public $_info = [
        'routes' => [
            'user/video/:uid' => 'video',
            'user/dynamic/:uid' => 'dynamic',
        ],
    ];

    protected function getUserData($uid)
    {
        $md5 = md5("https://space.bilibili.com/ajax/member/GetInfo/{$uid}");

        $data = Cache::getCache($md5,function () use ($uid)
        {
            $curl = new Curl();
            $curl->setReferrer("https://space.bilibili.com/{$uid}/");
            $curl->setHeader('Content-Type','application/x-www-form-urlencoded');
            $curl->setOpt(CURLOPT_SSL_VERIFYPEER,0);
            $curl->setOpt(CURLOPT_SSL_VERIFYHOST,0);
            $data = $curl->post("https://space.bilibili.com/ajax/member/GetInfo",[
                'mid' => $uid,
            ]);

            return $data;
        });
        $data = json_decode($data,true);

        if ($data['status'] == true)
        {
            return json_decode(json_encode($data),true)['data'];
        } else
            throw new Exception("[BiliBili] UID:{$uid} 个人信息获取失败.",'error');
    }

    public static function video($uid)
    {
        $md5 = md5("https://space.bilibili.com/ajax/member/getSubmitVideos?mid={$uid}");

        $data = Cache::getCache($md5,function () use ($uid)
        {
            $curl = new Curl();
            $curl->setReferrer("https://space.bilibili.com/{$uid}/");
            $curl->setOpt(CURLOPT_SSL_VERIFYPEER,0);
            $curl->setOpt(CURLOPT_SSL_VERIFYHOST,0);

            $data = json_decode(json_encode($curl->get("https://space.bilibili.com/ajax/member/getSubmitVideos?mid={$uid}")),true);

            return $data;
        });

        $data = json_decode($data,true);

        if (is_array($data) && $data['status'] == true)
        {
            $user = (new user)->getUserData($uid);

            $list = [
                'title' => '「' . $user['name'] . '」的哔哩哔哩投稿',
                'link' => "https://space.bilibili.com/{$uid}/#/video",
                'date' => filemtime(__DIR__ . '/../../cache/' . $md5 . '.json'),
                'description' => "签名: {$user['sign']}",
                'image' => [
                    'url' => $user['face'],
                    'title' => 'face',
                    'src' => $user['face'],
                ],
                'items' => [],
            ];

            foreach ($data['data']['vlist'] as $video)
            {
                $list['items'][] = [
                    'title' => $video['title'],
                    'link' => "https://www.bilibili.com/video/av{$video['aid']}",
                    'date' => $video['created'],
                    'description' => $video['description'] . "<br><img referrerpolicy=\"no-referrer\" src=\"{$video['pic']}\">",
                ];
            }

            return $list;
        } else
            throw new Exception("[BiliBili] UID: {$uid} 视频投稿获取失败",'error');
    }


    public static function dynamic($uid)
    {
        $md5 = md5("https://api.vc.bilibili.com/dynamic_svr/v1/dynamic_svr/space_history?host_uid={$uid}");

        $data = Cache::getCache($md5,function () use ($uid)
        {
            $curl = new Curl();
            $curl->setReferrer("https://space.bilibili.com/{$uid}/");
            $curl->setOpt(CURLOPT_SSL_VERIFYPEER,0);
            $curl->setOpt(CURLOPT_SSL_VERIFYHOST,0);

            $data = json_decode(json_encode($curl->get("https://api.vc.bilibili.com/dynamic_svr/v1/dynamic_svr/space_history?host_uid={$uid}")),true);

            return $data;
        });

        $data = json_decode($data,true);

        if (is_array($data) && $data['code'] == 0)
        {
            $user = (new user)->getUserData($uid);

            $list = [
                'title' => '「' . $user['name'] . '」的哔哩哔哩动态',
                'link' => "https://space.bilibili.com/{$uid}/#/dynamic",
                'date' => filemtime(__DIR__ . '/../../cache/' . $md5 . '.json'),
                'description' => "签名: {$user['sign']}",
                'image' => [
                    'url' => $user['face'],
                    'title' => 'face',
                    'src' => $user['face'],
                ],
                'items' => [],
            ];

            foreach ($data['data']['cards'] as $card)
            {
                $tmp = json_decode($card['card'],true);
                $img = '';
                foreach ($tmp['item']['pictures'] as $picture)
                {
                    $img .= "<img referrerpolicy=\"no-referrer\" src=\"{$picture['img_src']}\"><br>";
                }
                if ($img != '') $img = substr($img,0,-4);

                $list['items'][] = [
                    'title' => (isset($tmp['item']['title'])) ? $tmp['item']['title'] : ((isset($tmp['title'])) ? $tmp['title'] : json_decode($tmp['origin'],true)['title']),
                    'link' => "https://t.bilibili.com/{$card['desc']['dynamic_id']}",
                    'date' => $card['desc']['timestamp'],
                    'description' => (isset($tmp['item']['description'])) ? $tmp['item']['description'] : ((isset($tmp['desc'])) ? $tmp['desc'] : $tmp['item']['content']) . $img,
                ];
            }

            return $list;
        } else
            throw new Exception("[BiliBili] UID: {$uid} 动态获取失败",'error');
    }
}