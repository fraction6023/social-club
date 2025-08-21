<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;



class SC_Controller extends Controller
{
    public function scanPage()
    {
        return view('attendance.scan');
    }

    public function toggleByScan(Request $request)
    {
        $request->validate([
            'code' => ['nullable', 'string', 'max:255'],
        ]);

        // اجلبه كنموذج Eloquent ليرتاح Intelephense
        /** @var \App\Models\User $user */
        $user = User::query()->findOrFail(Auth::id());

        if ($user->account_status !== 'active') {
            return response()->json(['ok' => false, 'message' => 'حسابك غير مفعل بعد.'], 403);
        }

        return DB::transaction(function () use ($request, $user) {
            $new = $user->presence_status === 'in' ? 'out' : 'in';

            // أي من الطريقتين أدناه تعمل وتُرضي الـ IDE
            $user->update(['presence_status' => $new]);
            // أو:
            // $user->forceFill(['presence_status' => $new])->save();

            DB::table('attendance_logs')->insert([
                'user_id'      => $user->id,
                'action'       => $new,
                'scanned_code' => $request->code,
                'device'       => $request->userAgent(),
                'ip'           => $request->ip(),
                'scanned_at'   => now(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            return response()->json([
                'ok' => true,
                'message' => $new === 'in' ? 'تم تسجيل دخولك (IN)' : 'تم تسجيل خروجك (OUT)',
                'presence_status' => $new,
            ]);
        });
    }
}
