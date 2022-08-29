<?php

namespace RachidLaasri\LaravelInstaller\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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
            // check from owr server
            $verify = Http::post('http://codeshaper.net/api/envato-buyers', $validated);

            if ($verify->failed()) {
                $message = $verify->json();

                return back()->withErrors([
                    'purchase_code' => $message,
                ])->withInput();
            }

            $verifiedLogFile = storage_path('verified');
            $dateStamp = date('Y/m/d h:i:sa');
            if (!file_exists($verifiedLogFile)) {
                $message = trans('installer_messages.purchase_code.verified_msg').$dateStamp."\n";
                file_put_contents($verifiedLogFile, $message);
            }

            return view('vendor.installer.welcome');
        } catch (Exception $ex) {
            // print the error so the user knows what's wrong
            return back()->with('msg', $ex->getMessage());
        }
    }
}
