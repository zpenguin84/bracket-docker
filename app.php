<?php

require 'vendor/autoload.php';

use BracketValidator\BracketValidator as B;
use Application\SocketServer;
use Application\Config;
use Application\Log;

$port = Config::getPort();
if (!is_int($port) || $port > 65535 || $port <= 1024)
{
	echo "Incorrect value for port. Check your config.\n";
	exit;
}


$server = new SocketServer();

try
{
    $server->listen(Config::getHost(), $port);
}
catch (\Throwable $e)
{
    Log::write('Error starting PHP Bracket Server');
    Log::write($e->getMessage());
    exit;
}

Log::write('Server started on ' . $server->getHost() . ':' . $server->getPort() . '. Pid: ' . posix_getpid() . '.');


pcntl_signal(SIGCHLD, function () {
    $childId = pcntl_wait($status);
    $exitStatus = pcntl_wexitstatus($status);
    Log::write("Connection closed. Pid: $childId ($exitStatus).");
});

pcntl_signal(SIGHUP, function() use ($server) {
    $port = Config::getPort();
    if ($server->getPort() == $port)
        return;
    $server->close();
    $server->create();
    try
    {
        $server->listen(Config::getHost(), $port);
    }
    catch (\Throwable $e)
    {
        Log::write('Error changing port: ' . $port . '.');
        Log::write($e->getMessage());
        exit;
    }
    Log::write('Port was changed: ' . Config::getPort() . '.');
});


while (true)
{
	$worker = $server->accept();
	if (is_null($worker))
	{
		pcntl_signal_dispatch();
		usleep(200000);
		continue;
	}

	$pid = pcntl_fork();
	if ($pid == 0)
	{
		Log::write('New connection. Pid: ' . posix_getpid() . '. Port: ' . $server->getPort() . '.');
		$worker->write("\nWelcome to PHP Bracket Server!\n");
		while (true)
		{
			$input = $worker->read();

			if ($input == 'quit' || $input == 'exit')
			{
				$worker->close();
				exit;
			}

			try
			{
				sleep(mt_rand(2,5));
				$result = B::process($input) ? 'TRUE' : 'FALSE';
			}
			catch (InvalidArgumentException $e)
			{
				$result = 'Invalid Argument in position: ' . $e->getMessage();
			}

			$worker->write($result . "\n\n");
		}
	}
}
