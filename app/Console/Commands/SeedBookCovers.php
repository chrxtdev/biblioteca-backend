<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SeedBookCovers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:seed-covers {--force : Force overwrite existing covers}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign random seed covers to books without covers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cover seeding...');

        $seedPath = 'seed_covers';
        if (!Storage::disk('public')->exists($seedPath)) {
            $this->error("Seed directory '$seedPath' not found in public disk!");
            return 1;
        }

        $files = Storage::disk('public')->files($seedPath);
        if (empty($files)) {
            $this->error("No images found in '$seedPath'!");
            return 1;
        }

        $query = Book::query();
        if (!$this->option('force')) {
            $query->whereNull('cover_path')->orWhere('cover_path', '');
        }

        $books = $query->get();
        $count = $books->count();
        $this->info("Found $count books to update.");

        if ($count === 0) {
            $this->warn("No books to update. Use --force to overwrite existing covers.");
            return 0;
        }

        $bar = $this->output->createProgressBar($count);

        foreach ($books as $book) {
            // Pick random seed file
            $seedFile = $files[array_rand($files)];
            $extension = pathinfo($seedFile, PATHINFO_EXTENSION);
            
            // Create unique filename for this book
            $newFilename = 'livros_capas/' . Str::uuid() . '.' . $extension;
            
            // Ensure target directory exists
            if (!Storage::disk('public')->exists('livros_capas')) {
                Storage::disk('public')->makeDirectory('livros_capas');
            }

            // Copy
            Storage::disk('public')->copy($seedFile, $newFilename);

            $book->update(['cover_path' => $newFilename]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Covers assigned successfully!');
    }
}
