<?php

namespace Application;

class SocketWorker
{
	private $_socket;

	public function __construct($socket)
    {
        $this->_socket = $socket;
    }

    public function read(int $length = 2048): string
	{
        return trim(socket_read($this->_socket, $length, PHP_BINARY_READ));
	}

	public function write(string $message): bool
	{
		return (bool)socket_write($this->_socket, $message, strlen($message));
	}

	public function close()
    {
        socket_close($this->_socket);
    }
}