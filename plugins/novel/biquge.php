<?php namespace RSSHub\Plugins\novel;
use Curl\Curl;
use RSSHub\Lib\Cache;
use RSSHub\Lib\Exception;
use RSSHub\Lib\XML;
use Symfony\Component\DomCrawler\Crawler;
class biquge
{
    public $_info = [
        'routes' => [
            'biquge/:id' => 'biquge',
        ],
    ];

    public static function biquge($id) {
        
        if ($id==null) {
            throw new Exception('id不能为空','error');
        }
        
        $md5=md5("https://www.biquge5200.com/{$id}/");
        $data=Cache::getCache($md5,function () use ($id) {
        $curl = new Curl();
        $curl->setReferrer("https://www.biquge5200.com/{$id}/");
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER,0);
        $curl->setOpt(CURLOPT_SSL_VERIFYHOST,0);
        $curl->setopt(CURLOPT_FOLLOWLOCATION,1);
        $request = $curl->get("https://www.biquge5200.com/{$id}/");
        $request = mb_convert_encoding($request, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
        return $request;
        });
        
        $request=json_decode($data,true);
        $data = [];
        $crawler = new Crawler();
        $crawler->addHtmlContent($request);
        try {
            $data['title'] = '笔趣阁 《'.$crawler->filterXPath('//*[@id="info"]/h1')->text().'》 最新章节';
            $data['description'] = $crawler->filterXPath('//*[@id="intro"]')->text();
            $data['link'] = "https://www.biquge5200.com/{$id}/";
            $data['date'] = filemtime(__DIR__ . '/../../cache/' . $md5 . '.json');
            $data['generator']='https://rss.ifking.cn';
            $data['webMaster']='iLay1678 i@ifking.cn';
            for ($i = 1;$i < 10;) {
                $data['items'][$i] = [
                    "title" => $crawler->filterXPath('//*[@id="list"]/dl/dd['.$i.']/a')->text(),
                    "link" => $crawler->filterXPath('//*[@id="list"]/dl/dd['.$i.']/a')->attr('href'),
                    "description" => self::content($crawler->filterXPath('//*[@id="list"]/dl/dd['.$i.']/a')->attr('href')),
                ];
                $i++;
            }
         return $data;
        } catch (\Exception $e) {
            throw new Exception("获取小说数据失败，请检查参数是否正确",'warning');
        }
    }
    private function content ($url) {
        
        $md5=md5($url);
        $data=Cache::getCache($md5,function () use ($url) {
        $curl = new Curl();
        $curl->setReferrer($url);
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER,0);
        $curl->setOpt(CURLOPT_SSL_VERIFYHOST,0);
        $curl->setopt(CURLOPT_FOLLOWLOCATION,1);
        $request = $curl->get($url);
        $request = mb_convert_encoding($request, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
        return $request;
        });
        
        $request=json_decode($data,true);
        $crawler = new Crawler();
        $crawler->addHtmlContent($request);
        $content = $crawler->filterXPath('//div[contains(@id,"content")]')->html();
        return $content;
    }
}