<?php

namespace RachidLaasri\LaravelInstaller\Controllers;

use Illuminate\Routing\Controller;
use RachidLaasri\LaravelInstaller\Helpers\DatabaseManager;
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
        $adminEmail = request()->query('admin_email');
        $adminName = request()->query('admin_name');
        $adminPassword = request()->query('admin_password');

        $response = $this->databaseManager->migrateAndSeed();

        $this->updateSuperAdminCredentials($adminName, $adminEmail, $adminPassword);

        return redirect()->route('LaravelInstaller::final')
            ->with(['message' => $response]);
    }

    private function updateSuperAdminCredentials(string $name, string $email, string $password): void
    {
        $superAdmin = User::where('account_role', 1)->first();

        if ($superAdmin) {
            $superAdmin->name = $name;
            $superAdmin->email = $email;
            $superAdmin->password = Hash::make($password);
            $superAdmin->save();

            // if ($superAdmin->contact) {
            //     $superAdmin->contact->display_name = $name;
            //     $superAdmin->contact->save();
            // }
        }
    }
}
