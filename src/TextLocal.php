<?php

namespace Ridown\TextLocal;

class TextLocal
{
    private $url, $key, $sender, $format;

    public function __construct()
    {
        $conn = 'sms.connections.'.config('sms.default');
        
        $this->key    = config( $conn . '.key');
        $this->sender = config( $conn . '.sender');
        $this->url    = config( $conn . '.url');
        $this->format = config( $conn . '.format');
    }

    public function send($message, $receiver, $sender = null)
    {
        $sender = $sender != null ? $sender : $this->sender;
        
        $post   = '';
        
        if($this->format === 'xml') {
            $xmlData = '
              <SMS>
                <Account apiKey="' . $this->key . '" Test="0" Info="1" JSON="0">
                  <Sender From="' . $sender . '">
                    <Messages>
                      <Msg ID="16" Number="' . $receiver . '">
                        <Text>' . $message . '</Text>
                      </Msg>
                    </Messages>
                  </Sender>
                </Account>
            </SMS>';
            $post .= 'data=' . urlencode($xmlData);
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        
        return $data;

    }
}
