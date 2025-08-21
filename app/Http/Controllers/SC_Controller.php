<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;



class SC_Controller extends Controller
{
    // صفحة فيها كاميرا ومسح QR، وتعرض حسب الإجراء المختار
    public function scanPage(string $action)
    {
        abort_unless(in_array($action, ['in','out']), 404);
        return view('attendance.scan', compact('action'));
    }

    // يضبط الحالة حسب الاختيار (in/out) بعد نجاح السكان
    public function setByScan(Request $request, string $action)
    {
        abort_unless(in_array($action, ['in','out']), 404);

        $request->validate([
            'code' => ['nullable','string','max:255'],
        ]);

        /** @var \App\Models\User $user */
        $user = User::query()->findOrFail(Auth::id());

        if ($user->account_status !== 'active') {
            return response()->json(['ok' => false, 'message' => 'حسابك غير مفعل بعد.'], 403);
        }

        return DB::transaction(function () use ($request, $user, $action) {
            // لو تبي تمنع تكرار نفس الحالة، تقدر تتحقق هنا
            $user->update(['presence_status' => $action]);

            DB::table('attendance_logs')->insert([
                'user_id'      => $user->id,
                'action'       => $action,
                'scanned_code' => $request->code,
                'device'       => $request->userAgent(),
                'ip'           => $request->ip(),
                'scanned_at'   => now(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            return response()->json([
                'ok' => true,
                'message' => $action === 'in'
                    ? 'تم تسجيل دخولك (IN)'
                    : 'تم تسجيل خروجك (OUT)',
                'presence_status' => $action,
            ]);
        });
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
