<?php
require "templates/dbi_acm.php";
require "templates/header_acm.php";

function multiline($str){
    return str_replace("\n", "<br>", htmlspecialchars($str));
}

print('<div class="card">');
$query = "SELECT * FROM PERSONS;";
print('<h1>Select All Fields From Persons</h1>');
print('<p class="sql-code">' . multiline($query) . '</p>');
$result = $conn->query($query);

tableify($result);
print('</div>');

print('<div class="card">');
$query = "SELECT * FROM MEMBERSHIPS;";
print('<h1>Select All Fields From Memberships</h1>');

print('<p class="sql-code">' . multiline($query) . '</p>');
$result = $conn->query($query);

tableify($result);
print('</div>');

print('<div class="card">');
$query = "SELECT * FROM PERSON_MEMBERSHIP;";
print('<h1>Select All Fields From PERSON_MEMEBERSHIP</h1>');

print('<p class="sql-code">' . multiline($query) . '</p>');
$result = $conn->query($query);

tableify($result);
print('</div>');

print('<div class="card">');
$query = "SELECT * FROM SCHOOLS;";
print('<h1>Select All Fields From SCHOOLS</h1>');

print('<p class="sql-code">' . multiline($query) . '</p>');
$result = $conn->query($query);

tableify($result);
print('</div>');

print('<div class="card">');
$query = "SELECT * FROM PERSON_SCHOOL;";
print('<h1>Select All Fields From PERSON_SCHOOL</h1>');

print('<p class="sql-code">' . multiline($query) . '</p>');
$result = $conn->query($query);

tableify($result);
print('</div>');

print('<div class="card">');
$query = "SELECT * FROM CITIZENSHIP;";
print('<h1>Select All Fields From CITIZENSHIP</h1>');

print('<p class="sql-code">' . multiline($query) . '</p>');
$result = $conn->query($query);

tableify($result);
print('</div>');

print('<div class="card">');
$query = "SELECT * FROM PERSON_CITIZENSHIP;";
print('<h1>Select All Fields From PERSON_CITIZENSHIP</h1>');

print('<p class="sql-code">' . multiline($query) . '</p>');
$result = $conn->query($query);

tableify($result);
print('</div>');

print('<div class="card">');
$query = "SELECT PERSONS.FULL_NAME, SUM(MEMBERSHIPS.FEE) DUES
FROM PERSONS
INNER JOIN PERSON_MEMBERSHIP
ON PERSONS.PERSON_ID = PERSON_MEMBERSHIP.PERSON_ID
INNER JOIN MEMBERSHIPS
ON MEMBERSHIPS.MEMBERSHIP_ID = PERSON_MEMBERSHIP.MEMBERSHIP_ID
WHERE PERSON_MEMBERSHIP.STATUS in ('LAPSED', 'NEW')
GROUP BY PERSONS.PERSON_ID
HAVING SUM(MEMBERSHIPS.FEE) > 0;";
print('<h1>Print all members with outstanding fees greater than 0.00</h1>');

print('<p class="sql-code">' . multiline($query) . '</p>');
$result = $conn->query($query);

tableify($result);
print('</div>');

print('<div class="card">');
print('<h1>List Highest Level of School for each Person</h1>');

$query = "SELECT PERSONS.FULL_NAME, MIN(PERSON_SCHOOL.DEGREE_RANK) HIGHEST_DEGREE 
FROM PERSON_SCHOOL
INNER JOIN PERSONS
ON PERSONS.PERSON_ID = PERSON_SCHOOL.PERSON_ID
GROUP BY PERSONS.PERSON_ID;";
print('<p class="sql-code">' . multiline($query) . '</p>');
$result = $conn->query($query);

tableify($result);
print('</div>');

print('<div class="card">');
print('<h1>List all the ACM members who also work in the office of the representative</h1>');

$query = "SELECT FULL_NAME OFFICE_OF_REP_EMPLOYEE
from PERSONS
inner join EMPLOYEES
on PERSONS.PERSON_ID = EMPLOYEES.PERSON_ID
WHERE EMPLOYEES.REP_OFFICE = True;";
print('<p class="sql-code">' . multiline($query) . '</p>');
$result = $conn->query($query);

tableify($result);
print('</div>');

print('<div class="card">');
print('<h1>List all the ACM members who also work in the office of the chapter coordinator</h1>');

$query = "SELECT FULL_NAME OFFICE_OF_COOR_EMPLOYEE
from PERSONS
inner join EMPLOYEES
on PERSONS.PERSON_ID = EMPLOYEES.PERSON_ID
WHERE EMPLOYEES.COOR_OFFICE = True;";
print('<p class="sql-code">' . multiline($query) . '</p>');
$result = $conn->query($query);

tableify($result);
print('</div>');
print('<h1>List all the member who live in TEXAS</h1>');

print('<div class="card">');
$query = "select FULL_NAME MEMBERS_IN_TEXAS
from PERSONS
where STATE = 'TEXAS';";
print('<p class="sql-code">' . multiline($query) . '</p>');
$result = $conn->query($query);

