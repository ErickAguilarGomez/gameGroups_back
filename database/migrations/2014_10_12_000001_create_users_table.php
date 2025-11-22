<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->date('birthdate')->nullable();
            $table->string('photo_url')->nullable();
            $table->enum('photo_status', ['pending', 'approved', 'rejected'])->nullable();
            $table->text('photo_rejection_reason')->nullable();
            $table->enum('account_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('rejection_reason')->nullable();
            $table->string('nickname')->nullable();
            $table->timestamp('banned_at')->nullable();
            $table->text('ban_reason')->nullable();
            $table->unsignedBigInteger('banned_by')->nullable();
            $table->foreignId('social_network_id')->nullable()->constrained('social_networks');
            $table->foreignId('group_id')->nullable()->constrained('groups');
            $table->foreignId('role_id')->default(2)->constrained('roles');
            $table->timestamp('last_seen')->nullable();
            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
