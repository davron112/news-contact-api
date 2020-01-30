<?php
namespace App\Models\Traits;

trait TableName
{
    /**
     * @return mixed
     */
    public static function getTableName()
    {
        $class = self::class;

        return with(new $class())->getTable();
    }
}
