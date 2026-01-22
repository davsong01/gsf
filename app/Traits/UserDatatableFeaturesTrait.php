<?php
namespace App\Traits;

use App\Models\User;

trait UserDatatableFeaturesTrait {
    public function totalData($user){
        if($user->isSubAdmin() && $user->isMember()){
			$users = User::Wherehas('campus')
                ->where('chapter_id', auth()->user()->chapter_id)
                ->where('role','<>', 1)
                ->where('id', '<>', auth()->user()->id);
		}

        if($user->isAdmin()){
            $users = User::Wherehas('campus')
                ->where('role','<>', 1)
                ->where('id', '<>', auth()->user()->id);
        }

        return $users;

    }

    public function emptySearch($user, $start,$limit) {
        if($user->isSubAdmin() && $user->isMember()){
            $users = User::Wherehas('campus')
                ->where('role','<>', 1)
                ->where('chapter_id', auth()->user()->chapter_id)
                ->where('id', '<>', auth()->user()->id)
                ->offset($start)
                ->limit($limit)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        if($user->isAdmin()){
            $users = User::Wherehas('campus')
                ->where('role','<>', 1)
                ->where('id', '<>', auth()->user()->id)
                ->offset($start)
                ->limit($limit)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return $users;
    }

    public function results($user, $search, $start, $limit){
        if($user->isSubAdmin() && $user->isMember()){

            $q = User::with('campus')->where('role','<>', 1)->where('chapter_id', $user->chapter_id)->where(function($query)use ($search) {
                $query->where('name','LIKE',"%{$search}%")
                    ->orWhere('email', 'LIKE',"%{$search}%")
                    ->orWherehas('campus', function($q) use ($search) {
                        return $q->where('name', 'LIKE','%'.$search.'%');
                    })
                    ->orWhere('family_id', 'LIKE',"%{$search}%")
                    ->orWhere('matric_year', 'LIKE',"%{$search}%")
                    ->orWhere('graduation_year', 'LIKE',"%{$search}%")
                    ->orWhere('course', 'LIKE',"%{$search}%");
            });

        }

        if($user->isAdmin()){
            $q = User::where('role','<>', 1)
                ->where('name','LIKE',"%{$search}%")
                ->orWhere('email', 'LIKE',"%{$search}%")
                ->orWherehas('campus', function($q) use ($search) {
                    return $q->where('name', 'LIKE','%'.$search.'%');
                })
                ->orWhere('family_id', 'LIKE',"%{$search}%")
                ->orWhere('matric_year', 'LIKE',"%{$search}%")
                ->orWhere('graduation_year', 'LIKE',"%{$search}%")
                ->orWhere('course', 'LIKE',"%{$search}%");
        }

        $users = $q->offset($start)
					->limit($limit)
					->orderBy('created_at', 'desc')
					->get();

        $totalFiltered = $q->count();

        $results = [
            'users' => $users,
            'totalFiltered' => $totalFiltered,
        ];
        
        return $results;

    }





    public function users($user) {
        dd('users');
    }


}
