<!-- placeholder -->
<form method="post" enctype="multipart/form-data" action="<?php echo $this->manager->friendlyAction('testing', 'process'); ?>">
    <input type="file" name="myFile" id="myFile" />
    <button type="submit">Upload</button>
</form>