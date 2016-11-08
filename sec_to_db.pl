#!/usr/bin/perl
#
# Skript pre automaticke ukladanie korelovanych udalosti do DB
# Toto je 1. verzia,, ktora pracuje s MySQL
# 
# V tomto stadiu projektu nemame urcene ako budu korelovane udalosti vyzerat,
# tak zatial ukladam okrem 'description' a 'timestamp' len modelove data.
# V nasledujucich sprintoch doladime.
$| = 1;

use strict;
use DBI();

# Deklaracia zakladnych premennych
my $fileName;
my $inputLine;
my $title;
my $type_id;

# Nastavenie DB
my $dbHost = "localhost";
my $dbDatabase = "sedb";
my $dbUser = "root";
my $dbPassword = "m1n2b3v4";

# Otvorime named pipe FIFO. Named pipe musi uz existovat
# Treba vytvorit prikazom mkfifo nazov_pipe
$fileName = "./SEC_fifo";
open(FIFO, "+< $fileName") or die "FIFO error on $fileName $!";

# Handle pre pripojenie k DB
my $dbConnectionHandle = DBI->connect("DBI:mysql:database=$dbDatabase;host=$dbHost", "$dbUser", "$dbPassword", {'RaiseError' => 1});

# Handle pre vkladanie do DB
my $insertHandle = $dbConnectionHandle->prepare_cached("INSERT INTO events (title, description, type_id) VALUES (?, ?, ?)");

while (<FIFO>)
{
	# Zatial blizsie nespecifikovane premenne
	$title = "title";
	$type_id = 1;
	
	# Nacita riadok do premennej
	$inputLine = $_;

	# Ak nacita quit ukonci cyklus (pre testovanie)
	last if($inputLine =~ /quit/i);

	# Odstrani posledny znak z retazca. V tomto pripade \n 
	chop $inputLine;
	
	# Insert do DB
	if($insertHandle->execute($title, $inputLine, $type_id)) {
		print "success: record was inserted\n";
	} else {
		print "error: record didn't inserted\n";
	}
}

exit;

