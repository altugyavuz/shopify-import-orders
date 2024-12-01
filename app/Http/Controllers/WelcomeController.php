<?php

namespace App\Http\Controllers;

use App\Http\Traits\SetupHelper;
use App\Models\StoreSetting;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    use SetupHelper;

    public function index()
    {
        if (null !== config('shopify_service.store_api_key') && null !== config('shopify_service.store_api_key')) {
            $this->markSetupIsDone();

            return redirect()->route('dashboard');
        } else {
            $storeSettings = StoreSetting::first();

            if (!$storeSettings) {
                return redirect()->route('setup.index')->with('message', 'Please complete the setup process to continue.');
            } else {
                $this->markSetupIsDone();

                return redirect()->route('dashboard');
            }
        }
    }
}
