<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\Store;
use App\Http\Requests\User\Update;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $currentDate = Carbon::now()->toDateString();
        try {
            $allUserData = User::when($request->get('dob_filter') == 'u7', function ($q) use ($currentDate) {
                return $q->where('dob', '>=', $currentDate)
                    ->where('dob', '<=', Carbon::now()->addDay(6)->toDateString());
            })->when($request->get('dob_filter') == 'u', function ($q) use ($currentDate) {
                return $q->where('dob', '>=', $currentDate);
            })->when($request->get('dob_filter') == 'f7', function ($q) use ($currentDate) {
                return $q->where('dob', '>=', Carbon::now()->subDays(6))
                    ->where('dob', '<=', $currentDate);
            })
                ->get();
            $allUserSortedData = collect($allUserData)->sortBy('dob')->values();
            return $this->successAndErrorResponse(200, $allUserSortedData, null, null);
        } catch (\Exception $e) {
            return $this->successAndErrorResponse(422, null, null, $e);
        }
    }

    public function store(Store $request)
    {
        try {
            User::create([
                'email' => $request->email,
                'name' => $request->name,
                'dob' => $request->dob
            ]);
            return $this->successAndErrorResponse(200, null, 'New User Created', null);
        } catch (\Exception $e) {
            return $this->successAndErrorResponse(422, null, null, $e);
        }
    }

    public function show(User $user)
    {
        try {
            return $this->successAndErrorResponse(200, $user, null, null);
        } catch (\Exception $e) {
            return $this->successAndErrorResponse(422, null, null, $e);
        }
    }

    public function update(Update $request, User $user)
    {
        try {
            $user->update([
                'email' => $request->email,
                'name' => $request->name,
                'dob' => $request->dob
            ]);
            return $this->successAndErrorResponse(200, null, 'User Updated', null);
        } catch (\Exception $e) {
            return $this->successAndErrorResponse(422, null, null, $e);
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            return $this->successAndErrorResponse(200, null, 'User Deleted', null);
        } catch (\Exception $e) {
            return $this->successAndErrorResponse(422, null, null, $e);
        }
    }
}
