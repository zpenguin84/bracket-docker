<?php

namespace Application;

class SocketServer
{
	private $_socket;
	private $_host;
	private $_port;

	public function __construct()
	{
		$this->create();
	}

    public function getHost(): string
    {
        return $this->_host;
    }

    public function getPort(): int
    {
        return $this->_port;
    }

	public function listen(string $host, int $port)
	{
        $this->_host = $host;
        $this->_port = $port;
		if (socket_bind($this->_socket, $this->_host, $this->_port) === false)
			throw new \Exception('Could not bind socket');
		socket_listen($this->_socket, 1);
	}

	public function accept(): ?SocketWorker
    {
        $socket = socket_accept($this->_socket);
        return $socket === false ? null : new SocketWorker($socket);
    }

    public function create()
    {
        $this->_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_nonblock($this->_socket);
    }

    public function close()
    {
        socket_close($this->_socket);
    }
}