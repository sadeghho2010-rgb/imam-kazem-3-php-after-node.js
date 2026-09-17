<?php

namespace App\Http\Controllers;

use App\Models\MealMenu;
use App\Models\MealReservation;
use App\Models\Student;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MealController extends Controller
{
    /**
     * Get meal menus for dates.
     */
    public function getMenus(Request $request)
    {
        $query = MealMenu::query();
        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }
        return response()->json(['menus' => $query->orderBy('date', 'asc')->get()]);
    }

    /**
     * Add or update a daily meal menu.
     */
    public function saveMenu(Request $request)
    {
        $request->validate([
            'date' => 'required|string',
            'mealName' => 'required|string',
            'price' => 'required|numeric',
        ]);

        $menu = MealMenu::updateOrCreate(
            ['date' => $request->date],
            [
                'id' => $request->id ?: (string) Str::uuid(),
                'dayOfWeek' => $request->dayOfWeek,
                'mealName' => $request->mealName,
                'price' => $request->price,
                'subsidy' => $request->subsidy ?? 0,
                'description' => $request->description,
                'isActive' => true,
            ]
        );

        return response()->json(['menu' => $menu, 'message' => 'برنامه غذایی ثبت شد.']);
    }

    /**
     * Reserve meal for student.
     */
    public function reserveMeal(Request $request)
    {
        $request->validate([
            'studentId' => 'required|string|exists:students,id',
            'date' => 'required|string',
        ]);

        $menu = MealMenu::where('date', $request->date)->first();
        $student = Student::findOrFail($request->studentId);

        $pricePaid = $menu ? max(0, $menu->price - $menu->subsidy) : 0;

        $reservation = MealReservation::updateOrCreate(
            [
                'studentId' => $student->id,
                'date' => $request->date,
            ],
            [
                'id' => (string) Str::uuid(),
                'nationalId' => $student->nationalId,
                'status' => 'reserved',
                'pricePaid' => $pricePaid,
            ]
        );

        return response()->json(['reservation' => $reservation, 'message' => 'رزرو ناهار با موفقیت انجام شد.']);
    }

    /**
     * Cancel meal reservation.
     */
    public function cancelReservation(Request $request, $id)
    {
        $reservation = MealReservation::findOrFail($id);
        $reservation->status = 'cancelled';
        $reservation->save();

        return response()->json(['message' => 'رزرو غذا لغو گردید.']);
    }
}
