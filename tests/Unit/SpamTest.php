<?php

namespace Tests\Feature;

use App\Inecpections\Spam;
use App\Reply;
use App\Thread;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SpamTest extends TestCase
{
	use DatabaseMigrations;
	/** @test */
    public function test_detect_the_body_contains_Invalid_Keywords()
    {
    	$spam = new Spam;
    	$this->assertFalse($spam->detect("Yahoo  support"));
    }


    /** @test */
    public function test_throw_an_exception_if_key_was_holding_down () 
    {
    	$spam = new Spam;
    	$this->assertFalse($spam->detect("aaa"));
        $this->expectException(\Exception::class);
        $spam->detect('hello world aaaaaaaaaaa');
    }
    
    
}
