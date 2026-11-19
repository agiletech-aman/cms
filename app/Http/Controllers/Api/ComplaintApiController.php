<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Region;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ComplaintApiController extends Controller
{
    // complaint_status codes => keys used in the API
    const STATUSES = [
        0 => 'open',
        1 => 'in_progress',
        2 => 'resolved',
        4 => 'closed',
    ];

    public function Login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => trans('auth.failed'),
            ]);
        }

        return response()->json([
            'token' => $user->createToken('api')->plainTextToken,
            'user' => $user->only(['id', 'name', 'username', 'cwc_code', 'region_id']),
            'roles' => $user->getRoleNames(),
        ]);
    } // End Method

    public function Logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out']);
    } // End Method

    /**
     * Status-wise complaint counts grouped by region and warehouse.
     * Filters: region_id, warehouse_id, start_date, end_date (Y-m-d)
     */
    public function Summary(Request $request)
    {
        $request->validate($this->filterRules());

        $rows = $this->filteredQuery($request)
            ->whereIn('complaints.complaint_status', array_keys(self::STATUSES))
            ->groupBy('users.region_id', 'complaints.user_id', 'complaints.complaint_status')
            ->selectRaw('users.region_id, complaints.user_id, complaints.complaint_status, COUNT(*) as total')
            ->get();

        $regions = Region::whereIn('id', $rows->pluck('region_id')->unique())->pluck('name', 'id');
        $warehouses = User::whereIn('id', $rows->pluck('user_id')->unique())->get(['id', 'name', 'cwc_code'])->keyBy('id');

        $data = $rows->groupBy('region_id')->map(function ($regionRows, $regionId) use ($regions, $warehouses) {
            $warehouseList = $regionRows->groupBy('user_id')->map(function ($whRows, $warehouseId) use ($warehouses) {
                return [
                    'warehouse_id' => (int) $warehouseId,
                    'warehouse_name' => $warehouses[$warehouseId]->name ?? null,
                    'cwc_code' => $warehouses[$warehouseId]->cwc_code ?? null,
                    'counts' => $this->statusCounts($whRows),
                ];
            })->values();

            return [
                'region_id' => $regionId === '' ? null : (int) $regionId,
                'region_name' => $regions[$regionId] ?? null,
                'counts' => $this->statusCounts($regionRows),
                'warehouses' => $warehouseList,
            ];
        })->values();

        return response()->json([
            'totals' => $this->statusCounts($rows),
            'regions' => $data,
        ]);
    } // End Method

    /**
     * Paginated complaint list.
     * Filters: region_id, warehouse_id, status (open|in_progress|resolved|closed, comma separated), start_date, end_date, per_page
     */
    public function List(Request $request)
    {
        $request->validate($this->filterRules() + [
            'status' => 'nullable|string',
            'per_page' => 'nullable|integer|min:1|max:200',
        ]);

        $statusCodes = array_keys(self::STATUSES);
        if (!empty($request->status)) {
            $requested = array_map('trim', explode(',', $request->status));
            $invalid = array_diff($requested, self::STATUSES);
            if ($invalid) {
                throw ValidationException::withMessages([
                    'status' => 'Invalid status: ' . implode(', ', $invalid) . '. Allowed: ' . implode(', ', self::STATUSES),
                ]);
            }
            $statusCodes = array_keys(array_intersect(self::STATUSES, $requested));
        }

        $complaints = $this->filteredQuery($request)
            ->whereIn('complaints.complaint_status', $statusCodes)
            ->leftJoin('regions', 'regions.id', '=', 'users.region_id')
            ->orderBy('complaints.created_at', 'DESC')
            ->select([
                'complaints.id',
                'complaints.complaint_id',
                'complaints.subject',
                'complaints.description',
                'complaints.complaint_status',
                'complaints.created_at',
                'complaints.resolved_date',
                'complaints.closed_date',
                'complaints.remark',
                'users.id as warehouse_id',
                'users.name as warehouse_name',
                'users.cwc_code',
                'users.region_id',
                'regions.name as region_name',
            ])
            ->paginate($request->per_page ?? 50);

        $complaints->getCollection()->transform(function ($item) {
            $item->status = self::STATUSES[$item->complaint_status] ?? null;
            return $item;
        });

        return response()->json($complaints);
    } // End Method

    private function filterRules()
    {
        return [
            'region_id' => 'nullable|integer',
            'warehouse_id' => 'nullable|integer',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d',
        ];
    }

    // Complaints joined with their warehouse user, scoped to what the caller's role may see
    private function filteredQuery(Request $request)
    {
        $user = Auth::user();
        $query = Complaint::join('users', 'users.id', '=', 'complaints.user_id');

        if ($user && $user->hasRole('RegionalManager')) {
            $query->where('users.region_id', $user->region_id);
        } elseif ($user && $user->hasRole('WarehouseManager')) {
            $query->where('users.id', $user->id);
        }

        if (!empty($request->region_id)) {
            $query->where('users.region_id', $request->region_id);
        }

        if (!empty($request->warehouse_id)) {
            $query->where('complaints.user_id', $request->warehouse_id);
        }

        if (!empty($request->start_date)) {
            $query->where('complaints.created_at', '>=', Carbon::parse($request->start_date)->startOfDay());
        }

        if (!empty($request->end_date)) {
            $query->where('complaints.created_at', '<=', Carbon::parse($request->end_date)->endOfDay());
        }

        return $query;
    }

    private function statusCounts($rows)
    {
        $counts = [];
        foreach (self::STATUSES as $code => $key) {
            $counts[$key] = (int) $rows->where('complaint_status', $code)->sum('total');
        }
        $counts['total'] = array_sum($counts);

        return $counts;
    }
}
