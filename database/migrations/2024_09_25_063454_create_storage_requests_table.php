<?php

use App\Enums\StorageRequestStatus;
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
        Schema::create('storage_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_quota');
            $table->string('reason', 200);
            $table->enum('status', [StorageRequestStatus::PENDING->value, StorageRequestStatus::APPROVED->value, StorageRequestStatus::REJECTED->value])->default(StorageRequestStatus::PENDING->value);
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('storage_requests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('storage_requests');
    }
};
