<?php
/*
 * VipsSolution.php - Vips solution class for Stud.IP
 * Copyright (c) 2014  Elmar Ludwig
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

class VipsSolution extends SimpleORMap
{
    private $files;
    private $files_dirty;

    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        if (empty($config['db_table'])) {
            $config['db_table'] = 'vips_solution';
        }

        $config['default_values']['corrected'] = 0;
        $config['additional_fields']['files']['get'] = 'getFiles';
        $config['serialized_fields']['response'] = 'JSONArrayObject';
        $config['serialized_fields']['options'] = 'JSONArrayObject';

        $config['belongs_to']['exercise'] = [
            'class_name'  => 'Exercise',
            'foreign_key' => 'exercise_id'
        ];
        $config['belongs_to']['assignment'] = [
            'class_name'  => 'VipsAssignment',
            'foreign_key' => 'assignment_id'
        ];
        $config['belongs_to']['user'] = [
            'class_name'  => 'User',
            'foreign_key' => 'user_id'
        ];

        parent::configure($config);
    }

    /**
     * Delete entry from the database.
     */
    public function delete()
    {
        VipsFileRef::deleteBySQL('solution_id = ?', [$this->id]);

        return parent::delete();
    }

    /**
     * Store solution into the database.
     */
    public function store()
    {
        $result = parent::store();

        if ($this->files_dirty) {
            foreach ($this->files as $file) {
                $file_ref = new VipsFileRef([$file->id, $this->id]);
                $file_ref->store();
            }
        }

        return $result;
    }

    /**
     * Set value for the "exercise" relation (to avoid SORM errors).
     */
    public function setExercise(Exercise $exercise)
    {
        $this->exercise_id = $exercise->id;
        $this->relations['exercise'] = $exercise;
    }

    /**
     * Get array of submitted answers for this solution (PHP array).
     */
    public function getResponse()
    {
        return $this->content['response']->getArrayCopy();
    }

    public function addFile($file)
    {
        $this->files[] = $file;
        $this->files_dirty = true;
    }

    public function getFiles()
    {
        if (!isset($this->files)) {
            $sql = 'JOIN vips_file_ref ON vips_file_ref.file_id = vips_file.id
                    WHERE vips_file_ref.solution_id = ? ORDER BY name';
            $this->files = VipsFile::findBySQL($sql, [$this->id]);
        }

        return $this->files;
    }
}
