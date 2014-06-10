<?php

class ExampleTest extends TestCase {

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testBasicExample()
	{
        $redis = \Operator\WriteApi::redis();

        $redis ->set('test',11);


        $this->assertSame($redis->get('test'),11);

	}

}