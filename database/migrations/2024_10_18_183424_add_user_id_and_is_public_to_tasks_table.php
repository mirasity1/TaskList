<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdAndIsPublicToTasksTable extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Adiciona o campo user_id e o campo is_public
            $table->unsignedBigInteger('user_id'); // Adiciona o campo user_id
            $table->boolean('is_public')->default(false); // Adiciona o campo is_public
            // Adiciona uma chave estrangeira para associar as tarefas aos usuários
            $table->integer('order')->default(0);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'is_public']);
        });
    }
}
