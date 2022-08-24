<?php

namespace RachidLaasri\LaravelInstaller\Controllers;

use App\Models\Account;
use App\Models\Client;
use App\Models\GeneralSetting;
use App\Models\VatRate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use RachidLaasri\LaravelInstaller\Helpers\DatabaseManager;
use RachidLaasri\LaravelInstaller\Helpers\InstalledFileManager;

class UpdateController extends Controller
{
    use \RachidLaasri\LaravelInstaller\Helpers\MigrationsHelper;

    /**
     * Display the updater welcome page.
     *
     * @return \Illuminate\View\View
     */
    public function welcome()
    {
        return view('vendor.installer.update.welcome');
    }

    /**
     * Display the updater overview page.
     *
     * @return \Illuminate\View\View
     */
    public function overview()
    {
        $migrations = $this->getMigrations();
        $dbMigrations = $this->getExecutedMigrations();

        try {
            $defaultClient = Client::where('slug', 'walking-customer')->firstOrFail();
            $defaultAccount = Account::where('slug', 'cash-0001')->firstOrFail();
            $defaultVatRate = VatRate::where('slug', 'vat-0')->firstOrFail();
            $defaultAccountSlug = GeneralSetting::where('key', 'default_account_slug')->firstOrFail();
            $defaultClientSlug = GeneralSetting::where('key', 'default_client_slug')->firstOrFail();
            $defaultVatRateSlug = GeneralSetting::where('key', 'default_vat_rate_slug')->firstOrFail();

            return view('vendor.installer.update.overview',
                ['numberOfUpdatesPending' => count($migrations) - count($dbMigrations)]);
        } catch (ModelNotFoundException $exception) {
            return view('vendor.installer.update.overview',
                ['numberOfUpdatesPending' => count($migrations) - count($dbMigrations) + 4]);
        }

        // static 3 for defult seeded rows
    }

    /**
     * Migrate and seed the database.
     *
     * @return \Illuminate\View\View
     */
    public function database()
    {
        $databaseManager = new DatabaseManager;
        $response = $databaseManager->migrateAndSeed();

        // default seed data for the application
        $this->defaultSeedData();

        return redirect()->route('LaravelUpdater::final')
            ->with(['message' => $response]);
    }

    /**
     * @return void
     * Seed the database with the default data.
     */
    public function defaultSeedData()
    {
        // Default account seed in the database
        Account::updateOrCreate(
            ['account_number' => 'CASH-0001', 'slug' => 'cash-0001'],
            [
                'bank_name' => 'Cash',
                'branch_name' => 'Office',
                'account_number' => 'CASH-0001',
                'slug' => 'cash-0001',
                'created_by' => '1',
                'created_at' => now(),
                'updated_at' => now(),
                'date' => now(),
                'note' => null,
            ]);
        // Default client seed in the database
        Client::updateOrCreate(
            ['slug' => 'walking-customer', 'name' => 'Walking Customer'],
            [
                'name' => 'Walking Customer',
                'slug' => 'walking-customer',
                'email' => 'acculance@example.com',
                'phone' => '017000000',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        // Default vat-rate seed in the database
        VatRate::updateOrCreate(
            ['slug' => 'vat-0', 'code' => 'VAT@0'],
            [
                'name' => 'VAT 0%',
                'slug' => 'vat-0',
                'code' => 'VAT@0',
                'rate' => '0.00',
            ]);
        // Default info seed in general settings
        GeneralSetting::updateOrCreate(
            ['key' => 'default_client_slug', 'display_name' => 'Default Client Slug', 'value' => 'walking-customer'],
            ['key' => 'default_client_slug', 'display_name' => 'Default Client Slug', 'value' => 'walking-customer']
        );
        GeneralSetting::updateOrCreate(
            ['key' => 'default_account_slug', 'display_name' => 'Default Account Slug', 'value' => 'cash-0001'],
            ['key' => 'default_account_slug', 'display_name' => 'Default Account Slug', 'value' => 'cash-0001']
        );
        GeneralSetting::updateOrCreate(
            ['key' => 'default_vat_rate_slug', 'display_name' => 'Default Vat Rate Slug', 'value' => 'vat-0'],
            ['key' => 'default_vat_rate_slug', 'display_name' => 'Default Vat Rate Slug', 'value' => 'vat-0']
        );
    }

    /**
     * Update installed file and display finished view.
     *
     * @param  InstalledFileManager  $fileManager
     * @return \Illuminate\View\View
     */
    public function finish(InstalledFileManager $fileManager)
    {
        $fileManager->update();

        return view('vendor.installer.update.finished');
    }
}
