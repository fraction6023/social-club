<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * قائمة المشتركين مع بحث بسيط.
     */
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));

        $users = User::query()
            ->when($q !== '', function ($qr) use ($q) {
                $qr->where(function ($s) use ($q) {
                    $s->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%")
                      ->orWhere('national_id', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q'));
    }

    /**
     * صفحة تعديل مشترك.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * حفظ التعديلات (يدعم AJAX وClassic POST).
     */
    public function update(Request $request, User $user): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'account_status'  => ['nullable', Rule::in(['pending','active','suspended'])],
            'presence_status' => ['nullable', Rule::in(['in','out'])],
        ]);

        $changed = $user->fill($data)->isDirty();
        $user->save();

        if ($request->expectsJson() || $request->wantsJson() || str_contains((string) $request->header('Accept'), 'application/json')) {
            return response()->json([
                'ok'      => true,
                'changed' => $changed,
            ]);
        }

        return back()->with('ok', $changed ? 'تم الحفظ' : 'لا توجد تغييرات');
    }
}
