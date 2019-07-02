<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Conv {
	function toNum($str) {
		$limit = 5; //apply max no. of characters
		$colLetters = strtoupper($str); //change to uppercase for easy char to integer conversion
		$strlen = strlen($colLetters); //get length of col string
		if($strlen > $limit)	return "Column too long!"; //may catch out multibyte chars in first pass
		preg_match("/^[A-Z]+$/",$colLetters,$matches); //check valid chars
		if(!$matches)return "Invalid characters!"; //should catch any remaining multibyte chars or empty string, numbers, symbols
		$it = 0; $vals = 0; //just start off the vars
		for($i=$strlen-1;$i>-1;$i--){ //countdown - add values from righthand side
			$vals += (ord($colLetters[$i]) - 64 ) * pow(26,$it); //cumulate letter value
			$it++; //simple counter
		}
		return $vals; //this is the answer
	}
	
	function toStr($n,$case = 'upper') {
		$alphabet   = array(
			'A',	'B',	'C',	'D',	'E',	'F',	'G',
			'H',	'I',	'J',	'K',	'L',	'M',	'N',
			'O',	'P',	'Q',	'R',	'S',	'T',	'U',
			'V',	'W',	'X',	'Y',	'Z'
		);
		$n 			= $n;
		if($n <= 26){
			$alpha 	=  $alphabet[$n-1];
		} elseif($n > 26) {
			$dividend   = ($n);
			$alpha      = '';
			$modulo;
			while($dividend > 0){
				$modulo     = ($dividend - 1) % 26;
				$alpha      = $alphabet[$modulo].$alpha;
				$dividend   = floor((($dividend - $modulo) / 26));
			}
		}
		if($case=='lower'){
			$alpha = strtolower($alpha);
		}
		return $alpha;
	} 
	function romawi($integer, $upcase = true) {
		$table = array('M'=>1000, 'CM'=>900, 'D'=>500, 'CD'=>400, 'C'=>100, 'XC'=>90, 'L'=>50, 'XL'=>40, 'X'=>10, 'IX'=>9, 'V'=>5, 'IV'=>4, 'I'=>1); 
		$return = ''; 
		while($integer > 0) 
		{ 
			foreach($table as $rom=>$arb) 
			{ 
				if($integer >= $arb) 
				{ 
					$integer -= $arb; 
					$return .= $rom; 
					break; 
				} 
			} 
		} 
		return $return; 
	} 
	function hariIndo($date){ //date('N');
		$hariIndo 	= array("Senin","Selasa","Rabu","Kamis","Jum'at","Sabtu","Minggu");
		return $hariIndo[(int)$date-1];
	}
	function bulanIndo($date){ //date('m');
		$BulanIndo	= array("Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","Nopember","Desember");
		return $BulanIndo[(int)$date-1];
	}
	function tglIndo($date){
		if (!$date){
			$date	= date('Y-m-d');
		}
		$BulanIndo	= array("Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","Nopember","Desember");
		$tahun = substr($date, 0, 4);
		$bulan = substr($date, 5, 2);
		$tgl   = substr($date, 8, 2);
		
		$result = $tgl . " " . $BulanIndo[(int)$bulan-1] . " ". $tahun;		
		return($result);
	}
	function umur($date){
		$tz  = new DateTimeZone('Asia/Jakarta');
		$age = DateTime::createFromFormat('Y-m-d', $date, $tz)
	     		->diff(new DateTime('now', $tz))
    			->y;
		return $age;
	}
    function time_since($since) {
        $chunks = array(
            array(60 * 60 * 24 * 365 , 'year'),
            array(60 * 60 * 24 * 30 , 'month'),
            array(60 * 60 * 24 * 7, 'week'),
            array(60 * 60 * 24 , 'day'),
            array(60 * 60 , 'hour'),
            array(60 , 'minute'),
            array(1 , 'second')
        );

        for ($i = 0, $j = count($chunks); $i < $j; $i++) {
            $seconds = $chunks[$i][0];
            $name = $chunks[$i][1];
            if (($count = floor($since / $seconds)) != 0) {
                break;
            }
        }

        $print = ($count == 1) ? '1 '.$name : "$count {$name}s";
        return $print;
    }
    function ago($time){
	    date_default_timezone_set('Asia/Jakarta');
        $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
        $lengths = array("60","60","24","7","4.35","12","10");

        $now = time();

        $difference     = $now - $time;
        $tense         = "ago";

        for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
            $difference /= $lengths[$j];
        }

        $difference = round($difference);


        if($difference != 1) {
            $periods[$j].= "s";
        }
        if ($difference < 1) {
            return 'a moments ago';
        } elseif ($periods[$j] == 'day' || $periods[$j] == 'days' || $periods[$j] == 'week' || $periods[$j] == 'weeks' || $periods[$j] == 'month' || $periods[$j] == 'months' || $periods[$j] == 'year' || $periods[$j] == 'years' || $periods[$j] == 'decade' || $periods[$j] == 'decades'){
            return $this->tglIndo(date('Y-m-d',$time)).'&nbsp;'.date('H:i',$time);
        } else {
            return "$difference $periods[$j] ago ";
        }

    }
	function getmime($file){
		return mime_content_type($file);
	}
	function user_type($type){
		switch ($type){
			default		:
				null	:
			case 1		: $x = 'Guru'; break;
			case 50		: $x = 'Admin'; break;
			case 99		: $x = 'Super Admin'; break;
		}
		return $x;
	}
    function genPass($length=FALSE){
        if (!$length){
            $length 	= 8;
        }
        $characters = '0123456789ABCDEFGHJKLMNPRSTUVWXY';
        $string 	= "";
        for ($p = 0; $p < $length; $p++) {
            $string .= $characters[rand(0, strlen($characters)-1)];
        }
        return $string;
    }
    function predikat($x){
	    if ($x < 75){
	        $n = 'D';
        } elseif ($x < 83){
	        $n = 'C';
        } elseif ($x < 91){
	        $n = 'B';
        } else {
	        $n = 'A';
        }
        return $n;
    }
    function predikat10($x){
        if ($x < 60){
            $n = 'D';
        } elseif ($x < 70) {
            $n = 'C';
        } elseif ($x < 75){
            $n = 'B-';
        } elseif ($x < 80) {
            $n = 'B';
        } elseif ($x < 85) {
            $n = 'B+';
        } elseif ($x < 90) {
            $n = 'A-';
        } elseif ($x < 95) {
            $n = 'A';
        } else {
            $n = 'A+';
        }
        return $n;
    }
    function kurangbagus($x){
	    if ($x < 75){
	        $n = 'Kurang Sekali';
        } elseif ($x < 83){
	        $n = 'Cukup';
        } elseif ($x < 91){
	        $n = 'Baik';
        } else {
	        $n = 'Baik Sekali';
        }
        return $n;
    }
    function mampu($x){
	    if ($x < 75){
	        $n = 'Perlu peningkatan';
        } elseif ($x < 83){
	        $n = 'Cukup';
        } elseif ($x < 91){
	        $n = 'Mampu';
        } else {
	        $n = 'Sangat Mampu';
        }
        return $n;
    }
    function mampuK($x){
        if ($x < 75){
            $n = 'Perlu peningkatan';
        } elseif ($x < 83){
            $n = 'Cukup Mahir';
        } elseif ($x < 91){
            $n = 'Mahir';
        } else {
            $n = 'Sangat Mahir';
        }
        return $n;
    }
    function terbilang($x) {
        $angka = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
        if ($x < 12)
            return " " . $angka[$x];
        elseif ($x < 20)
            return $this->terbilang($x - 10) . " belas";
        elseif ($x < 100)
            return $this->terbilang($x / 10) . " puluh" . $this->terbilang($x % 10);
        elseif ($x < 200)
            return " seratus" . $this->terbilang($x - 100);
        elseif ($x < 1000)
            return $this->terbilang($x / 100) . " ratus" . $this->terbilang($x % 100);
        elseif ($x < 2000)
            return " seribu" . $this->terbilang($x - 1000);
        elseif ($x < 1000000)
            return $this->terbilang($x / 1000) . " ribu" . $this->terbilang($x % 1000);
        elseif ($x < 1000000000)
            return $this->terbilang($x / 1000000) . " juta" . $this->terbilang($x % 1000000);
    }
    function localize_phone($phone) {
        $depan      = str_split($phone,3);
        $belakang   = str_split($phone,3);
        return $depan[0].' '.$belakang[1].'-'.$belakang[2].'-'.$belakang[3].'-'.$belakang[4];
    }
    function localize_wa($phone) {
        $depan      = str_split($phone,3);
        $belakang   = str_split($phone,3);
        return $depan[0].' '.$belakang[1].'-'.$belakang[2].'-'.$belakang[3].'-'.$belakang[4];
    }
    function conv_skor($nilai){
	    $hasil = 0;
        $komanya = explode(".",$nilai);
	    if ($nilai >= 0 && $nilai < 0.1) {
            $hasil = 0;
        } elseif ($nilai >= 0.1 && $nilai < 0.3){
            $hasil = 20;
        } elseif ($nilai >= 0.3 && $nilai < 0.6){
	        $hasil = 30;
        } elseif ($nilai >= 0.6 && $nilai < 0.8){
            $hasil = 40;
        } elseif ($nilai >= 0.8 && $nilai < 1){
            $hasil = 50;
        } elseif ($nilai >= 1 && $nilai < 2){
	        $hasil = 60;
        } elseif ($nilai >= 2 && $nilai < 2.5){
            $hasil = 70;
        } elseif ($nilai >= 2.5 && $nilai < 3){
            $hasil = 80;
        } elseif ($nilai >= 3){
	        $hasil = 90;
        } else {
	        $hasil = 90;
        }
        if (count($komanya) > 1){
            $komanya = $komanya[1];
            $komanya = substr($komanya,0,1);
            $hasil  = $hasil + $komanya;
        }
        return $hasil;
    }
}