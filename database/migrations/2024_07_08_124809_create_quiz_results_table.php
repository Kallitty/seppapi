<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizResultsTable extends Migration
{
    public function up()
    {
        Schema::create('quiz_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('quiz_id'); // Ensure this is unsigned
            $table->integer('score');
            $table->integer('correct_answers');
            $table->integer('wrong_answers');
            $table->decimal('correct_answers_percentage', 5, 2);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('quiz_id')->references('id')->on('quiz_attempts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('quiz_results');
    }
}



