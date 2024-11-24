<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('role_as')
                ->default(0)
                ->nullable()
                ->after('remember_token')
                ->comment('0=users, 1=admin');
        });
    }

    /**
     * Reverse the migrations.
     */
     public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role_as');
        });
    }
};
