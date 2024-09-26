<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuthUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use Illuminate\Support\Facades\Mail;
use Pest\Support\Str;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function create()
    {
        if (Auth::guard('admin')->user()->id == 1) {
            return view('admin.user.create');
        } else {
            return redirect()->back()->withErrors(['error' => 'You are not authorized.']);
        }
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'same:password'],
            'user_type' => 'required|integer',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->user_type = $request->user_type;
        $user->created_by =Auth::guard('admin')->user()->id;;
        $user->save();
        $activation_code = Str::random(64);

        Mail::send('admin.user.emails.activation_email_Html', [
            'activation_code' => $activation_code,
            'name' => $request->name
        ], function ($message) use($request) {
            $message->to($request->email, $request->name);
            $message->subject('You have registered');
        });

        return response()->json(['success' => 'User Created Successfully'], 200);
    }

    public function index()
    {
        $users = AuthUser::where([
            ['is_active', 1],
            ['is_deleted', 0],
            ['id', '>', 1]
        ])->with('creator')->get();

        return view('admin.user.index', compact('users'));
    }

    public function edit($id)
    {
        $user = AuthUser::find($id);
        if ($user) {
            return view('admin.user.edit', compact('user'));
        } else {
            return redirect('admin/users')->with('error', 'User not found!');
        }
    }

    public function update(Request $request, $id)
    {
        $user = AuthUser::find($id);
        if ($user) {
            $user->name = $request->name ? $request->name : $user->name;
            if ($request->password) {
                $user->password = bcrypt($request->password);
            }
            $user->user_type = $request->user_type;
            // if ($request->hasFile('profile_picture')) {
            //     $file = $request->file('profile_picture');
            //     $filename = time() . '.' . $file->getClientOriginalExtension();
            //     $file->move('uploads/user/', $filename);
            //     $user->profile_picture = $filename;
            // }
            $user->update();
            return redirect('admin/users')->with('success', 'User updated successfully!');
        } else {
            return redirect()->back()->with('error', 'User not found!');
        }
    }


    public function destroy($id)
    {
        DB::beginTransaction(); // Start the transaction

        try {
            $user = User::where('id', $id)->first();

            if (!$user) {
                return redirect()->back()->with('error', 'User not found!');
            }

            // Prevent deletion of the admin user with ID 1
            if ($user->id == 1) {
                return redirect()->back()->with('error', 'You have no permission to delete Admin!');
            }

            // Update the user to mark as deleted
            $user->update([
                'is_deleted' => 1,
                'updated_at' => now(),
                'updated_by' => Auth::guard('admin')->user()->id,
            ]);

            DB::commit(); // Commit the transaction

            return redirect()->back()->with('success', 'User moved to Trash Successfully!');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction if something goes wrong

            return redirect()->back()->with('error', 'Failed to delete user!');
        }
    }

    public function delete($id)
    {
        $user = AuthUser::find($id);
        if ($user) {
            $user->delete();
            return redirect()->back()->with('success', 'User Deleted Permanently!');
        } else {
            return redirect()->back()->with('error', 'User not found!');
        }
    }

    public function restore($id)
    {
        DB::beginTransaction(); // Start the transaction

        try {
            $user = User::where('id', $id)->where('is_deleted', 1)->first();

            if (!$user) {
                return redirect()->back()->with('error', 'User not found or not deleted!');
            }

            // Restore the user
            $user->update([
                'is_deleted' => 0,
                'updated_at' => now(),
                'updated_by' => Auth::guard('admin')->user()->id,
            ]);

            DB::commit(); // Commit the transaction

            return redirect()->back()->with('success', 'User restored successfully!');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction if something goes wrong
            return redirect()->back()->with('error', 'Failed to restore user!');
        }
    }

    public function trash()
    {
        $trashed_users = AuthUser::where('is_deleted', 1)->get();
        return view('admin.user.trash', compact('trashed_users'));
    }
}
