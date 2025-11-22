<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSocialNetworksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_networks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); 
            $table->string('logo_url')->nullable();
            $table->timestamps();
        });

        // Insertar las redes sociales predeterminadas
        DB::table('social_networks')->insert([
            [
                'name' => 'Instagram',
                'logo_url' => 'https://cdn-icons-png.flaticon.com/512/174/174855.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Discord',
                'logo_url' => 'https://cdn-icons-png.flaticon.com/512/5968/5968756.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Telegram',
                'logo_url' => 'https://cdn-icons-png.flaticon.com/512/2111/2111646.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'TikTok',
                'logo_url' => 'https://cdn-icons-png.flaticon.com/512/3046/3046121.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Twitter',
                'logo_url' => 'https://cdn-icons-png.flaticon.com/512/733/733579.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Twitch',
                'logo_url' => 'https://cdn-icons-png.flaticon.com/512/5968/5968819.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('social_networks');
    }
}
