<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->change(); // تأكيد وجود الاسم
            $table->string('nationality')->nullable();
            $table->string('national_id')->nullable()->index();
            $table->date('birth_date')->nullable();
            $table->string('phone')->nullable();
            // email موجود مسبقًا
            $table->string('address')->nullable();
            $table->decimal('weight', 5, 2)->nullable(); // بالكيلو
            $table->decimal('height', 5, 2)->nullable(); // بالسم
            $table->string('health_status')->nullable(); // نص حر أو رموز
            $table->enum('swimming_level', ['none', 'basic', 'intermediate', 'advanced'])->default('none');

            $table->enum('account_status', ['pending', 'active', 'suspended'])->default('pending'); // حالة الحساب
            $table->enum('presence_status', ['in', 'out'])->default('out'); // حالة (in/out)
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nationality',
                'national_id',
                'birth_date',
                'phone',
                'address',
                'weight',
                'height',
                'health_status',
                'swimming_level',
                'account_status',
                'presence_status'
            ]);
        });
    }
};
