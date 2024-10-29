<?php
namespace App\Service;

class ContentWatchApi
{
    private const API_URL = 'https://content-watch.ru/public/api/';
    public function __construct(
        private readonly string $key
    ) {
    }

    public function checkText(string $text): int
    {
        $curl = curl_init(self::API_URL);


        $post_data = array(
            'key' => $this->key,
            'text' => $text,
            'test' => 1
        );

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_data, '', '&'));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);




        $data = json_decode(trim(curl_exec($curl)), TRUE);

        curl_close($curl);

        return $data['percent'];
    }
}