<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('avatar_path')->nullable();
        });

        // Для старых записей используем создателя первой книги автора.
        foreach (DB::table('authors')->orderBy('id')->get() as $author) {
            $creatorId = DB::table('books')->where('author_id', $author->id)
                ->whereNotNull('created_by_user_id')->orderBy('id')->value('created_by_user_id');
            DB::table('authors')->where('id', $author->id)->update(['created_by_user_id' => $creatorId]);
        }
    }

    public function down(): void
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by_user_id');
            $table->dropColumn('avatar_path');
        });
    }
};
