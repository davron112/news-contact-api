<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Log\Logger;
use App\Services\Contracts\TelegramService as TelegramServiceInterface;
use Telegram\Bot\Laravel\Facades\Telegram;

/**
 * Class TelegramService
 * @package App\Services
 */
class TelegramService extends BaseService implements TelegramServiceInterface {

    /**
     * TelegramService constructor.
     * @param DatabaseManager $databaseManager
     * @param Logger $logger
     * @param UserRepository $repository
     */
    public function __construct(
        DatabaseManager $databaseManager,
        Logger $logger,
        UserRepository $repository
    )
    {
        parent::__construct($databaseManager, $logger, $repository);
    }

    /**
     * @param $id
     * @param $text
     * @return void
     */
    public function sendMessageToClient($id, $text)
    {
        Telegram::sendMessage([
            'chat_id' => $id,
            'parse_mode' => 'HTML',
            'text' => $text
        ]);
    }

    /**
     * @param $title
     * @param $url
     * @param $image
     * @param $tags
     * @return void
     */
    public function sendMessageChannel($title, $url, $image, $tags)
    {
        Telegram::sendPhoto([
            'chat_id' => config('telegram.channel_id'),
            'photo' => 'https:' . $image,
            'caption' => $title
                .PHP_EOL .'ℹ👉  http://sportonline.uz/uz' . $url
                .PHP_EOL .'Telegram: 👉 https://t.me/sportonlineuz'
                .PHP_EOL . '#' . $tags
        ]);
    }
}
