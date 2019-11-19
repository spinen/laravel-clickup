<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClickUpTokenToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'users',
            function (Blueprint $table) {
                $table->string('clickup_token')
                      ->after('password')
                      ->nullable();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'users',
            function (Blueprint $table) {
                $table->dropColumn('clickup_token');
            }
        );
    }
}
