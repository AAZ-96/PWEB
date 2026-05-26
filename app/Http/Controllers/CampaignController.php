<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:10|max:100',
            'cat' => 'required|string',
            'location' => 'required|string',
            'fundraiser' => 'required|string',
            'desc' => 'required|string|min:30',
            'target' => 'required|numeric|min:1000000',
            'days' => 'required|integer|min:1',
            'startDate' => 'required|date',
            'pic_name' => 'nullable|string',
            'pic_phone' => 'nullable|string',
            'social' => 'nullable|string',
            'img' => 'nullable|string',
            'budget' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'msg' => $validator->errors()->first(),
            ]);
        }

        $imagePath = $request->img;

        // If the image is a base64 DataURL, decode and save it as a local file
        if ($imagePath && str_starts_with($imagePath, 'data:image/')) {
            try {
                // Parse base64
                $pos  = strpos($imagePath, ';');
                $type = explode(':', substr($imagePath, 0, $pos))[1];
                $ext  = explode('/', $type)[1];
                $allowed = ['jpeg', 'png', 'jpg', 'webp', 'gif'];
                
                if (!in_array($ext, $allowed)) {
                    $ext = 'png';
                }

                $image = str_replace('data:image/' . $ext . ';base64,', '', $imagePath);
                $image = str_replace('data:image/jpeg;base64,', '', $image);
                $image = str_replace('data:image/jpg;base64,', '', $image);
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                
                $imageName = 'campaign_' . Str::random(10) . '_' . time() . '.' . $ext;
                $directory = public_path('uploads/campaigns');
                
                if (!File::exists($directory)) {
                    File::makeDirectory($directory, 0755, true, true);
                }
                
                File::put($directory . '/' . $imageName, base64_decode($image));
                $imagePath = '/uploads/campaigns/' . $imageName;
            } catch (\Exception $e) {
                return response()->json([
                    'ok' => false,
                    'msg' => 'Gagal memproses unggahan gambar: ' . $e->getMessage(),
                ]);
            }
        }

        $campaign = Campaign::create([
            'title' => $request->title,
            'cat' => $request->cat,
            'img' => $imagePath ?: 'https://images.unsplash.com/photo-1593113598332-cd288d649433?auto=format&fit=crop&w=800&q=60',
            'location' => $request->location,
            'fundraiser' => $request->fundraiser,
            'desc' => $request->desc,
            'target' => $request->target,
            'collected' => 0,
            'days' => $request->days,
            'start_date' => $request->startDate,
            'status' => 'pending', // default pending approval
            'pic_name' => $request->pic_name,
            'pic_phone' => $request->pic_phone,
            'social' => $request->social,
            'budget' => $request->budget ?: [],
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'ok' => true,
            'campaign' => $campaign,
        ]);
    }

    public function approve($id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->status = 'active';
        $campaign->save();

        return response()->json(['ok' => true]);
    }

    public function reject($id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->status = 'rejected';
        $campaign->save();

        return response()->json(['ok' => true]);
    }

    public function destroy($id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->delete();

        return response()->json(['ok' => true]);
    }
}
