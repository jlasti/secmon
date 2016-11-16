#!/usr/bin/perl
#
########################################################################
# Nazov:
#	secdb.pl
#
# Veriza:
#	2.0
#
# Popis:
#	Jednoduchy skript pre ukladanie korelovanych logov z nastroa SEC do
#	databazy. Na prepojenie vystupu zo SECu a tohto skriptu sluzi named
#	pipe. SEC do nej zapise, skript precita a ulozi do DB. S vyuzitim
#	systemd (init daemon) bezi skript ako sluzba na pozadi a je aktivny
#	stale
#
########################################################################
$| = 1;

use strict;
use DBI();

# Pridavna kniznica na spracovanie konfiguracneho suboru
use Config::IniFiles;

my $fileName;
my $inputLine;
my $title;
my $type_id;

# Otvorenie konfigu
my $cfg = Config::IniFiles->new(-file => "/home/st3lly/sec_test/config.ini") or die $!;

# Nacitanie nastaveni z konfigu
my $dbHost = $cfg->val('db', 'host');
my $dbDatabase = $cfg->val('db', 'dbname');
my $dbUser = $cfg->val('db', 'user');
my $dbPassword = $cfg->val('db', 'password');
my $logDir = $cfg->val('log', 'dir');
my $logFile = $cfg->val('log', 'file');
my $fifoDir = $cfg->val('fifo', 'dir');
my $fifoFile = $cfg->val('fifo', 'file');

# Funkcia pre krajsi timestamp pri logoch
sub getLogTime
{
	my ($sec, $min, $hour, $mday, $mon, $year, $wday, $yday, $isdst) = localtime(time);
	my $niceTimestamp = sprintf("%04d%02d%02d %02d:%02d:%02d", $year+1900, $mon + 1, $mday, $hour, $min, $sec);

	return $niceTimestamp;
}

# Otvorim logovaci subor pre zapisovanie
open(my $log, '>>', $logDir . $logFile) or die "Log file error: $!";

# Otvorim named pipe kam SEC zapisuje
if(!open(FIFO, '+<', $fifoDir . $fifoFile)) {
	printf $log "%s: %s%s: %s\n", &getLogTime(), $fifoDir, $fifoFile, $!;
	exit 1;
}

# Handle pre pripojenie k DB
my $dbConnectionHandle = DBI->connect("DBI:mysql:database=$dbDatabase;host=$dbHost", "$dbUser", "$dbPassword", {PrintError => 0, RaiseError => 0});
if(!$dbConnectionHandle)
{
	printf $log "%s: Could not connect to database!\n", &getLogTime();
	exit 1;
}

# Handle pre vkladanie do DB
my $insertHandle = $dbConnectionHandle->prepare_cached("INSERT INTO events (title, description, type_id) VALUES (?, ?, ?)");

while (<FIFO>)
{
	# Zatial blizsie nespecifikovane premenne
	$title = "title";
	$type_id = 1;
	
	# Nacita riadok do premennej
	$inputLine = $_;

	# Odstrani posledny znak z retazca. V tomto pripade \n 
	chop $inputLine;
	
	# Insert do DB
	$insertHandle->execute($title, $inputLine, $type_id);
	#if(!)
	#{
	#	printf $log "%s: Inserting error: %s\n", &getLogTime(), $insertHandle->errstr;
	#}
}

exit;

