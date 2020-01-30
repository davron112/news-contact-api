<?php

namespace App\Services\Contracts;

use App\Models\Image;

/**
 * Interface ImageService
 *
 * @package App\Services\Contracts
 * @method bool destroy(int)
 */
interface ImageService extends BaseService
{
    /**
     * Store a newly created resource in storage
     *
     * @param array $data
     * @return Image
     */
    public function store(array $data);

    /**
     * Update block in the storage.
     *
     * @param  int  $id
     * @param  array  $data
     * @return Image
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
