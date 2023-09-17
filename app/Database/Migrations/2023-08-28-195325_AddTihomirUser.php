<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTihomirUser extends Migration
{
    public function up()
    {
        $data = [
            'username' => 'tihomir',
            'email' => 'newuser@example.com',
            'password' => password_hash('0000', PASSWORD_DEFAULT),
        ];

        // Using the model to insert data
        $this->db->table('users')->insert($data);
    }

    public function down()
    {
        //
    }
}
