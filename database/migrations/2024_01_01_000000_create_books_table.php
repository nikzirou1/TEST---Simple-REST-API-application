<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the books table with all required columns.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();                          // auto-increment primary key
            $table->string('title');               // book title
            $table->string('publisher');           // publishing company
            $table->string('author');              // author full name
            $table->string('genre', 100);          // literary genre
            $table->date('publication_date');      // first publication date
            $table->unsignedInteger('word_count'); // total word count
            $table->decimal('price', 10, 2);       // price in USD (DECIMAL avoids float rounding)
            $table->timestamps();                  // created_at / updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
