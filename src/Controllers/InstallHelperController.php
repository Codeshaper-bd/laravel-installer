<?php

namespace RachidLaasri\LaravelInstaller\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'purchase_code' => 'required|string|max:36|min:36',
        ]);

        try {
            // clean purchase code
            $purchaseCode = clean(trim($request->purchase_code));

            // This step is important - requests with incorrect formats can be blocked!
            if (preg_match('/^([a-f0-9]{8})-(([a-f0-9]{4})-){3}([a-f0-9]{12})$/i', $purchaseCode)) {
                $personalToken = '6lpYs05IDzMw8wxNz0uUz51dCbWkLbRu';

                // envato api request
                $response = Http::withToken($personalToken)
                    ->get('https://api.envato.com/v3/market/author/sale', [
                        'code' => $purchaseCode,
                    ]);

                // store user info if purchase code is valid
                if ($response->successful()) {
                    $sale = $response->json();
                    //preload data
                    $data = [
                        'name' => clean($request->name),
                        'email' => clean($request->email),
                        'purchaseCode' => $request->purchase_code,
                        'amount' => $sale['amount'],
                        'soldAt' => $sale['sold_at'],
                        'license' => $sale['license'],
                        'supportAmount' => $sale['support_amount'],
                        'supportDate' => $sale['supported_until'],
                        'item' => $sale['item']['name'],
                        'buyer' => $sale['buyer'],
                        'purchaseCount' => $sale['purchase_count'],
                    ];

                    // send post request to server
                    $postResponse = Http::post('http://projects.codeshaper.tech/envato/', $data);

                    if ($postResponse->successful()) {
                        // store verified file in storage
                        $verifiedLogFile = storage_path('verified');
                        $dateStamp = date('Y/m/d h:i:sa');
                        if (!file_exists($verifiedLogFile)) {
                            $message = trans('installer_messages.purchase_code.verified_msg').$dateStamp."\n";
                            file_put_contents($verifiedLogFile, $message);
                        }
                    } else {
                        return $postResponse->json();
                    }

                    return view('vendor.installer.welcome');
                }

                return back()->withErrors([
                    'purchase_code' => 'Invalid purchase code. Please provide valid purchase code!',
                ])->withInput();
            } else {
                return back()->withErrors([
                    'purchase_code' => 'Invalid purchase code. Please provide valid purchase code!',
                ])->withInput();
            }
        } catch (Exception $ex) {
            // Print the error so the user knows what's wrong
            echo $ex->getMessage();
        }
    }
}
