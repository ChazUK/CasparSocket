<?php namespace ChazUK\CasparSocket;

class CasparSocket {

    private $hostname;
	private $port;
	private $socket = FALSE;
	private $connected = FALSE;
	private $eol = "\r\n";
	private $byte_length = 262144;

	public function __construct($hostname, $port = 5250)
	{
		$this->hostname = $hostname;
		$this->port = $port;

		$this->connect();
	}

	public function __destruct()
	{
		$this->disconnect();
	}

    private function connect()
	{
		if ($this->connected)
			return TRUE;

		$this->socket = fsockopen($this->hostname, $this->port, $errno, $errstr, 5);

		if ( ! $this->socket)
    		die("ERROR: " . $errstr . " (" . $errstr . ")");

        $this->connected = TRUE;

		return TRUE;
	}

    public function send($command)
	{
		if ( ! $this->connected)
			return FALSE;

        $response['command'] = $command;

		fwrite($this->socket, $response['command'] . $this->eol);

        $response['raw'] = trim(fgets($this->socket));
        $response['status'] = substr($response['raw'], 0, 3);
        $end = FALSE;

		switch ($response['status'])
		{
			case 200:
				$end = $this->eol.$this->eol;
				break;
			case 201:
				$end = $this->eol;
				break;
		}

		if ($end)
			$response['data'] = stream_get_line($this->socket, $this->byte_length, $end);

		return $response;
	}

	public function disconnect()
	{
	    if ( ! $this->connected)
	        return TRUE;

		fclose($this->socket);

		$this->connected = FALSE;

		return TRUE;
	}

}
