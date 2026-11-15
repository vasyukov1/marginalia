<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->foreignId('author_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('title_key')->nullable();
            $table->unsignedInteger('pages')->nullable();
            $table->string('genre')->nullable();
        });

        foreach (DB::table('books')->get() as $book) {
            $name = Str::squish($book->author_name);
            $nameKey = Str::lower($name);
            $authorId = DB::table('authors')->where('name_key', $nameKey)->value('id');

            if ($authorId === null) {
                $authorId = DB::table('authors')->insertGetId([
                    'name' => $name,
                    'name_key' => $nameKey,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('books')->where('id', $book->id)->update([
                'author_id' => $authorId,
                'title_key' => Str::lower(Str::squish($book->title)),
            ]);
        }

        Schema::table('books', function (Blueprint $table) {
            $table->unsignedBigInteger('author_id')->nullable(false)->change();
            $table->string('title_key')->nullable(false)->change();
            $table->unique(['author_id', 'title_key']);
            $table->dropColumn('author_name');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('author_name')->nullable();
        });

        foreach (DB::table('books')->get() as $book) {
            DB::table('books')->where('id', $book->id)->update([
                'author_name' => DB::table('authors')->where('id', $book->author_id)->value('name'),
            ]);
        }

        Schema::table('books', function (Blueprint $table) {
            $table->dropUnique(['author_id', 'title_key']);
            $table->dropForeign(['author_id']);
            $table->dropColumn(['author_id', 'title_key', 'pages', 'genre']);
        });
    }
};
