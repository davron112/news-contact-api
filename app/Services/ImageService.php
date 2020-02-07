<?php

namespace App\Services;

use App\Exceptions\UnexpectedErrorException;
use App\Helpers\FileHelper;
use App\Models\Image;
use App\Models\Language;
use App\Repositories\Contracts\ImageRepository;
use App\Services\Contracts\ImageService as ImageServiceInterface;
use App\Services\Traits\ServiceTranslateTable;
use Illuminate\Database\DatabaseManager;
use Illuminate\Log\Logger;
use Illuminate\Support\Arr;

/**
 * @method bool destroy
 */
class ImageService  extends BaseService implements ImageServiceInterface
{
    use ServiceTranslateTable;

    /**
     * @var DatabaseManager $databaseManager
     */
    protected $databaseManager;

    /**
     * @var ImageRepository $repository
     */
    protected $repository;

    /**
     * Language $language
     */
    protected $language;

    /**
     * @var Logger $logger
     */
    protected $logger;

    /**
     * @var FileHelper $fileHelper
     */
    protected $fileHelper;

    /**
     * ImageService constructor.
     *
     * @param DatabaseManager $databaseManager
     * @param ImageRepository $repository
     * @param Language $language
     * @param Logger $logger
     * @param FileHelper $fileHelper
     */
    public function __construct(
        DatabaseManager $databaseManager,
        ImageRepository $repository,
        Language $language,
        Logger $logger,
        FileHelper $fileHelper
    ) {

        $this->databaseManager     = $databaseManager;
        $this->repository     = $repository;
        $this->logger     = $logger;
        $this->language     = $language;
        $this->fileHelper     = $fileHelper;
    }

    /**
     * Create image
     *
     * @param array $data
     * @return Image
     * @throws \Exception
     */
    public function store(array $data)
    {

        $this->beginTransaction();

        try {
            $image = $this->repository->create($data);
            if (!$image->save()) {
                throw new UnexpectedErrorException('Image was not saved to the database.');
            }
            $this->logger->info('Image was successfully saved to the database.');
        } catch (UnexpectedErrorException $e) {
            $this->rollback($e, 'An error occurred while storing an ', [
                'data' => $data,
            ]);
        }

        $this->commit();

        return $image;
    }

    /**
     * Update block in the storage.
     *
     * @param  int  $id
     * @param  array  $data
     *
     * @return Image
     *
     * @throws
     */
    public function update($id, array $data)
    {
        $this->beginTransaction();

        try {
            $image = $this->repository->find($id);

            if (!$image->update($data)) {
                throw new UnexpectedErrorException('An error occurred while updating a image');
            }
            $this->logger->info('Image was successfully updated.');

        } catch (UnexpectedErrorException $e) {
            $this->rollback($e, 'An error occurred while updating an articles.', [
                'id'   => $id,
                'data' => $data,
            ]);

        }
        $this->commit();
        return $image;
    }
    /**
     * Delete block in the storage.
     *
     * @param  int  $id
     *
     * @return array
     *
     * @throws
     */
    public function delete($id)
    {

        $this->beginTransaction();

        try {
            $bufferImage = [];
            $image = $this->repository->find($id);

            $bufferImage['id'] = $image->id;
            $bufferImage['name'] = $image->name;

            if (!$image->delete($id)) {
                throw new UnexpectedErrorException(
                    'Image and image translations was not deleted from database.'
                );
            }
            $this->logger->info('Image image was successfully deleted from database.');
        } catch (UnexpectedErrorException $e) {
            $this->rollback($e, 'An error occurred while deleting an image.', [
                'id'   => $id,
            ]);
        }
        $this->commit();
        return $bufferImage;
    }


    protected function storeImage(array $data){

        $dataFields =[];
        if(Arr::has($data,'img')) {
            $uploadedFile  = $data['img'];
            $dataFields['img'] = $this->fileHelper->upload($uploadedFile,'img\content');
        }
        return $dataFields;
    }
}
