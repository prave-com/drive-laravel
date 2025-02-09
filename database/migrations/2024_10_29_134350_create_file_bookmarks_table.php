<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('file_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_starred')->default(false);
            $table->foreignUuid('file_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['file_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('file_bookmarks', function (Blueprint $table) {
            $table->dropForeign(['file_id']);
        });

        Schema::table('file_bookmarks', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('file_bookmarks');
    }
};
