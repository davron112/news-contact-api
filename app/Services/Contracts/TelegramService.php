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
     * @return mixed
     */
    public function sendMessageToClient($id, $text);


    /**
     * @param $title
     * @param $description
     * @param $image
     * @return mixed
     */
    public function sendMessageChannel($title, $description, $image);
}
