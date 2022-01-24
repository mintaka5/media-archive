<?php
class UCI_ImageArchive_Upload extends HTTP_Upload {
    private $file_data = null;
    private $file_parts = null;
    private $storage_base_path = null;
    private $target_filename = null;
    private $db_filename = null;
    private $storage_path = null;
    private $basename = null;

    const DEFAULT_JPEG_EXTENSION = 'jpg';

    public function __construct($base_path, $field_name = "file") {
        parent::HTTP_Upload("en");

        $this->setBasePath($base_path);

        $this->file_data = $this->getFiles($field_name);
    }

    public function process() {
        if($this->file_data->isValid()) {

            $this->file_parts = pathinfo($this->file_data->getProp("name"));

            /**
             * Handle TIFF files, converting them to Hi-res JPEG files
             */
            if(stristr($this->file_parts['extension'], 'tif')) { // what if it's a TIFF file
                $this->tiffProcess();
            } else { // primarily handle JPEG's
                $this->jpegProcess();
            }
        }
    }

    private function makeStoragePath() {
        $pathinfo = pathinfo($this->getDBFilename());
        $full_path = $this->getBasePath() . $pathinfo['dirname'];

        if(!file_exists($full_path)) {
            try {
                $made = mkdir($full_path, 0777, true);

                if(!$made) {
                    error_log("Failed to create the directory, " . $full_path, 0);
                }
            } catch(Exception $e) {
                error_log($e->getMessage(), 0);
                die(-1);
            }
        }

        return $full_path;
    }

    private function tiffProcess() {
        $this->setDBfilename(DBO_Asset::generateFilename($this->file_parts['filename'] . "." . self::DEFAULT_JPEG_EXTENSION));

        $this->setStoragePath($this->makeStoragePath());

        $jpgFilename = $this->getBasePath() . $this->getDBFilename();

        try {
            // make it a JPEG
            exec(APP_IMAGEMAGICK_PATH . DIRECTORY_SEPARATOR . "convert " . $this->file_data->getProp("tmp_name") . " " . $jpgFilename);
        } catch(Exception $e) {
            error_log($e->getMessage(), 0);
        }

        $this->setTargetFilename($jpgFilename);
    }

    private function jpegProcess() {
        $this->setDBfilename(DBO_Asset::generateFilename($this->file_parts['filename'] . "." . self::DEFAULT_JPEG_EXTENSION));

        /**
         * Get path information, so we can pass the upload into it's corresponding filename path
         * @var array
         */
        $pathinfo = pathinfo($this->getDBFilename());

        $this->file_data->setName($pathinfo['basename']);

        $this->setStoragePath($this->makeStoragePath());

        $this->setTargetFilename($this->getBasePath() . $this->getDBFilename());

        try {
            /**
             * Move temporary JPEG file to storage location
             * @var unknown_type
             */
            $moved = $this->file_data->moveTo($this->getStoragePath());
            if(PEAR::isError($moved)) {
                error_log("Failed to move file to destination: " . $moved->getMessage(), 0);
            }
        } catch(Exception $e) {
            error_log($e->getMessage(), 0);
        }
    }

    public function getMimeType() {
        return MIME_Type::autodetect($this->getTargetFilename());
    }

    private function setDBfilename($filename) {
        $this->db_filename = $filename;
    }

    public function getDBFilename() {
        return $this->db_filename;
    }

    public function setTargetFilename($filename) {
        $this->target_filename = $filename;
    }

    public function getTargetFilename() {
        return $this->target_filename;
    }

    public function setStoragePath($path) {
        $this->storage_path = $path;
    }

    public function getStoragePath() {
        return $this->storage_path;
    }

    public function setBasePath($path) {
        $this->storage_base_path = $path;
    }

    public function getBasePath() {
        return $this->storage_base_path;
    }
}