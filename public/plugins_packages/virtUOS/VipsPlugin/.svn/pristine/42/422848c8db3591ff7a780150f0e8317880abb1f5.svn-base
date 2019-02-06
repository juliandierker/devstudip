<?php

class VipsFileUpload extends Migration
{
    function description()
    {
        return 'add vips_file table for file uploads in exercises';
    }

    function up()
    {
        $db = DBManager::get();

        // create vips_file table
        $db->exec("CREATE TABLE vips_file (
                   id VARCHAR(32) COLLATE latin1_bin NOT NULL,
                   user_id VARCHAR(32) COLLATE latin1_bin NOT NULL,
                   mime_type VARCHAR(255) NOT NULL DEFAULT '',
                   name VARCHAR(255) NOT NULL,
                   size INT(11) NOT NULL,
                   created TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
                   PRIMARY KEY (id))");

        // create vips_file_ref table
        $db->exec("CREATE TABLE vips_file_ref (
                   file_id VARCHAR(32) COLLATE latin1_bin NOT NULL,
                   solution_id INT(11) NOT NULL,
                   PRIMARY KEY (file_id, solution_id))");
    }

    function down()
    {
        $db = DBManager::get();

        $stmt = $db->query('SELECT id FROM vips_file');
        $file_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // remove uploaded files
        foreach ($file_ids as $file_id) {
            $path = $GLOBALS['UPLOAD_PATH'] . '/' . substr($file_id, 0, 2) . '/' . $file_id;

            if (file_exists($path)) {
                unlink($path);
            }
        }

        // drop vips_file, vips_file_ref table
        $db->exec('DROP TABLE vips_file, vips_file_ref');
    }
}
