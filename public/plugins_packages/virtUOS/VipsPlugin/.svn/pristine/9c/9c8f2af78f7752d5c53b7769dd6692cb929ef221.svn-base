<?php
/*
 * VipsExerciseRef.php - Vips exercise reference class for Stud.IP
 * Copyright (c) 2016  Elmar Ludwig
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

class VipsExerciseRef extends SimpleORMap
{
    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'vips_exercise_ref';

        $config['belongs_to']['exercise'] = [
            'class_name'  => 'Exercise',
            'foreign_key' => 'exercise_id'
        ];
        $config['belongs_to']['test'] = [
            'class_name'  => 'VipsTest',
            'foreign_key' => 'test_id'
        ];

        parent::configure($config);
    }

    /**
     * Set value for the "exercise" relation (to avoid SORM errors).
     */
    public function setExercise($exercise)
    {
        $this->exercise_id = $exercise->id;
        $this->relations['exercise'] = $exercise;
    }
}
