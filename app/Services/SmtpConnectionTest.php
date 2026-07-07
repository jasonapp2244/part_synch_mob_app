<?php

namespace App\Services;

class SmtpConnectionTest
{
    /**
     * Test SMTP connection with different configurations
     */
    public static function testConnection($host = 'smtp.gmail.com', $port = 587)
    {
        $results = [];
        
        // Test 1: Basic socket connection
        $results['socket_test'] = self::testSocket($host, $port);
        
        // Test 2: Try with stream context
        $results['stream_test'] = self::testStream($host, $port);
        
        // Test 3: Try port 465 with SSL
        if ($port == 587) {
            $results['port_465_test'] = self::testSocket($host, 465);
        }
        
        return $results;
    }
    
    private static function testSocket($host, $port)
    {
        $connection = @fsockopen($host, $port, $errno, $errstr, 10);
        if ($connection) {
            fclose($connection);
            return ['status' => 'success', 'message' => "Connected to {$host}:{$port}"];
        }
        return ['status' => 'failed', 'error' => "$errstr ($errno)"];
    }
    
    private static function testStream($host, $port)
    {
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ]
        ]);
        
        $connection = @stream_socket_client(
            "tcp://{$host}:{$port}",
            $errno,
            $errstr,
            10,
            STREAM_CLIENT_CONNECT,
            $context
        );
        
        if ($connection) {
            fclose($connection);
            return ['status' => 'success', 'message' => "Stream connected to {$host}:{$port}"];
        }
        return ['status' => 'failed', 'error' => "$errstr ($errno)"];
    }
}


