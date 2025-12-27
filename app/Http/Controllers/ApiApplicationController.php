<?php

namespace App\Http\Controllers;

use App\Models\ApiApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiApplicationController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();
        $existingApp = $user->apiApplication;

        if ($existingApp) {
            if ($existingApp->status === 'pending') {
                return redirect()->back()->with('error', 'You currently have a pending application. Please wait for the review process to complete.');
            }
            if ($existingApp->status === 'approved') {
                return redirect()->back()->with('info', 'You already have approved API access. No need to apply again.');
            }
        }

        $request->validate([
            'api_type' => 'required|string',
            'business_name' => 'required|string',
            'website_link' => 'nullable|url',
            'business_description' => 'required|string',
            'business_nature' => 'required|string',
            'terms' => 'required|accepted',
        ]);

        if ($existingApp && $existingApp->status === 'rejected') {
            $existingApp->update([
                'api_type' => $request->api_type,
                'business_name' => $request->business_name,
                'website_link' => $request->website_link,
                'business_description' => $request->business_description,
                'business_nature' => $request->business_nature,
                'status' => 'pending',
            ]);
            
            $message = 'Your application has been successfully resubmitted for review.';
        } else {
            ApiApplication::create([
                'user_id' => $user->id,
                'api_type' => $request->api_type,
                'business_name' => $request->business_name,
                'website_link' => $request->website_link,
                'business_description' => $request->business_description,
                'business_nature' => $request->business_nature,
                'status' => 'pending',
            ]);

            $message = 'Your API access request has been submitted successfully and is being reviewed.';
        }

        return redirect()->back()->with('success', $message);
    }
}
