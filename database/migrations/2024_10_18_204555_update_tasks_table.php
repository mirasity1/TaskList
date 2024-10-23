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
    Schema::table('tasks', function (Blueprint $table) {
        $table->boolean('is_public')->default(false);
        $table->string('uuid')->nullable()->unique();
        $table->softDeletes();
        $table->timestamp('due_date')->nullable();
        $table->integer('priority')->default(0);
        $table->integer('progress')->default(0);
        $table->json('metadata')->nullable();
        $table->json('tags')->nullable();
        $table->json('options')->nullable();
        $table->json('settings')->nullable();
        $table->json('extra')->nullable();
        $table->json('data')->nullable();
        $table->json('notes')->nullable();
        $table->json('details')->nullable();
        $table->json('content')->nullable();
        $table->json('attributes')->nullable();
        $table->json('fields')->nullable();
    });
}

public function down()
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->dropColumn([
            'is_public', 'uuid', 'due_date', 'priority', 'progress',
            'metadata', 'tags', 'options', 'settings', 'extra', 'data',
            'notes', 'details', 'content', 'attributes', 'fields'
        ]);
    });
}

};
