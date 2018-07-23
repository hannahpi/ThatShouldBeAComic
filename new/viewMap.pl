#!/usr/bin/perl -w
use DBI;

print "Content-type: text/html\n\n";
print "\n<br />Attempting Database Connection";
$dbh = DBI->connect('dbi:mysql:dreamre2_comicReq','dreamre2_comicR','comicR')
or die "<br />Connection Error: $DBI::errstr\n";
print "\n<br />Preparing SQL statement";
$sql = "SELECT * FROM  `ImageMapper`";
print "\n<br />Preparations complete";
$sth = $dbh->prepare($sql);
print "\n<br />Executing...";
$sth->execute
or die "SQL Error: $DBI::errstr\n";
print "\n<br />OK";
print "\n<br />Seems like the connection should be ok.<br /><br />";
print "\n<table border=1>";
print "\n<tr><b><u>";
print "\n    <td>ImgID</td>";
print "\n    <td>MapID</td>";
print "\n    <td>startX</td>";
print "\n    <td>startY</td>";
print "\n    <td>endX</td>";
print "\n    <td>endY</td>";
print "\n    <td>link</td>";
print "\n</tr></b>";
while (@row = $sth->fetchrow_array) {
  print "\n<tr>";
  for ($i=0; $i<=$#row; $i++){
    print "\n   <td> $row[$i]</td>";
  }
  print "\n</tr>";
}
print "\n</table>";