<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function prepareUserData($request, ?User $user = null): array
    {
        $data = $request->only([
            'name', 'email', 'phone', 'gender', 'status', 'open_to_work', 'dob','chapter_id', 'role','designation_id', 'portfolio_session', 'program', 'course','skills', 'course_duration','matric_year','graduation_year','facebook','twitter','show_phone', 'show_email'
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

    public function uploadImage($file, $folder = 'frontend/passports', $width = 500, $height = 500): string
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs($folder, $filename, 'public');

        // Optional: resize with Intervention Image if needed
        // \Image::make(storage_path("app/public/$path"))->fit($width, $height)->save();

        return 'storage/' . $path;
    }

    protected function createFamilyId(User $user)
    {
        $user->family_id = 'FAM-' . str_pad($user->id, 5, '0', STR_PAD_LEFT);
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

    public function emptySearch(User $currentUser, int $start = 0, int $limit = 10)
    {
        $query = $this->totalData($currentUser)
                      ->orderBy('created_at', 'desc');

        if ($limit) {
            $query->skip($start)->take($limit);
        }

        return $query->get();
    }

    public function results(User $currentUser, string $search, int $start = 0, int $limit = 10): array
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
        $count = 1;
        $start = $requestData['start'] ?? 0;
        $limit = $requestData['length'] ?? 10;
        $search = $requestData['search']['value'] ?? null;
        $chapterId = $requestData['chapter_id'] ?? null;
        $canDelete = $requestData['canDelete'] ?? false;
        $canSwitch = $requestData['canSwitch'] ?? false;
        $isStakeholder = $requestData['isStakeholder'] ?? false;

        $query = User::query()->where('role', '<>', 1);

        if ($chapterId) {
            $query->where('chapter_id', $chapterId);
        }

        $totalData = $query->count();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $totalFiltered = $query->count();

        $users = $query->orderByDesc('created_at')
                    ->skip($start)
                    ->take($limit)
                    ->get();

        $data = [];
        foreach ($users as $user) {
            $avatar = $user->passport ?? "frontend/passports/avatar.jpg";
            $campus = $user->campus?->name ?? '';

            $nestedData = [
                'S/N' => $count++,
                'email' => $user->email,
                'family_id' => $user->family_id,
                'details' => "<strong>{$user->name}</strong><br>
                            <i class='fa fa-envelope'></i> {$user->email}<br>
                            <i class='fa fa-phone'></i> {$user->phone}<br>
                            <i class='fa fa-university'></i> GSF, {$campus}",
                'avatar' => "<img class='mr-1' style='border-radius:50%' src='{$avatar}' alt='avatar' height='40' width='40'>",
                'status' => $user->status == 0 ? 'Student' : 'Alumni',
                'designation' => $user?->designation?->name ?? 'N/A',
                'role' => $user->rolename
                            . '<br><em>'
                            . (($user->rolename !== 'Admin' && $user->rolename !== 'Member') ? $user->portfolio_session : '')
                            . '</em>',
                'actions' => $this->generateActionButtons($user, $canDelete, $canSwitch, $isStakeholder),
            ];

            $data[] = $nestedData;
        }

        return [
            'draw' => intval($requestData['draw'] ?? 1),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ];
    }

    protected function generateActionButtons(User $user, bool $canDelete, bool $canSwitch, ?bool $isStakeholder = false): string
    {
        $buttons = '<div class="btn-group" role="group" aria-label="User Actions">';

        if ($canDelete) {
            $buttons .= sprintf('<a href="%s" class="btn btn-sm btn-primary">Edit</a>', route($isStakeholder ? 'stakeholders.users.edit' : 'users.edit', $user->id));
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
