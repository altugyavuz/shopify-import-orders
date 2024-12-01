<?php

namespace App\Http\Controllers;

use App\Http\Traits\SetupHelper;
use App\Models\StoreSetting as Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SetupController extends Controller
{
    use SetupHelper;

    /**
     * @return \Illuminate\Http\RedirectResponse|\Inertia\Response
     */
    public function index()
    {
        // $checkSetup = $this->checkSetupIsDone();

        // if ($checkSetup) {
        //     return redirect()->route('dashboard');
        // }

        return Inertia::render('Setup/SaveStoreInfo');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveCredentials(Request $request)
    {
        $request->validate([
            'store_alias'  => 'required',
            'store_name'   => 'required',
            'access_token' => 'required',
        ]);

        // Check information is correct
        $checkInformation = $this->checkStoreInformation(
            $request->input('store_name'),
            $request->input('access_token')
        );

        // If information is not correct or not authorized return redirect back with errors
        if (!$checkInformation) {
            return back()->withErrors([
                'message' => 'Store Domain Name or Access Token is wrong. Check the information and try again!'
            ]);
        }
        // Save store inform
        Setting::query()->create(
            $request->only([
                'store_alias',
                'store_name',
                'access_token',
            ]),
        );
        // Mark setup is completed
        $this->markSetupIsDone();

        return redirect()->route('dashboard');
    }
}
