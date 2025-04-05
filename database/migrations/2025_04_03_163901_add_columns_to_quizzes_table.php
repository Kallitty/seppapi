<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToQuizzesTable extends Migration
{
    public function up()
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->boolean('is_visible')->default(false);
            $table->unsignedTinyInteger('premium')->default(0);
            $table->unsignedTinyInteger('status')->default(0); 
        });
    }

    public function down()
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn(['is_visible', 'premium', 'status']);
        });
    }
}