<?php
/*
 * VipsFileRef.php - Vips test class for Stud.IP
 * Copyright (c) 2018  Elmar Ludwig
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

class VipsFileRef extends SimpleORMap
{
    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'vips_file_ref';

        $config['belongs_to']['file'] = [
            'class_name'  => 'VipsFile',
            'foreign_key' => 'file_id'
        ];
        $config['belongs_to']['solution'] = [
            'class_name'  => 'VipsSolution',
            'foreign_key' => 'solution_id'
        ];

        parent::configure($config);
    }

    /**
     * Delete entry from the database.
     */
    public function delete()
    {
        $ref_count = self::countBySql('file_id = ?', [$this->file_id]);

        if ($ref_count == 1) {
            $this->file->delete();
        }

        return parent::delete();
    }
}
