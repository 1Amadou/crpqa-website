<?php
   use Illuminate\Database\Migrations\Migration;
   use Illuminate\Database\Schema\Blueprint;
   use Illuminate\Support\Facades\Schema;
   return new class extends Migration
   {
       public function up(): void
       {
           Schema::table('news', function (Blueprint $table) {
               // SI VOUS AVIEZ MIS 'user_id' ICI PAR ERREUR, CORRIGEZ EN 'created_by_user_id'
               $table->foreignId('created_by_user_id') // NOM CORRECT
                     ->nullable() // Rendez-le nullable pour commencer, pour éviter les erreurs de seeder
                     ->after('is_featured') 
                     ->constrained('users')
                     ->onDelete('set null'); // ou 'cascade'
           });
       }
       public function down(): void
       {
           Schema::table('news', function (Blueprint $table) {
               // Assurez-vous de supprimer la bonne clé étrangère et la bonne colonne
               $table->dropForeign(['created_by_user_id']); // NOM CORRECT
               $table->dropColumn('created_by_user_id');    // NOM CORRECT
           });
       }
   };