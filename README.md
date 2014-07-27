# TimeControl
Simple manual
TimeControl is a simple class for validating time.
It can be used in access control system to determine if a user can login or not
depending on what time it is.

## Functions
### Clean();
Removes all filters.
 
### AddFilter($filter, $onlyNow);
Adds a new filter.

The filter is composed as follows:

```{action};{year};{month};{day};{start};{end}```

Action: 0 gives false and 1 gives true on a match
* Year: The year with four digits or "\*" for any year
* Month: The number of the month or "\*" for any month. It is possible to use ","
  to separate more than one month "1,2" means january and february. It is also
  possible to use "-" to indicate a span "1-4" means january through april.
  If month begins with "w:" then it indicated weeknumber instead, year will
  become obsolete and day will indicate day of week.
* Day: The numerical value of the date or "\*" for any date. It is possible to use
  "," to separate more than one date "1,2" means "1" and "2". It is also
  possible to use "-" to indicate a span "1-4" means "1st" through "4th".
  If day begins with "w:" then it indicated day of week instead, year will
  become obsolete as well as month.
  The short abbreviation for weekdays (mon,tue,wed,thu,fri,sat,sun) will be
  replaced by its corresponding number for day of week.
  Please note that a 0 for day of week means monday and 6 means sunday. This
  results in a logical connection for normal workingdays (monday through friday)
  as 0 through 4 and weekends (saturday and sunday) as 5 and 6.
* Start: Time when event should start. Hours and minutes separated by a ":". A "\*"
  indicates "00:00". If time is given then it must contain a valute for both
  hour and minute.
* End: Time when event should end. Hours and minutes separated by a ":". A "\*"
  indicates "23:59". If time is given then it must contain a valute for both
  hour and minute.

When validation is performed it matches
```((current time >= start time) and (current time <= end time))```

If $onlyNow is set to true then only filters valid at the registered time will
be added.

### GetFilterArray();
Returns the filters added sofar as an array. This array can be used as an
argument for SetFilterArray which means that it is possible to store the
filterarray in a database and load it at an appropriate time.

### SetFilterArray($filter);
Sets the internal filters. The filter supplied as an argument should be an
result from GetFilterArray.

### Validate();
A call to Validate will go through all filters to check if there is a match to
the current time. If a match is found that will render the result false the rest
of the filters will not be checked.
Validate can return false if a match for false is found.
If one or more results is found that is a match for true then Validate will
return true.
If no match for either true or false is found then Validate will return 0.

```php
<?php
include("timecontrol.php");
$tc = new TimeControl();
$tc->AddFilter('0;*;*;w:0-4;*;07:59', false); // Any monday through friday from
                                              // midnight through 07:59
$tc->AddFilter('0;*;*;w:0-4;18:00;*', false); // Any monday through friday from
                                              // 18:00 through midnight
$tc->AddFilter('0;*;*;w:5-6;*;*', false); // Any saturday or sunday
$tc->AddFilter('1;*;*;w:0-4;09:00;17:59', false); // Any monday through friday
                                                  // from 09:00 through 17:59
$tc->AddFilter('0;*;w:22;mon-wed;*;*', false); // All day on monday through
                                               // wednesday in week 22
$result = $tc->Validate();
if($result == true) {
  // A match is found indicating true
  // This occurs on mondays through fridays between 09:00 and 17:59 (except for
  // week 22 on monday through wednesday)
} elseif($result === false) {
  // A match is found indicating false
} else {
  // No match is found
  // This can occur monday through friday between 08:00 and 08:59 (except for
  // week 22 on monday through wednesday)
}
?>
```