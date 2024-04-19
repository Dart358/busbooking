<?php 

namespace App\Controllers;

class DatabaseTest extends BaseController {

    public function index() {
        $db = \Config\Database::connect();
        $tables = $db->listTables();
 
        // loop through the table and display
        foreach ($tables as $table) {
            echo $table . "<br>";
        }
    }
}