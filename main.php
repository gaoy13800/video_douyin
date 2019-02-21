<?php
require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use phpspider\core\selector;

print_r(json_encode(Spider(), JSON_UNESCAPED_UNICODE));


function Spider()
{
    //需要爬取的页面
    $url = 'http://v.douyin.com/YaCD7c';

    //下载网页内容
    $client   = new Client([
        'timeout' => 10,
        'headers' => ['User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1',
        ],
   ]);
    $response = $client->request('GET', $url)->getBody()->getContents();

    $val = str_replace(" ",'',$response);
    $val = str_replace("\n",'',$val);

    preg_match_all('/\"theVideo"class="video-player"src=\"(.*);line=0/', $val,$data);

    $watermarkURL = $data[1][0];

    $video_raw_url = str_replace('playwm', 'play', $watermarkURL);


    //下载视频 存储10s

    return download($video_raw_url);
}

function download($url){
    //下载视频 存储10s

    $client = new Client(['verify' => false]);

    $path = "./DouYinVideo/" . time() . '.mp4';

    $pathParents = dirname($path);

    if (!file_exists($pathParents)){
        mkdir($pathParents, '0777');
    }

    $response = $client->get($url, ['save_to' => $path]);

    if ($response->getStatusCode() == 200) {
        return $path;
    }
}