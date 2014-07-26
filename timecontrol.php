/*******************************************************************************
*    TimeControl is free software; you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation; either version 2 of the License, or
*    (at your option) any later version.
*
*    TimeControl is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with TimeControl; if not, write to the Free Software
*    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*******************************************************************************/
class TimeControl {
  protected $filter;
  
  function TimeControl() {
    $this->filter = array();
  }
  
  function __destruct() {
    unset($this->filter);
  }
  
  function Clean() {
    unset($this->filter);
    $this->filter = array();
  }
  
  function AddFilter($filter = '*', $nowOnly = false) {
    $filter = strtolower($filter);
    $filter = str_replace(array('mon','tue','wed','thu','fri','sat','sun'), array(0,1,2,3,4,5,6), $filter);
    $filters = explode('|', $filter);
    foreach($filters as $filt) {
      list($action, $year, $month, $day, $start, $stop) = explode(';', $filt, 6);
      if(preg_match('/^w\:/i', $month)) {
        $preg = '/^';
        $month = substr($month, 2);
        if($month == '*') {
          $preg .= '([0][1-9]|[1-4][0-9]|[5][0-3])';
        } else {
          $parts = explode(',', $month);
          $res = array();
          foreach($parts as $part) {
            if(strpos($part, '-') !== false) {
              list($from, $to) = explode('-', $part);
              for($i = intval($from); $i <= intval($to); $i++) {
                $res[] = $i;
              }
            } else {
              $res[] = $part;
            }
          }
          $preg .= '('.implode('|', $res).')';
        }
        if($day == '*') {
          $preg .= '([0-6])';
        } else {
          $parts = explode(',', $day);
          $res = array();
          foreach($parts as $part) {
            if(strpos($part, '-') !== false) {
              list($from, $to) = explode('-', $part);
              for($i = intval($from); $i <= intval($to); $i++) {
                $res[] = $i;
              }
            } else {
              $res[] = $part;
            }
          }
          $preg .= '('.implode('|', $res).')';
        }
        $preg .= '$/';
        if($start == '*') {
          $start = 0;
        } else {
          list($hour, $minute) = explode(':', $start);
          $start = (intval($hour) * 60) + intval($minute);
        }
        if($stop == '*') {
          $stop = 1439;
        } else {
          list($hour, $minute) = explode(':', $stop);
          $stop = (intval($hour) * 60) + intval($minute);
        }
        if($nowOnly) {
          if(!preg_match($preg, date('W'))) {
            continue;
          }
          $time = intval(date('G')) * 60 + intval(date('i'));
          if(($time < $start) || ($time > $stop)) {
            continue;
          }
        }
        $this->filter[] = array(2, $preg, ($action == true)?1:0, $start, $stop);
      } elseif(preg_match('/^w\:/i', $day)) {
        $preg = '/^';
        $day = substr($day, 2);
        if($day == '*') {
          $preg .= '([0-6])';
        } else {
          $parts = explode(',', $day);
          $res = array();
          foreach($parts as $part) {
            if(strpos($part, '-') !== false) {
              list($from, $to) = explode('-', $part);
              for($i = intval($from); $i <= intval($to); $i++) {
                $res[] = $i;
              }
            } else {
              $res[] = $part;
            }
          }
          $preg .= '('.implode('|', $res).')';
        }
        $preg .= '$/';
        if($start == '*') {
          $start = 0;
        } else {
          list($hour, $minute) = explode(':', $start);
          $start = (intval($hour) * 60) + intval($minute);
        }
        if($stop == '*') {
          $stop = 1439;
        } else {
          list($hour, $minute) = explode(':', $stop);
          $stop = (intval($hour) * 60) + intval($minute);
        }
        if($nowOnly) {
          if(!preg_match($preg, date('w'))) {
            continue;
          }
          $time = intval(date('G')) * 60 + intval(date('i'));
          if(($time < $start) || ($time > $stop)) {
            continue;
          }
        }
        $this->filter[] = array(0, $preg, ($action == true)?1:0, $start, $stop);
      } else {
        $preg = '/^';
        if($year == '*') {
          $preg .= '([0-9]{4})';
        } else {
          $preg .= "($year)";
        }
        $preg .= '\:';
        if($month == '*') {
          $preg .= '([0][1-9]|[1][0-2])';
        } else {
          $parts = explode(',', $month);
          $res = array();
          foreach($parts as $part) {
            if(strpos($part, '-') !== false) {
              list($from, $to) = explode('-', $part);
              for($i = intval($from); $i <= intval($to); $i++) {
                $res[] = str_pad(intval($i), 2, '0', STR_PAD_LEFT);
              }
            } else {
              $res[] = str_pad(intval($part), 2, '0', STR_PAD_LEFT);
            }
          }
          $preg .= '('.implode('|', $res).')';
        }
        $preg .= '\:';
        if($day == '*') {
          $preg .= '([0][1-9]|[1][0-9]|[2][0-9]|[3][0-1])';
        } else {
          $parts = explode(',', $day);
          $res = array();
          foreach($parts as $part) {
            if(strpos($part, '-') !== false) {
              list($from, $to) = explode('-', $part);
              for($i = intval($from); $i <= intval($to); $i++) {
                $res[] = str_pad(intval($i), 2, '0', STR_PAD_LEFT);
              }
            } else {
              $res[] = str_pad(intval($part), 2, '0', STR_PAD_LEFT);
            }
          }
          $preg .= '('.implode('|', $res).')';
        }
        $preg .= '$/';
        if($start == '*') {
          $start = 0;
        } else {
          list($hour, $minute) = explode(':', $start);
          $start = (intval($hour) * 60) + intval($minute);
        }
        if($stop == '*') {
          $stop = 1439;
        } else {
          list($hour, $minute) = explode(':', $stop);
          $stop = (intval($hour) * 60) + intval($minute);
        }
        if($nowOnly) {
          if(!preg_match($preg, date('Y:m:d'))) {
            continue;
          }
          $time = intval(date('G')) * 60 + intval(date('i'));
          if(($time < $start) || ($time > $stop)) {
            continue;
          }
        }
        $this->filter[] = array(1, $preg, ($action == true)?1:0, $start, $stop);
      }
    }
  }
  
  function SetFilterArray($filter) {
    unset($this->filter);
    $this->filter = $filter;
  }
  
  function GetFilterArray() {
    return $this->filter;
  }
  
  // Will return true if valid
  // false if not valid
  // 0 if no match found
  function Validate() {
    $date = date('Y:m:d');
    $time = intval(date('G')) * 60 + intval(date('i'));
    $dow = (date('w') + 6) % 7;
    $week = str_pad(date('W'), 2, '0', STR_PAD_LEFT).$dow;
    $action = 1;
    foreach($this->filter as $filter) {
      if($filter[0] == 0) {
        if(preg_match($filter[1], $dow)) {
          if(($time >= $filter[3]) && ($time <= $filter[4])) {
            $action = $action && $filter[2];
            if($action == 0) {
              return false;
            }
          }
        }
      } elseif($filter[0] == 1) {
        if(preg_match($filter[1], $date)) {
          if(($time >= $filter[3]) && ($time <= $filter[4])) {
            $action = $action && $filter[2];
            if($action == 0) {
              return false;
            }
          }
        }
      } elseif($filter[0] == 2) {
        if(preg_match($filter[1], $week)) {
          if(($time >= $filter[3]) && ($time <= $filter[4])) {
            $action = $action && $filter[2];
            if($action == 0) {
              return false;
            }
          }
        }
      }
    }
    if(is_bool($action)) {
      return $action;
    }
    return 0;
  }
  
}