tableify($result);
print('</div>');

print('<div class="card">');
print('<h1>List all the members who live in Texas and are Indians</h1>');

$query = "SELECT FULL_NAME INDIANS_IN_TEXAS
from PERSONS
INNER JOIN PERSON_CITIZENSHIP
ON PERSON_CITIZENSHIP.PERSON_ID = PERSONS.PERSON_ID
INNER JOIN CITIZENSHIP
ON PERSON_CITIZENSHIP.CITIZENSHIP_ID = CITIZENSHIP.CITIZENSHIP_ID
where STATE = 'TEXAS' and CITIZENSHIP.NAME = 'India';";
print('<p class="sql-code">' . multiline($query) . '</p>');
$result = $conn->query($query);

tableify($result);
print('</div>');

print('<div class="card">');
print('<h1>List all members who hold phds</h1>');

$query = "SELECT FULL_NAME DOCTORS
FROM PERSONS
INNER JOIN PERSON_SCHOOL
ON PERSON_SCHOOL.PERSON_ID = PERSONS.PERSON_ID
WHERE PERSON_SCHOOL.DEGREE_RANK = 'DOCTORATE'
GROUP BY PERSONS.PERSON_ID;";
print('<p class="sql-code">' . multiline($query) . '</p>');
$result = $conn->query($query);

tableify($result);
print('</div>');

print('<div class="card">');
print('<h1>List all the members who hold youth membership</h1>');

$query = "SELECT FULL_NAME YOUTH_MEMBERS
FROM PERSONS
INNER JOIN PERSON_MEMBERSHIP
ON PERSON_MEMBERSHIP.PERSON_ID = PERSONS.PERSON_ID
INNER JOIN MEMBERSHIPS
ON PERSON_MEMBERSHIP.MEMBERSHIP_ID = MEMBERSHIPS.MEMBERSHIP_ID
WHERE MEMBERSHIPS.MEMBERSHIP_TYPE = 'YOUTH'
and PERSON_MEMBERSHIP.STATUS = 'CURRENT';";
print('<p class="sql-code">' . multiline($query) . '</p>');
$result = $conn->query($query);

tableify($result);
print('</div>');

print('<div class="card">');
print('<h1>List all the members between the ages of 18 and 35</h1>');

$query = "SELECT FULL_NAME YOUNG_ADULTS,
TIMESTAMPDIFF(YEAR, DOB, current_date) AGE
FROM PERSONS
WHERE TIMESTAMPDIFF(YEAR, DOB, current_date) between 18 and 35;";
print('<p class="sql-code">' . multiline($query) . '</p>');
$result = $conn->query($query);

tableify($result);
print('</div>');

print('<div class="card">');
print('<h1>List all the members who are Americans</h1>');

$query = "SELECT FULL_NAME AMERICANS
from PERSONS
INNER JOIN PERSON_CITIZENSHIP
ON PERSON_CITIZENSHIP.PERSON_ID = PERSONS.PERSON_ID
INNER JOIN CITIZENSHIP
ON PERSON_CITIZENSHIP.CITIZENSHIP_ID = CITIZENSHIP.CITIZENSHIP_ID
where CITIZENSHIP.NAME = 'USA';";
print('<p class="sql-code">' . multiline($query) . '</p>');
$result = $conn->query($query);

tableify($result);
print('</div>');

print('<div class="card">');
print('<h1>List all the members who are Americans and are South Sudanese</h1>');

$query = "SELECT FULL_NAME AMERICAN_AND_SOUTH_SUDAN
from PERSONS
INNER JOIN PERSON_CITIZENSHIP
ON PERSON_CITIZENSHIP.PERSON_ID = PERSONS.PERSON_ID
INNER JOIN CITIZENSHIP
ON PERSON_CITIZENSHIP.CITIZENSHIP_ID = CITIZENSHIP.CITIZENSHIP_ID
where CITIZENSHIP.NAME in ('USA', 'South Sudan')
GROUP BY PERSONS.PERSON_ID
HAVING count(*)=2;";
print('<p class="sql-code">' . multiline($query) . '</p>');
$result = $conn->query($query);

tableify($result);
print('</div>');

print('<div class="card">');
print('<h1>List all the members who join ACM in 2018</h1>');

$query = "SELECT FULL_NAME NEW_JOINERS
FROM PERSONS
WHERE YEAR(JOIN_DATE) = 2018;";
print('<p class="sql-code">' . multiline($query) . '</p>');
$result = $conn->query($query);

tableify($result);
print('</div>');

print('<div class="card">');
print('<h1>List email of all who report to Jane</h1>');

$query = "SELECT FULL_NAME, EMAIL
From EMPLOYEES
inner join PERSONS
on PERSONS.PERSON_ID = EMPLOYEES.PERSON_ID
inner join REPORTS
on REPORTS.reporter = EMPLOYEES.person_id
WHERE REPORTS.reportee = 2;";
print('<p class="sql-code">' . multiline($query) . '</p>');
$result = $conn->query($query);

tableify($result);
print('</div>');


$conn->close();

require 'templates/footer_acm.php';
?>
