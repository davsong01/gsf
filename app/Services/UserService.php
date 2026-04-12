<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    public function prepareUserData($request, ?User $user = null): array
    {
        $data = $request->only([
            'name', 'email', 'phone','is_graduated','skills', 'gender', 'status', 'open_to_work', 'dob','chapter_id', 'role','designation_id', 'portfolio_session', 'program', 'course','skills', 'course_duration','matric_year','graduation_year','facebook','twitter','show_phone', 'show_email'
        ]);

        // Handle password
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } elseif (!$user) {
            // If creating and password not set, use phone
            $data['password'] = Hash::make($request->phone);
        } else {
            // For updates, keep existing password if not changed
            $data['password'] = $user->password;
        }

        // Handle avatar/passport upload
        if ($request->hasFile('passport')) {
            $data['passport'] = app(FileUploadService::class)->uploadImage($request->file('passport'), 'passports');
        }

        // Handle show flags
        $data['show_phone'] = $request->filled('show_phone') ? 1 : 0;
        $data['show_email'] = $request->filled('show_email') ? 1 : 0;

        // Slug for name
        $data['slug'] = Str::slug($request->name);

        return $data;
    }

    public function createUser(array $data): User
    {
        $user = User::create($data);
        $this->createFamilyId($user);
        return $user;
    }

    public function updateUser(User $user, array $data): bool
    {
        return $user->update($data);
    }

    public function importUsers($request)
    {
        $users = ExcelService::import($request->file('file'));

        foreach ($users as &$user) {
            $user = array_change_key_case($user, CASE_LOWER);

            // Normalize values as needed
            $user['password'] = Hash::make(trim(strtolower($user['phone'])));
            $user['chapter_id'] = $request->chapter_id;
            $user['gender'] = ucfirst(strtolower($user['gender']));
            $user['role'] = 2;
            $user['status'] = 'active';
            $user['is_graduated'] = $request->type;
            $user['slug'] = $user['name'];

            try {
                $this->createUser($user);
                //code...
            } catch (\Throwable $th) {
                continue;
            }
        }

        return;
    }

    public static function exportConferenceParticipantsData($transactions, $edition){
        $transactions = $transactions
        ->unique(function ($t) {
            return $t->provider_reference ?? 'null_' . $t->id;
        })
        ->values();

        $allocatableFields = $edition->conferenceplans
        ->flatMap(fn ($plan) => $plan->fields()->pluck('name'))
        ->unique()
        ->reject(fn ($field) => $field === 'no_of_participants')
        ->mapWithKeys(function ($field) {
            return [
                $field => ucfirst(strtolower(explode('_', $field)[0]))
            ];
        })
        ->toArray();

        $headers = [
            'conference'                => 'Conference Title',
            'conference_plan_id'        => 'Registration Plan',
            'transid'                   => 'Transaction ID',
            'family_id'                 => 'Family ID',
            'name'                      => 'Name',
            'email'                     => 'Email',
            'registration_user_type'    => 'Registration User Type',
            'slot'                      => 'Slot Bought',
            'allocated'                 => 'Slots Used',
            'remaining'                 => 'Slot Remaining',
            'phone'                     => 'Phone',
            'provider_reference'        => 'Provider Reference',
            'amount_paid'               => 'Amount',
            'total_amount'              => 'Total Amount Paid',
            'status'                    => 'Payment Status',
            'registration_status'       => 'Registration Status',
            'moderator'                 => 'Moderator',
            'chapter_id'                => 'Chapter',
            'field_id'                  => 'Field',
            'zone_id'                   => 'Zone',
            'gender'                    => 'Gender',

            'hostel'                    => 'Hostel Allocation',
            'servicePoint'              => 'Service Point Allocation',

            'level'                     => 'Registration Plan',
            'hostel_allocation_type'    => 'Hostel Allocation Type',
            'hostel_allocation_number'  => 'Hostel Allocation Number',

            'service_point_allocation_type'   => 'Service Point Allocation Type',
            'service_point_allocation_number' => 'Service Point Allocation Number',

            'created_at'                => 'Registration Date',
        ];

        $headers = array_merge($headers, $allocatableFields);

        $data = $transactions->map(function ($transaction) use ($headers, $allocatableFields) {

            // build allocation keys once
            $allocationKeys = array_unique(
                array_merge(['chapter_id', 'field_id', 'zone_id'], array_keys($allocatableFields))
            );

            return collect($headers)->mapWithKeys(function ($label, $key) use ($transaction, $allocationKeys) {
                $moderator = $transaction->moderator->name ?? null;

                $value = match (true) {
                    // Allocation fields (dynamic + fixed)
                    in_array($key, $allocationKeys) =>
                        optional($transaction->allocationFields
                            ->firstWhere('key', $key))->value,

                    // Relations
                    in_array($key, ['hostel', 'servicePoint']) =>
                        optional($transaction->{$key})->name,

                    // User family id
                    $key === 'family_id' =>
                        optional($transaction->user)->family_id,

                    // Date formatting
                    $key === 'created_at' =>
                        optional($transaction->created_at)->format('Y-m-d H:i'),

                    // Moderator name
                    $key === 'moderator' =>
                        $moderator,

                    $key === 'slot' => $transaction->slot,
                    $key === 'allocated' => $transaction->slot_filled,
                    $key === 'remaining' => $transaction->slot - $transaction->slot_filled,
                    $key === 'conference' => $transaction->conferenceEdition->conference_theme ?? null,
                    $key === 'conference_plan_id' => $transaction->conferenceplan->title ?? null,
                    // Default field
                    default =>
                        $transaction->{$key} ?? null,
                };

                return [$label => $value];
            })->toArray();
        });

        return [
            'headers' => $headers,
            'data' => $data
        ];
    }


    protected function createFamilyId(User $user)
    {
        $user->family_id = 'GSFP-' . str_pad($user->id, 5, '0', STR_PAD_LEFT);
        $user->save();
    }


    public function totalData(User $currentUser)
    {
        $query = User::query();

        if ($currentUser->isSubAdmin() && $currentUser->isMember()) {
            $query->where('chapter_id', $currentUser->chapter_id)
                  ->where('role', '<>', 1);
        }

        return $query;
    }

    public function emptySearch(User $currentUser, int $start = 0, int $limit = 20)
    {
        $query = $this->totalData($currentUser)
                      ->orderBy('created_at', 'desc');

        if ($limit) {
            $query->skip($start)->take($limit);
        }

        return $query->get();
    }

    public function results(User $currentUser, string $search, int $start = 0, int $limit = 20): array
    {
        $query = $this->totalData($currentUser)
                      ->where(function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                      })
                      ->orderBy('created_at', 'desc');

        $totalFiltered = $query->count();

        if ($limit) {
            $query->skip($start)->take($limit);
        }

        return [
            'users' => $query->get(),
            'totalFiltered' => $totalFiltered,
        ];
    }

    public function getAllUsers(array $requestData): array
    {
        $start  = (int) ($requestData['start'] ?? 0);
        $limit  = (int) ($requestData['length'] ?? 20);
        $search = $requestData['search']['value'] ?? null;

        $chapterId    = $requestData['chapter_id'] ?? null;
        $canEdit      = $requestData['canEdit'] ?? false;
        $canDelete    = $requestData['canDelete'] ?? false;
        $canSwitch    = $requestData['canSwitch'] ?? false;
        $isStakeholder= $requestData['isStakeholder'] ?? false;
        $filters = $requestData['filters'] ?? [];
        $isAdmin = $requestData['isAdmin'] ?? false;

        $baseQuery = User::query()->where('role', '<>', 1);

        if (!empty($filters)) {
            $baseQuery->where($filters);
        }

        if ($chapterId) {
            $baseQuery->where('chapter_id', $chapterId);
        }

        // recordsTotal (no search)
        $recordsTotal = (clone $baseQuery)->count();

        // Apply search
        if (!empty($search)) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // recordsFiltered (after search)
        $recordsFiltered = (clone $baseQuery)->count();

        // Data query
        $query = $baseQuery->orderByDesc('created_at');

        // Apply pagination ONLY if not "All"
        if ($limit != -1) {
            $query->skip($start)->take($limit);
        }

        $users = $query->get();

        $data = [];
        $count = $start + 1; // correct serial number

        foreach ($users as $user) {
            $avatar = asset($user->passport ?? "frontend/passports/avatar.jpg");
            $campus = $user->campus?->name ?? '';

            $data[] = [
                'S/N' => $count++,
                'email' => $user->email,
                'family_id' => $user->family_id . '<br>' .
               ($user->status == 'active'
                   ? '<span class="badge bg-success">Active</span>'
                   : '<span class="badge bg-danger">Inactive</span>') .
               ($isAdmin
                   ? '<br><span>Last Login: ' . ($user->last_login ?? 'N/A') . '</span>'
                   : ''),

                'details' => "<strong>{$user->name}</strong><br>
                            <i class='fa fa-envelope'></i> {$user->email}<br>
                            <i class='fa fa-phone'></i> {$user->phone}<br>
                            <i class='fa fa-university'></i> GSF, {$campus}",
                'avatar' => "<img style='border-radius:50%' src='{$avatar}' height='40' width='40'>",
                'is_graduated' => $user->is_graduated == 0 ? 'Student' : 'Alumni',
                'designation' => $user?->designation?->name ?? 'N/A',
                'role' => $user->rolename
                            . '<br><em>'
                            . (($user->rolename !== 'Admin' && $user->rolename !== 'Member') ? $user->portfolio_session : '')
                            . '</em>',
                'actions' => $this->generateActionButtons($user, $canDelete, $canEdit, $canSwitch, $isStakeholder),
            ];
        }

        return [
            'draw' => intval($requestData['draw'] ?? 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }


    protected function generateActionButtons(User $user, bool $canDelete, bool $canEdit, bool $canSwitch, ?bool $isStakeholder = false): string
    {
        $buttons = '<div class="btn-group" role="group" aria-label="User Actions">';

        if ($canEdit) {
            $buttons .= sprintf('<a href="%s" class="btn btn-sm btn-primary">Edit</a>', route($isStakeholder ? 'stakeholders.users.edit' : 'users.edit', $user->id));
        }

        if ($canDelete) {
            $buttons .= sprintf('<a href="%s" class="btn btn-sm btn-danger" onclick="return confirm(\'Delete this user?\');">Delete</a>', route($isStakeholder ? 'stakeholders.users.destroy' : 'users.destroy', $user->id));
        }


        if ($canSwitch && !$isStakeholder) {
            $buttons .= sprintf('<a href="%s" class="btn btn-sm btn-warning" onclick="return confirm(\'Switch to this user?\');">Switch</a>', route('switchuser', $user->id));
        }

        $buttons .= '</div>';

        return $buttons;
    }


    public function authorizeUserAction()
    {
        // Only Admins or SubAdmins who are Members can create/update users
        if (!auth()->user()->isAdmin() && !(auth()->user()->isSubAdmin() && auth()->user()->isMember())) {
            abort(403, 'Unauthorized action.');
        }
    }

}
