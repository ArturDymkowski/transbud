<?php

use App\Models\Delivery;
use App\Models\Driver;
use App\Models\LoginAuditLog;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

/**
 * demo:reset is meant to run unattended on a cron — these tests pin down the two
 * things it must never touch (login_audit_log, is_super_admin accounts) alongside
 * the things it must actually clean up (demo records, soft-deleted leftovers,
 * uploaded documents), see App\Console\Commands\ResetDemoData.
 */
test('login_audit_log survives a reset untouched', function () {
    $log = LoginAuditLog::factory()->create();

    Artisan::call('demo:reset', ['--force' => true]);

    expect(LoginAuditLog::find($log->id))->not->toBeNull();
});

test('an is_super_admin account survives a reset with its Admin role restored', function () {
    Artisan::call('db:seed', ['--force' => true]);

    $owner = User::factory()->create(['is_super_admin' => true]);
    // Deliberately no role assigned yet — the command must (re-)assign Admin, not
    // just leave whatever role happened to survive.
    expect($owner->roles)->toBeEmpty();

    Artisan::call('demo:reset', ['--force' => true]);

    $owner->refresh();
    expect($owner->exists)->toBeTrue()
        ->and($owner->is_super_admin)->toBeTrue()
        ->and($owner->hasRole('Admin'))->toBeTrue();
});

test('disposable demo users, including already soft-deleted ones, are gone after a reset', function () {
    Artisan::call('db:seed', ['--force' => true]);

    $junkUser = User::factory()->create(['is_super_admin' => false]);
    $trashedUser = User::factory()->create(['is_super_admin' => false]);
    $trashedUser->delete();

    Artisan::call('demo:reset', ['--force' => true]);

    // Checked by email, not id — truncate() resets SQLite's autoincrement
    // sequence, so a same-id lookup can't tell "still there" from a freshly
    // reseeded row coincidentally landing on the same id.
    expect(User::withTrashed()->where('email', $junkUser->email)->exists())->toBeFalse()
        ->and(User::withTrashed()->where('email', $trashedUser->email)->exists())->toBeFalse()
        // the seeded admin@admin.com demo account must still be there, freshly recreated
        ->and(User::where('email', 'admin@admin.com')->exists())->toBeTrue();
});

test('demo domain data is wiped and reseeded back to a consistent count', function () {
    Artisan::call('db:seed', ['--force' => true]);
    $baselineDriverCount = Driver::count();

    $junkDriver = Driver::factory()->create();
    $junkDelivery = Delivery::factory()->create();

    Artisan::call('demo:reset', ['--force' => true]);

    // Checked by a unique business field, not id: truncate() resets SQLite's
    // autoincrement sequence, so a freshly reseeded row can legitimately land on
    // the exact same id the deleted junk row had — a same-id lookup can't tell
    // "still there" from "coincidentally reused".
    //
    // Driver count is deterministic (DriverSeeder creates a fixed number), Delivery
    // count isn't (DeliverySeeder randomises how many per month on purpose, for
    // demo variety) — so deliveries are only checked for "did get reseeded", not
    // an exact count.
    expect(Driver::withTrashed()->where('pesel', $junkDriver->pesel)->exists())->toBeFalse()
        ->and(Driver::count())->toBe($baselineDriverCount)
        ->and(Delivery::where('number', $junkDelivery->number)->exists())->toBeFalse()
        ->and(Delivery::count())->toBeGreaterThan(0);
});

test('uploaded driver documents are removed from disk and the database on reset', function () {
    Storage::fake('driver_documents');
    Artisan::call('db:seed', ['--force' => true]);

    $driver = Driver::factory()->create();
    $file = UploadedFile::fake()->image('license-front.jpg');
    $driver->addMedia($file->getPathname())
        ->usingName('license-front.jpg')
        ->preservingOriginal()
        ->toMediaCollection(Driver::MEDIA_DRIVING_LICENSE_FRONT);

    $media = $driver->getFirstMedia(Driver::MEDIA_DRIVING_LICENSE_FRONT);
    $storedPath = $media->id.'/'.$media->file_name;
    Storage::disk('driver_documents')->assertExists($storedPath);

    Artisan::call('demo:reset', ['--force' => true]);

    expect($driver->fresh()?->getFirstMedia(Driver::MEDIA_DRIVING_LICENSE_FRONT))->toBeNull();
    Storage::disk('driver_documents')->assertMissing($storedPath);
});

test('demo:reset asks for confirmation interactively unless --force is passed', function () {
    Artisan::call('db:seed', ['--force' => true]);

    $this->artisan('demo:reset')
        ->expectsConfirmation(
            'This deletes all demo data (drivers, vehicles, deliveries, contractors, '.
            'goods, uploaded documents, demo users) and reseeds it from scratch. '.
            'login_audit_log and is_super_admin accounts are left untouched. Continue?',
            'no'
        )
        ->assertFailed();
});
