<?php

namespace App\Services\Contracts;

/**
 * Interface TelegramService
 * @package App\Services\Contracts
 */
interface TelegramService extends BaseService
{
    /**
     * @param $id
     * @param $text
     * @return void
     */
    public function sendMessageToClient($id, $text);


    /**
     * @param $title
     * @param $url
     * @param $image
     * @param $tags
     * @return void
     */
    public function sendMessageChannel($title, $url, $image, $tags = null);
}
