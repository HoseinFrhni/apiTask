<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function index()
    {
// فقط تسک‌های کاربر وارد شده را برمی‌گرداند
        $tasks = Task::where('user_id', auth()->id())->get();
        return response()->json($tasks);
    }

    /**
     * ایجاد یک تسک جدید
     */
    public function store(Request $request)
    {
// اعتبارسنجی ورودی‌ها برای ایجاد تسک
        $validated = $request->validate([
            'title' => 'required|string|max:255', // عنوان الزامی و حداکثر طول 255
            'description' => 'nullable|string',   // توضیحات اختیاری
            'status' => ['required', Rule::in(['in_progress', 'completed'])], // وضعیت باید یکی از مقادیر مشخص شده باشد
            'start_date' => 'nullable|string',      // تاریخ شروع باید یک تاریخ معتبر باشد
            'end_date' => 'nullable|string', // تاریخ پایان باید بعد از تاریخ شروع باشد
        ]);

        $task = Task::create([
            'user_id' => $request->user_id, // کاربر وارد شده را استفاده می‌کنیم
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'],
        ]);

        return response()->json([
            'task' => $task,
            'message' => 'وظیفه ایجاد شد'
        ], 201);
    }

    /**
     * نمایش جزئیات یک تسک خاص
     */
    public function show(Task $task)
    {
// اعتبارسنجی برای بررسی وجود تسک
        if ($task->user_id != auth()->id()) {
            return response()->json(['message' => 'شما دسترسی به این تسک را ندارید'], 403);
        }

        return response()->json($task);
    }

    /**
     * به‌روزرسانی یک تسک
     */
    public function update(Request $request, Task $task)
    {
// اعتبارسنجی ورودی‌ها برای بروزرسانی تسک
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',  // عنوان می‌تواند تغییر کند ولی الزامی نیست
            'description' => 'nullable|string',
            'status' => ['sometimes', Rule::in(['in_progress', 'completed'])],
            'start_date' => 'nullable|string',
            'end_date' => 'nullable|string', // تاریخ پایان باید بعد از تاریخ شروع باشد
            'user_id' => 'sometimes|integer|exists:users,id',
        ]);

// بررسی اینکه تسک متعلق به کاربر وارد شده باشد
        if ($task->user_id != auth()->id()) {
            return response()->json(['message' => 'شما دسترسی به این تسک را ندارید'], 403);
        }

// بروزرسانی تسک با داده‌های اعتبارسنجی شده
        $task->update($validated);

        return response()->json($task);
    }

    /**
     * حذف یک تسک
     */
    public function destroy(Task $task)
    {
// بررسی اینکه تسک متعلق به کاربر وارد شده باشد
        if ($task->user_id != auth()->id()) {
            return response()->json(['message' => 'شما دسترسی به این تسک را ندارید'], 403);
        }

        $task->delete();

        return response()->json([
            'message' => 'وظیفه حذف شد'
        ], 204);
    }
}
