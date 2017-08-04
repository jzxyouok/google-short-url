<?php
/**
 * google 短链服务
 * Created by PhpStorm.
 * User: zhaohailei
 * Date: 2017/8/4
 * Time: 下午5:25
 */
namespace common\components;

use Yii;

class ShortenUrl extends \yii\base\Component {

  /** @var string */
  const API_URL = 'https://www.googleapis.com/urlshortener/v1/url';

  /** @var null | string */
  public $api_key = null;

  public function init() {
    parent::init();
    if (!$this->api_key) {
      throw new HttpException(500, 'Set Api Key in Config');
    }
  }

  /**
   * 获取google 短链接
   * @param $long_url string
   * @return string
   */
  public function shortUrl($long_url) {
    $post_data = json_encode(['longUrl' => $long_url]);
    $result = $this->request($post_data, 'POST');
    return $result['id'] ?? $long_url;
  }

  public function request($post_data, $method) {
    try {
      if (SERVER_RUN_ON_BOX) {
        $start = microtime(true);
      }

      $curl = curl_init();
      switch (strtoupper($method)) {
        case 'POST':
          $url = self::API_URL . '?key=' . $this->api_key;
          curl_setopt($curl, CURLOPT_POST, 1);
          curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
          break;
        case 'GET':
          $url = self::API_URL . '?' . http_build_query($post_data);
          break;
      }
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($curl, CURLOPT_HEADER, 0);
      curl_setopt($curl, CURLOPT_NOSIGNAL, 1);
      curl_setopt($curl, CURLOPT_TIMEOUT_MS, 200);
      curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-type:application/json']);

      $response = curl_exec($curl);
      $response = json_decode($response, true);
      curl_close($curl);

      if (SERVER_RUN_ON_BOX) {
        $end = microtime(true);
        $t1 = $end - $start;
        Yii::info(__METHOD__." API call: {$t1}");
      }
    } catch (\Exception $ex) {
      \common\helper\SentryHelper::getInstance()->captureException($ex);
      return null;
    }

    return $response;
  }
}
