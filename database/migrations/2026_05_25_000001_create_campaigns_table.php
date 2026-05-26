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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('cat'); // bencana, pendidikan, kesehatan, air bersih, lingkungan, komunitas
            $table->string('img')->nullable();
            $table->string('location');
            $table->string('fundraiser'); // Penyelenggara/Organisasi
            $table->text('desc');
            $table->bigInteger('target'); // target nominal in Rp (e.g. 50000000)
            $table->bigInteger('collected')->default(0); // collected nominal in Rp
            $table->integer('days'); // duration in days
            $table->date('start_date');
            $table->string('status')->default('pending'); // pending, active, done, rejected
            $table->string('pic_name')->nullable();
            $table->string('pic_phone')->nullable();
            $table->string('social')->nullable();
            $table->json('budget')->nullable(); // json array of budget items: [{'label': 'Food', 'amount': 1000}]
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
