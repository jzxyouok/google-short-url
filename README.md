# google-short-url
yii2 接入google 短链服务

参照： https://developers.google.com/url-shortener/v1/getting_started

引入方法：
1. 把ShortenUrl.php下载，放到项目 components目录下
2. 在main.php中配置相关信息
  components => [
    ....
    'short_url' => [
      'class' => 'common\components\ShortenUrl.php',
      'api_key' => 'yourself api key',
    ],
    ....
  ];
  
3. 使用：
use Yii;
return Yii::$app->short_url->shortUrl($original_url);
