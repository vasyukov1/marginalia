<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class AuthorPortraitSeeder extends Seeder
{
    public function run(): void
    {
        $portraits = json_decode(file_get_contents(__DIR__.'/library-authors.json'), true, flags: JSON_THROW_ON_ERROR);

        foreach ($portraits as $portrait) {
            $author = Author::where('name_key', Str::lower($portrait['name']))->first();
            if (! $author || $author->avatar_path || empty($portrait['portrait_url'])) {
                continue;
            }

            $path = null;
            try {
                $response = Http::withUserAgent('MarginaliaLibrary/1.0 (educational library project)')
                    ->connectTimeout(5)->timeout(30)->get($portrait['portrait_url'])->throw();
                $mime = getimagesizefromstring($response->body())['mime'] ?? null;
                $extension = match ($mime) {
                    'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif',
                    default => throw new RuntimeException('Unsupported portrait format'),
                };
                $path = 'authors/'.Str::uuid().'.'.$extension;
                if (! Storage::disk('public')->put($path, $response->body())) {
                    throw new RuntimeException('Could not store portrait');
                }
                $author->avatar_path = $path;
                $author->avatar_source_url = $portrait['portrait_source'];
                $author->avatar_credit = $portrait['portrait_credit'];
                $author->save();
                $this->command?->info($author->name);
            } catch (Throwable $exception) {
                if ($path !== null) {
                    Storage::disk('public')->delete($path);
                }
                $this->command?->warn($author->name.': '.$exception->getMessage());
            }
        }
    }
}
