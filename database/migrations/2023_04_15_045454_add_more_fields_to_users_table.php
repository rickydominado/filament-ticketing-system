<?php

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
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'firstname');
            $table->string('lastname')->after('name');
            $table->string('email')->nullable()->change();
            $table->after('email', function ($table) {
                $table->string('username');
                $table->boolean('is_admin')->default(false);
                $table->string('address');
                $table->string('mobile_number');
                $table->string('profile_photo_path')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'firstname',
                'lastname',
                'username',
                'is_admin',
                'address',
                'mobile_number',
                'profile_photo_path',
            ]);
        });
    }
};
