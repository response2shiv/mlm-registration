<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreEnrollmentSelection extends Model {
	protected $table = 'pre_enrollment_selection';

	protected $fillable = [
		'userId',
		'productId'
	];

	protected $primaryKey = 'id';

	public static function getDays() {
		$days = [];
		for ( $x = 1; $x <= 31; $x ++ ) {
			$days[] = str_pad( $x, 1, '0', STR_PAD_LEFT );
		}

		return $days;
	}

	public static function getMonths() {
		return array(
			'01' => 'January',
			'02' => 'February',
			'03' => 'March',
			'04' => 'April',
			'05' => 'May',
			'06' => 'June',
			'07' => 'July',
			'08' => 'August',
			'09' => 'September',
			'10' => 'October',
			'11' => 'November',
			'12' => 'December'
		);
	}

}
