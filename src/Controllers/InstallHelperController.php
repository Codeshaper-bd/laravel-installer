<?php

namespace RachidLaasri\LaravelInstaller\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InstallHelperController extends Controller
{
    /**
     * Display the purchase code verify page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function getPurchaseCodeVerifyPage()
    {
        return view('vendor.installer.verify');
    }

    /**
     * Verify purchase code and store info.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse | \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function verifyPurchaseCode(Request $request)
    {
        // validate request
        $validated = $this->validate($request, [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'purchase_code' => 'required|string|max:36|min:36',
        ]);

        try {
            if (empty(config('installer.item_id'))) {
                throw new Exception('CODESHAPER_ITEM_ID not found in .env');
            }
            // check from owr server
            $verify = Http::acceptJson()
                ->post(config('installer.site_url').'/api/envato-buyers', $validated + [
                        'item_id' => config('installer.item_id'),
                        'details' => [
                            'item_id' => config('installer.item_id'),
                            'client_ip' => $request->ip(),
                            'installed_at' => now(),
                            'server' => $request->server(),
                        ],
                    ]);

            $response = $verify->json();

            if ($verify->failed()) {
                return back()->withErrors([
                    'purchase_code' => $response['message'],
                ])->withInput();
            }

            $verifiedLogFile = storage_path('verified');
            $dateStamp = date('Y/m/d h:i:sa');
            if (!File::exists($verifiedLogFile)) {
                $message = trans('installer_messages.purchase_code.verified_msg').$dateStamp."\n";
                try {
                    File::put($verifiedLogFile, $message);
                } catch (Exception $e) {
                    Log::error($e->getMessage());
                    return back()->withErrors([
                        'purchase_code' => 'Please make sure \'storage/\' folder is writable.',
                    ])->withInput();
                }
            }

            return view('vendor.installer.welcome');
        } catch (Exception $ex) {
            // print the error so the user knows what's wrong
            return back()->with('msg', $ex->getMessage())->withInput();
        }
    }
}
