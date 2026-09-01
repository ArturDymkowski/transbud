<?php

namespace App\Console\Commands;

use App\Models\Delivery;
use App\Models\Driver;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ResetDemoData extends Command
{
    protected $signature = 'demo:reset {--force : Skip the confirmation prompt}';

    protected $description = 'Reset the public demo data to a clean state, without touching login history or is_super_admin accounts';

    public function handle(): int
    {
        if ($this->input->isInteractive() && ! $this->option('force')) {
            $confirmed = $this->confirm(
                'This deletes all demo data (drivers, vehicles, deliveries, contractors, '.
                'goods, uploaded documents, demo users) and reseeds it from scratch. '.
                'login_audit_log and is_super_admin accounts are left untouched. Continue?'
            );

            if (! $confirmed) {
                $this->comment('Aborted.');

                return self::FAILURE;
            }
        }

        $this->info('Clearing uploaded driver/delivery documents...');
        $this->clearDocuments();

        $this->info('Reseeding demo data...');
        Artisan::call('db:seed', ['--class' => DatabaseSeeder::class, '--force' => true]);
        $this->output->write(Artisan::output());

        $this->info('Restoring the Admin role for any surviving super-admin account...');
        $this->restoreSuperAdminRoles();

        $this->info('Demo data reset complete.');

        return self::SUCCESS;
    }

    /**
     * Media rows are deleted through Eloquent, one by one, rather than a raw
     * truncate — spatie/medialibrary hooks Media::deleted to remove the
     * underlying file, and a truncate would silently skip that and leave every
     * uploaded file orphaned on disk. The disk sweep afterwards is a defensive
     * fallback for anything left over from before this command existed.
     */
    private function clearDocuments(): void
    {
        Media::query()
            ->whereIn('model_type', [Driver::class, Delivery::class])
            ->get()
            ->each(fn (Media $media) => $media->delete());

        $this->wipeDisk(Storage::disk('driver_documents'));
        $this->wipeDisk(Storage::disk('delivery_documents'));
    }

    private function wipeDisk(Filesystem $disk): void
    {
        foreach ($disk->directories() as $directory) {
            $disk->deleteDirectory($directory);
        }

        foreach ($disk->files() as $file) {
            $disk->delete($file);
        }
    }

    private function restoreSuperAdminRoles(): void
    {
        User::where('is_super_admin', true)->get()->each(
            fn (User $user) => $user->syncRoles(['Admin'])
        );
    }
}
