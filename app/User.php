<?php

namespace App;

use App\Food;
use App\Post;
use App\Hostel;
use App\Payout;
use App\Chapter;
use App\Notification;
use Illuminate\Support\Facades\Session;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable implements MustVerifyEmail
{
	use Notifiable;
	use SoftDeletes;

	protected $guarded = [];

	/**
	 * The attributes that should be hidden for arrays
	 * @var array
	 */
    protected $hidden = [
				'password', 'remember_token', 'id', 'created_at', 'updated_at', 'deleted_at'
				, 'passport'
    ];

    public function hostel(){
        return $this->belongsTo(Hostel::class);
    }

    public function food(){
        return $this->belongsTo(Food::class);
    }
    public function foodstand(){
        return $this->belongsTo(Food::class);
    }

    public function moderator(){
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function campus(){
        return $this->belongsTo(Chapter::class, 'chapter');
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


	/**
	 * Scope a query to exclude certain columns
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $query
	 * @param  array|string $excludeColumns columns to exlude
	 * @param  string $alias change cloumn name
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopeExclude($query, $excludeColumns, $alias = '')
	{
		return $query->select(
			...array_merge(array_diff(Schema::getColumnListing('users'), (array) $excludeColumns), $alias)
		);
	}

	public function scopeAlias($query, $from, $to)
	{
		$columns = Schema::getColumnListing('users');
		$from = (array) $from;
		$to = (array) $to;
		if (count($from) == count($to))
			for ($i = 0; $i < count($from); $i++) {
				if (in_array($from[$i], $columns)) {
					$columns[array_search($from[$i], $columns)] = $from[$i] . ' AS ' . $to[$i];
				}
			}
		return $query->select(
			...$columns
		);
	}
	
	public function scopeForeign($query, $foreignKeys, $tables, $localKeys, $foreignColumns)
	{
		$foreignKeys = (array) $foreignKeys;
		$localKeys = (array) $localKeys;
		$tables = (array) $tables;
		$foreignColumns = (array) $foreignColumns;
		$columns = Schema::getColumnListing('users');

		if (count($foreignKeys) == count($localKeys)) {
			foreach ($columns as &$value) {
				if (in_array($value, $foreignKeys))
					$value =  $tables[array_search($value, $foreignKeys)] . '.' . $foreignColumns[array_search($value, $foreignKeys)] . ' AS table_users_on_table_' . $tables[array_search($value, $foreignKeys)] . '_column_' . $foreignColumns[array_search($value, $foreignKeys)];
				else
					$value = 'users.' . $value;
			}
			unset($value);
			$query = $query->select(...$columns);

			foreach ($tables as $index => $table) {
				$query = $query->leftJoin($table, 'users.' . $foreignKeys[$index], '=', $table . '.' . $localKeys[$index]);
			}
		}
		return $query;
	}
}
