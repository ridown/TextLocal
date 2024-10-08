<?php

namespace Ridown\TextLocal;

class TextLocal
{
    private $url, $apiKey, $sender, $format, $username, $hash, $unicode_enabled = false;
    
    const REQUEST_TIMEOUT = 60;
    const REQUEST_HANDLER = 'curl';
    
    public function __construct()
    {
        $conn = 'sms.connections.'.config('sms.default');
        
        $this->apiKey = config( $conn . '.key');
        $this->username = config( $conn . '.username');
        $this->hash   = config( $conn . '.hash');
        $this->sender = config( $conn . '.sender');
        $this->url    = config( $conn . '.url');
        $this->format = config( $conn . '.format');
        $this->unicode_enabled = config( $conn . '.unicode_enabled', false);  
    }

    public function getBalance()
    {
        return $this->_sendRequest('balance');
    }
    
    public function send($message, $numbers, $sender = null, $sched = null, $test = false, $receiptURL = null, $custom = null, $optouts = false, $simpleReplyService = false)
    {
        $sender = ($sender != null) ? $sender : $this->sender;
        
        $unicode = false;
        if(strstr($message, 'U+') && $this->unicode_enabled) {
            $unicode='true';
            $message = $this->unicodeMessageEncode($message);
        }
        
        if(!is_array($numbers)) $numbers = [$numbers];
        $params = [
            'message'       => rawurlencode($message),
            'numbers'       => implode(',', $numbers),
            'sender'        => rawurlencode($sender),
            'schedule_time' => $sched,
            'test'          => $test,
            'receipt_url'   => $receiptURL,
            'custom'        => $custom,
            'optouts'       => $optouts,
            'simple_reply'  => $simpleReplyService,
            'unicode'       => $unicode,
        ];
        
        return $this->_sendRequest('send', $params);
        
    }

    /**
     * Get the status of a message based on the Message ID - this can be taken from send() or from a history report
     * @param $messageidhttps://github.com/ridown/TextLocal.git
     * @return array|mixed
     */
    public function getMessageStatus($messageid)
    {
        $params = array("message_id" => $messageid);
        return $this->_sendRequest('status_message', $params);
    }

    private function _sendRequest($command, $params = [])
    {
        if ($this->apiKey && ! empty($this->apiKey)) {
            $params['apiKey'] = $this->apiKey;
        } else {
            $params['hash'] = $this->hash;
        }
        // Create request string
        $params['username'] = $this->username;
        $this->lastRequest = $params;
        if (self::REQUEST_HANDLER == 'curl') {
            $rawResponse = $this->_sendRequestCurl($command, $params);
        } else {
            throw new \Exception('Invalid request handler.');
        }
        $result = json_decode($rawResponse);
        
        if (isset($result->errors)) {
            if (count($result->errors) > 0) {
                foreach ($result->errors as $error) {
                    switch ($error->code) {
                        default:
                            throw new \Exception($error->message);
                    }
                }
            }
        }
        return $result;
    }
    
    
    private function _sendRequestCurl($command, $params)
    {
        $url = $this->url.$command.'/';
        
        $ch = curl_init($url);
        curl_setopt_array(
            $ch, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $params,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT        => self::REQUEST_TIMEOUT,
            ]
        );
        $rawResponse = curl_exec($ch);
        $httpCode    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error       = curl_error($ch);
        
        curl_close($ch);
        
        if ($rawResponse === false) {
            throw new \Exception('Failed to connect to the Textlocal service: '.$error);
        } elseif ($httpCode != 200) {
            throw new \Exception('Bad response from the Textlocal service: HTTP code '.$httpCode);
        }
        return $rawResponse;
    }
    
    protected function unicodeMessageDecode($message) {
        if (stripos($message, '@U') !== 0) {
            return $message;
        }
        $message = substr($message, 2);
        $_message = hex2bin($message);
        $message = mb_convert_encoding($_message, 'UTF-8', 'UCS-2');
        return $message;
    }

    protected function unicodeMessageEncode($message){
        return '@U' . strtoupper(bin2hex(mb_convert_encoding($message, 'UCS-2','auto')));
    }
    
    protected function utf8_to_unicode_codepoints($text) {
         return ''.implode(unpack('H*', iconv("UTF-8", "UCS-2BE", $text)));
     }
    
}
