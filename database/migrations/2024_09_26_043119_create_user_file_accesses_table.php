<?php

use App\Enums\PermissionType;
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
        Schema::create('user_file_accesses', function (Blueprint $table) {
            $table->id();
            $table->enum('permission_type', [PermissionType::READ->value, PermissionType::READ_WRITE->value])->default(PermissionType::READ->value);
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
        Schema::table('user_file_accesses', function (Blueprint $table) {
            $table->dropForeign(['file_id']);
        });

        Schema::table('user_file_accesses', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('user_file_accesses');
    }
};
