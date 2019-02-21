<?php
header('Access-Control-Allow-Origin:*');
header("Content-Type: text/html;charset=utf-8");
require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use phpspider\core\selector;


$url = $_GET["url"];

echo json_encode(['path' => Spider($url)]);


return;
function Spider($url)
{
    if (strpos("复制", $url) !== false){
        preg_match_all('/http(.*)复制/', str_replace(' ', '',$url),$vURL);

        //需要爬取的页面
        $url = $vURL[1][0];
    }

    //下载网页内容
    $client   = new Client([
        'timeout' => 10,
        'headers' => ['User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1',
        ],
   ]);

    $response = $client->request('GET', $url)->getBody()->getContents();

    $val = str_replace(" ",'',$response);
    $val = str_replace("\n",'',$val);

    preg_match_all('/\"theVideo"class="video-player"src=\"(.*)&amp;line=0/', $val,$data);

    $watermarkURL = $data[1][0];


    $video_raw_url = str_replace('playwm', 'play', $watermarkURL);

    return download($video_raw_url);
}

function download($url){
    //下载视频 存储10s

    $client   = new Client([
        'timeout' => 10,
        'headers' => ['User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1',
        ],
    ]);

//    $client = new Client(['verify' => false]);

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