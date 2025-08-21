<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $users = User::query()
            ->when($q, fn($qq) => $qq->where(function($w) use ($q) {
                $w->where('name','like',"%$q%")
                  ->orWhere('email','like',"%$q%")
                  ->orWhere('phone','like',"%$q%")
                  ->orWhere('national_id','like',"%$q%");
            }))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users','q'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'account_status'  => ['nullable', Rule::in(['pending','active','suspended'])],
            'presence_status' => ['nullable', Rule::in(['in','out'])],
        ]);

        return DB::transaction(function () use ($user, $data) {
            $changes = [];

            if (array_key_exists('account_status', $data) && $data['account_status'] !== null) {
                $changes['account_status'] = $data['account_status'];
            }
            if (array_key_exists('presence_status', $data) && $data['presence_status'] !== null) {
                $changes['presence_status'] = $data['presence_status'];

                // (اختياري) نسجّل تغيير التواجد كسجل حضور مصدره لوحة المشرف
                DB::table('attendance_logs')->insert([
                    'user_id'      => $user->id,
                    'action'       => $data['presence_status'],
                    'scanned_code' => 'admin-panel',
                    'device'       => 'admin',
                    'ip'           => request()->ip(),
                    'scanned_at'   => now(),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }

            if ($changes) {
                $user->update($changes);
            }

            return response()->json(['ok' => true, 'changes' => $changes]);
        });
    }
}
