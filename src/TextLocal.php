<?php

namespace Ridown\TextLocal;

class TextLocal
{
    private $url, $apiKey, $sender, $format, $username, $hash;
    
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
    }

    public function getBalance()
    {
        return $this->_sendRequest('balance');
    }
    
    public function send($message, $numbers, $sender = null, $sched = null, $test = false, $receiptURL = null, $custom = null, $optouts = false, $simpleReplyService = false)
    {
        $sender = ($sender != null) ? $sender : $this->sender;
        
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
        ];
        
        return $this->_sendRequest('send', $params);
        
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
    
    
    
}
