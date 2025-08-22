<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit(string $id)
    {
        $user = User::find($id);
        return view('profiles.edit', compact('user'));
    }

    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'mobile' => 'required|string|max:255',
                'image' => 'nullable|image',
                'address' => 'nullable|string',
            ]);

            $user = User::find($id);
            $user->name = $request->name;
            $user->mobile = $request->mobile;
            $user->address = $request->address;
            if ($request->hasFile('image')) {
                if ($user->image) {
                    $image_path = public_path('uploads/' . $user->image);
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
                $image = $request->file('image');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads'), $filename);
                $user->image = $filename;
            }
            $user->save();
            return redirect()->back()->with('success', 'Profile updated successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|string',
                'old_password' => 'required|string',
                'new_password' => 'required|string|min:8',
                'confirm_password' => 'required|string|same:new_password',
            ], [
                'confirm_password.same' => 'Password and confirm password must be same'
            ]);

            $user = User::find($request->id);

            if (!$user || !Hash::check($request->old_password, $user->password)) {
                return redirect()->back()->with('error', 'Old password is incorrect');
            }

            if (Hash::check($request->new_password, $user->password)) {
                return redirect()->back()->with('error', 'New password must be different from old password');
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            return redirect()->back()->with('success', 'Password updated successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
