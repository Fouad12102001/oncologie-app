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
        Schema::create('cycles_traitement', function(Blueprint $table){

    $table->id();

    $table->foreignId('patient_id');

    $table->foreignId('protocole_id');

    $table->integer('numero_cycle');

    $table->date('date_prevue');

    $table->date('date_effective')->nullable();

    $table->enum('statut',[
        'planifie',
        'effectue',
        'annule'
    ]);

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cycle_traitements');
    }
};
