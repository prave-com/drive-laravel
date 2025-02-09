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
        Schema::create('folder_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_starred')->default(false);
            $table->foreignUuid('folder_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['folder_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('folder_bookmarks', function (Blueprint $table) {
            $table->dropForeign(['folder_id']);
        });

        Schema::table('folder_bookmarks', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('folder_bookmarks');
    }
};
