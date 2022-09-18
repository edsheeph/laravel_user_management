<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->string('desscription');
            $table->boolean("visible")->default(0);
            $table->timestamps();
        });

        DB::table('user_roles')->insert([
            [
                'desscription' => 'System Administrator',
                'visible' => 1,
            ],
            [
                'desscription' => 'Administrator',
                'visible' => 1,
            ],
            [
                'desscription' => 'Registrar',
                'visible' => 1,
            ],
            [
                'desscription' => 'Academic',
                'visible' => 1,
            ],
            [
                'desscription' => 'Accounting',
                'visible' => 1,
            ],
            [
                'desscription' => 'Cashier',
                'visible' => 1,
            ],
            [
                'desscription' => 'Faculty',
                'visible' => 1,
            ],
            [
                'desscription' => 'Student',
                'visible' => 1,
            ],
            [
                'desscription' => 'Applicant',
                'visible' => 1,
            ],
            [
                'desscription' => 'Guest',
                'visible' => 0,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_roles');
    }
}
