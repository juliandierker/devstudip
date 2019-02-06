<?php
/*
 * VipsFile.php - Vips test class for Stud.IP
 * Copyright (c) 2014  Elmar Ludwig
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

class VipsFile extends SimpleORMap
{
    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'vips_file';

        $config['belongs_to']['user'] = [
            'class_name'  => 'User',
            'foreign_key' => 'user_id'
        ];

        parent::configure($config);
    }

    /**
     * Create a new VipsFile instance for this upload.
     */
    public static function createWithFile($name, $path, $user_id)
    {
        $file = new VipsFile();
        $file->user_id = $user_id;
        $file->mime_type = get_mime_type($name);
        $file->name = basename($name);
        $file->size = filesize($path);
        $file->created = date('Y-m-d H:i:s');
        $file->store();

        if (move_uploaded_file($path, $file->getFilePath(true))) {
            return $file;
        }

        $file->delete();
        return null;
    }

    /**
     * Return the absolute path of an uploaded file. The uploaded files
     * are organized in sub-folders of UPLOAD_PATH to avoid performance
     * problems with large directories.
     */
    public function getFilePath($create = false)
    {
        if ($this->id === null) {
            return null;
        }

        $dir = $GLOBALS['UPLOAD_PATH'] . '/' . substr($this->id, 0, 2);

        if ($create && !file_exists($dir)) {
            mkdir($dir);
        }

        return $dir . '/' . $this->id;
    }

    public function delete()
    {
        $path = $this->getFilePath();

        if (file_exists($path)) {
            unlink($path);
        }

        return parent::delete();
    }
}
