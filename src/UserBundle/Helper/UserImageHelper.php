<?php

namespace UserBundle\Helper;

/**
 * Помоoник для обработки изображений
 *
 * @author Alexander
 */
class UserImageHelper
{

    /**
     * Пусть сохранения картинок
     * @var string
     */
    protected $storePath = null;

    public function __construct($storePath)
    {
        $this->storePath = $storePath;
    }

    /**
     * Обрабатываем картинку для пользователя в формате BASE64 и сохраняем
     * @param string $content
     * @return boolean
     * @throws \Exception
     */
    public function processUserImage($base64Content, $prefix = '', $prevImage = null)
    {
        try {
            
            if ($prevImage) {
                $this->unlinkFile($prevImage);
            }

            $regexPattern = 'data\:image\/([a-zA-Z0-9]+?)\;base64\,';
            if (preg_match('/^' . $regexPattern . '/', $base64Content, $matches)) {

                $fileFormat = $matches[1];
                $content = base64_decode(preg_replace('/^(' . $regexPattern . ')/', '', $base64Content));

                $result = $this->storeFile($content, $fileFormat, $prefix);


                return $result;
            } else {
                throw new \Exception('File format not provided!');
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Сохраняем файл на диск и возвращаем имя файла
     * @param string $content
     * @param string $fileFormat
     */
    protected function storeFile($content, $fileFormat, $prefix = '')
    {
        if (!is_dir($this->storePath)) {

            mkdir($this->storePath);
        }

        $fileName = uniqid($prefix, true) . '.' . $fileFormat;
        file_put_contents($this->storePath . $fileName, $content);

        return $fileName;
    }

    public function unlinkFile($file)
    {
        unlink(realpath($this->storePath . $file));
    }

}
