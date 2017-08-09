<?php

namespace http;

use firestark\app;

class kernel
{
	private $app = null;

	public function __construct ( app $app )
	{
		$this->app = $app;
	}

	public function handle ( request $request ) : response
	{
		if ( ! $this->app [ 'router' ]->has ( ( string ) $request ) )
			return $this->handleMissing ( $request );
		
		$value = $this->app->call ( $this->app [ 'router' ]->match ( ( string ) $request ) );
		return $this->createResponse ( $value );
	}

	private function createResponse ( $result ) : response
	{
		 if ( is_string ( $result ) )
            return $this->handleString ( $result );
        if ( is_array ( $result ) )
            return $this->handleArray ( $result );
        throw new exception ( 'The return value of your route can not be handled by the kernel.' );
	}

	private function handleString ( string $result ) : response
    {
        $type = ( $result !== strip_tags ( $result ) ) 
            ? 'text/html' : 'text/plain';

        return $this->respond ( $result, 200, $type );
    }
    
    private function handleArray ( array $result ) : response
    {
        return $this->respond ( json_encode ( $result ), 200, 'application/json' );
    }

	private function handleMissing ( $request ) : response
	{
		return $this->respond ( '<h1>The resource you where looking for does not exist.</h1>', 404, 'text/html' );
	}

	private function respond ( string $content, int $status, string $type ) : response
    {
        $response = new response ( $content, $status );
        $response [ 'Content-Type' ] = $type;
        return $response;
    }
}