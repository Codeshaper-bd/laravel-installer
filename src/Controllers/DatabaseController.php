<?php

namespace RachidLaasri\LaravelInstaller\Controllers;

use Illuminate\Routing\Controller;
use RachidLaasri\LaravelInstaller\Helpers\DatabaseManager;
use App\Settings\GeneralSettings;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseController extends Controller
{
    /**
     * @var DatabaseManager
     */
    private $databaseManager;

    /**
     * @param DatabaseManager $databaseManager
     */
    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }

    /**
     * Migrate and seed the database.
     *
     * @return \Illuminate\View\View
     */
    public function database()
    {
        $appName = request()->query('app_name');
        $adminEmail = request()->query('admin_email');
        $adminName = request()->query('admin_name');
        $adminPassword = request()->query('admin_password');

        $response = $this->databaseManager->migrateAndSeed();

        $this->saveGeneralSettings($appName);
        $this->updateSuperAdminCredentials($adminName, $adminEmail, $adminPassword);

        return redirect()->route('LaravelInstaller::final')
            ->with(['message' => $response]);
    }

    private function saveGeneralSettings(string $appName): void
    {
        $settings = new GeneralSettings();
        $settings->name = $appName;
        $settings->save();
    }

    private function updateSuperAdminCredentials(string $name, string $email, string $password): void
    {
        $superAdmin = User::role('super-admin')->first();

        if ($superAdmin) {
            $superAdmin->name = $name;
            $superAdmin->email = $email;
            $superAdmin->password = Hash::make($password);
            $superAdmin->save();

            if ($superAdmin->contact) {
                $superAdmin->contact->display_name = $name;
                $superAdmin->contact->save();
            }
        }
    }
}
