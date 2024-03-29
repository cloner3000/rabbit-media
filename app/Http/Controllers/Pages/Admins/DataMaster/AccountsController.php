<?php

namespace App\Http\Controllers\Pages\Admins\DataMaster;

use App\User;
use App\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class AccountsController extends Controller
{
    public function showAdminsTable()
    {
        $admins = Admin::all();

        return view('pages.admins.dataMaster.accounts.admin-table', compact('admins'));
    }

    public function createAdmins(Request $request)
    {
        $this->validate($request, [
            'ava' => 'image|mimes:jpg,jpeg,gif,png|max:2048',
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:admins',
            'password' => 'required|string|min:6|confirmed',
            'jabatan' => 'required',
            'role' => 'required'
        ]);

        if ($request->hasfile('ava')) {
            $name = $request->file('ava')->getClientOriginalName();
            $request->file('ava')->storeAs('public/admins/ava', $name);

        } else {
            $name = null;
        }

        Admin::create([
            'ava' => $name,
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'jabatan' => $request->jabatan,
            'role' => $request->role
        ]);
        return back()->with('success', '' . $request->name . ' is successfully created!');
    }

    public function updateProfileAdmins(Request $request)
    {
        $admin = Admin::find($request->admin_id);
        $this->validate($request, [
            'ava' => 'image|mimes:jpg,jpeg,gif,png|max:2048',
        ]);
        if ($request->hasFile('ava')) {
            $name = $request->file('ava')->getClientOriginalName();
            if ($admin->ava != '') {
                Storage::delete('public/admins/ava/' . $admin->ava);
            }
            $request->file('ava')->storeAs('public/admins/ava', $name);

        } else {
            $name = $admin->ava;
        }
        $admin->update([
            'ava' => $name,
            'name' => $request->name,
            'jabatan' => $request->jabatan
        ]);

        return back()->with('success', 'Successfully update ' . $admin->name . '\'s profile!');
    }

    public function updateAccountAdmins(Request $request)
    {
        $admin = Admin::find($request->admin_id);
        if (!Hash::check($request->password, $admin->password)) {
            return back()->with('error', '' . $admin->name . '\'s current password is incorrect!');

        } else {
            if ($request->new_password != $request->password_confirmation) {
                return back()->with('error', '' . $admin->name . '\'s password confirmation doesn\'t match!');

            } else {
                $admin->update([
                    'email' => $request->email,
                    'password' => bcrypt($request->new_password),
                    'role' => $request->role == null ? 'root' : $request->role
                ]);
                return back()->with('success', 'Successfully update ' . $admin->name . '\'s account!');
            }
        }
    }

    public function deleteAdmins($id)
    {
        $admin = Admin::find(decrypt($id));
        if ($admin->ava != '') {
            Storage::delete('public/admins/ava/' . $admin->ava);
        }
        $admin->forceDelete();

        return back()->with('success', '' . $admin->name . ' is successfully deleted!');
    }

    public function massDeleteAdmins(Request $request)
    {
        $admins = Admin::whereIn('id', explode(",", $request->admin_ids))->get();
        foreach ($admins as $admin) {
            if ($admin->ava != '') {
                Storage::delete('public/admins/ava/' . $admin->ava);
            }
            $admin->forceDelete();
        }
        $message = count($admins) > 1 ? count($admins) . ' admin accounts are ' : count($admins) . ' admin account is ';

        return back()->with('success', $message . 'successfully deleted!');
    }

    public function showUsersTable()
    {
        $users = User::all();

        return view('pages.admins.dataMaster.accounts.user-table', compact('users'));
    }

    public function deleteUsers($id)
    {
        $user = User::find(decrypt($id));
        if ($user->ava != '') {
            Storage::delete('public/users/ava/' . $user->ava);
        }
        $user->forceDelete();

        return back()->with('success', '' . $user->name . ' is successfully deleted!');
    }

    public function massDeleteUsers(Request $request)
    {
        $users = User::whereIn('id', explode(",", $request->user_ids))->get();
        foreach ($users as $user) {
            if ($user->ava != '') {
                Storage::delete('public/users/ava/' . $user->ava);
            }
            $user->forceDelete();
        }
        $message = count($users) > 1 ? count($users) . ' user accounts are ' : count($users) . ' user account is ';

        return back()->with('success', $message . 'successfully deleted!');
    }
}
