<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Fluent;

/**
 * Class AddClickUpTokenToUsersTable
 *
 * Adds a column for the ClickUp API token to your users table.
 */
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
            fn (Blueprint $table): ColumnDefinition => $table->string('clickup_token', 1024)
                      ->after('password')
                      ->nullable()
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
            fn (Blueprint $table): Fluent => $table->dropColumn('clickup_token')
        );
    }
}
