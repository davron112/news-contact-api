<?php

namespace App\Services\Contracts;

use App\Models\Video;

/**
 * Interface VideoService
 *
 * @property string $link
 * @package App\Services\Contracts
 * @method bool destroy(int)
 */
interface VideoService extends BaseService
{
    /**
     * Store a newly created resource in storage
     *
     * @param array $data
     * @return Video
     */
    public function store(array $data);

    /**
     * Update block in the storage.
     *
     * @param  int  $id
     * @param  array  $data
     * @return Video
     */
    public function update($id, array $data);

    /**
     * Update block in the storage.
     *
     * @param  int  $id
     * @return array
     */
    public function delete($id);
}
