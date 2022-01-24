<?php
switch(Ode_Manager::getInstance()->getMode()) {
    default:

        break;
    case 'process':
        Util::debug($_FILES);

        $uploader = new UCI_ImageArchive_Upload(IMAGE_STORAGE_PATH, 'myFile');
        $uploader->process();

        $uuid = DBO_Asset::addFullUpload($uploader->getDBFilename(), $uploader->getMimeType(), '3a4b07a8-09bb-11e5-b025-002590274214');

        Util::debug($uuid);
        break;
}
?>
