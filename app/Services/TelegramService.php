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
     * @return mixed
     */
    public function sendMessageToClient($id, $text)
    {
        $test = Telegram::sendMessage([
            'chat_id' => $id,
            'parse_mode' => 'HTML',
            'text' => $text
        ]);

        return $test;
    }

    /**
     * @param $title
     * @param $description
     * @param $image
     * @return mixed
     */
    public function sendMessageChannel($title, $description, $image)
    {
        $test = Telegram::sendPhoto([
            'chat_id' => config('telegram.telegram_public_channel_id'),
            'photo' => $image,
            'caption' => $title
        ]);

        return $test;
    }
}
