<?php

namespace App;

use App\Food;
use App\Post;
use App\Hostel;
use App\Payout;
use App\Chapter;
use App\Payment;
use App\Notification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
	use Notifiable;
	use SoftDeletes;

	protected $guarded = [];

	/**
	 * The attributes that should be hidden for arrays
	 * @var array
	 */
    protected $guaraded = [];

	public function isSubAdmin() {
		$subAdmins = [3,4,5,6];
        if(in_array($this->role, $subAdmins) && $this->status == 0){
            return true;
        }else return false;
	}

	public function isAdmin(){
		$admins = [1];
		if(in_array($this->role, $admins)){
			return true;
		}else return false;
	}
	
	public function isMember(){
		if($this->status == '0'){
			return true;
		}else return false;
	}

	public function isParticipant($edition)
	{
		$check = Payment::where(['level'=>'Participant', 'conference_edition_id'=>$edition->id,'user_id'=>$this->id])->first();
		if (isset($check) && !empty($check)) {
			return true;
		} else return false;
	}

	public function isAlumni($edition)
	{
		$check = Payment::where(['level' => 'Alumni', 'conference_edition_id' => $edition->id, 'user_id' => $this->id])->first();

		if (isset($check) && !empty($check)) {
			return true;
		} else return false;
	}

	public function isNec($edition)
	{
		$check = Payment::where(['level' => 'Nec', 'conference_edition_id' => $edition->id, 'user_id' => $this->id])->first();

		if (isset($check) && !empty($check)) {
			return true;
		} else return false;
	}

	public function isModerator($edition)
	{
		$check = Payment::where(['level' => 'Moderator', 'conference_edition_id' => $edition->id, 'user_id' => $this->id])->first();
		
		if (isset($check) && !empty($check)) {
			return true;
		} else return false;
	}

	public function isChoir($edition)
	{
		$check = Payment::where(['level' => 'Choir', 'conference_edition_id' => $edition->id, 'user_id' => $this->id])->first();

		if (isset($check) && !empty($check)) {
			return true;
		} else return false;
	}

	public function isOfficial($edition)
	{
		$check = Payment::where(['level' => 'Official', 'conference_edition_id' => $edition->id, 'user_id' => $this->id])->first();

		if (isset($check) && !empty($check)) {
			return true;
		} else return false;
	}


	public function completeReg($edition)
	{
		$check = Payment::where(['registration_status' => 'Complete', 'conference_edition_id' => $edition->id, 'user_id' => $this->id])->first();

		if (isset($check) && !empty($check)) {
			return true;
		} else return false;
	}

	
    public function campus(){
        return $this->belongsTo(Chapter::class, 'chapter_id');
    }

	public function payments(){
        return $this->hasMany(Payment::class)->orderBy('created_at','DESC');
    }

	
    public function payouts(){
        return $this->hasMany(Payout::class);
    }

    public function setSwitchingUser($id)
    {
        Session::put('switchuser', $id);
    }

    public function stopSwitchingUser()
    {
        Session::forget('switchuser');
    }

    public function isSwitchingUser()
    {
        return Session::has('switchuser');
    }  

	public function getRolenameAttribute(){
		$attributes = new Controller();
		$attributes = $attributes->getCommunityPortfolios();
		
		return $attributes[$this->role];
	}

	// public function scopeExclude($query, $excludeColumns, $alias = '')
	// {
	// 	return $query->select(
	// 		...array_merge(array_diff(Schema::getColumnListing('users'), (array) $excludeColumns), $alias)
	// 	);
	// }

	// public function scopeAlias($query, $from, $to)
	// {
	// 	$columns = Schema::getColumnListing('users');
	// 	$from = (array) $from;
	// 	$to = (array) $to;
	// 	if (count($from) == count($to))
	// 		for ($i = 0; $i < count($from); $i++) {
	// 			if (in_array($from[$i], $columns)) {
	// 				$columns[array_search($from[$i], $columns)] = $from[$i] . ' AS ' . $to[$i];
	// 			}
	// 		}
	// 	return $query->select(
	// 		...$columns
	// 	);
	// }
	
	// public function scopeForeign($query, $foreignKeys, $tables, $localKeys, $foreignColumns)
	// {
	// 	$foreignKeys = (array) $foreignKeys;
	// 	$localKeys = (array) $localKeys;
	// 	$tables = (array) $tables;
	// 	$foreignColumns = (array) $foreignColumns;
	// 	$columns = Schema::getColumnListing('users');

	// 	if (count($foreignKeys) == count($localKeys)) {
	// 		foreach ($columns as &$value) {
	// 			if (in_array($value, $foreignKeys))
	// 				$value =  $tables[array_search($value, $foreignKeys)] . '.' . $foreignColumns[array_search($value, $foreignKeys)] . ' AS table_users_on_table_' . $tables[array_search($value, $foreignKeys)] . '_column_' . $foreignColumns[array_search($value, $foreignKeys)];
	// 			else
	// 				$value = 'users.' . $value;
	// 		}
	// 		unset($value);
	// 		$query = $query->select(...$columns);

	// 		foreach ($tables as $index => $table) {
	// 			$query = $query->leftJoin($table, 'users.' . $foreignKeys[$index], '=', $table . '.' . $localKeys[$index]);
	// 		}
	// 	}
	// 	return $query;
	// }
}
