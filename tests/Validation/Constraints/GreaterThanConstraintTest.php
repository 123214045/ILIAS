<?php

/* Copyright (c) 2018 Richard Klees <richard.klees@concepts-and-training.de> Extended GPL, see docs/LICENSE */
require_once("libs/composer/vendor/autoload.php");

use ILIAS\Validation;
use ILIAS\Data;

class GreatherThanConstraintTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->df = new Data\Factory();
		$this->lng = $this->createMock(\ilLanguage::class);
		$this->f = new Validation\Factory($this->df, $this->lng);

		$this->greater_than = 10;

		$this->c = $this->f->greaterThan($this->greater_than);
	}

	public function testAccepts() {
		$this->assertTrue($this->c->accepts(12));
	}

	public function testNotAccepts() {
		$this->assertFalse($this->c->accepts(2));
	}

	public function testCheckSucceed() {
		$this->c->check(12);
		$this->assertTrue(true); // does not throw
	}

	public function testCheckFails() {
		$this->expectException(\UnexpectedValueException::class);
		$this->c->check(2);
	}

	public function testNoProblemWith() {
		$this->assertNull($this->c->problemWith(12));
	}

	public function testProblemWith() {
		$this->lng
			->expects($this->once())
			->method("txt")
			->with("not_greater_than")
			->willReturn("-%s-%s-");

		$this->assertEquals("-2-10-", $this->c->problemWith(2));
	}

	public function testRestrictOk() {
		$ok = $this->df->ok(12);

		$res = $this->c->restrict($ok);
		$this->assertTrue($res->isOk());
	}

	public function testRestrictNotOk() {
		$not_ok = $this->df->ok(2);

		$res = $this->c->restrict($not_ok);
		$this->assertFalse($res->isOk());
	}

	public function testRestrictError() {
		$error = $this->df->error("error");

		$res = $this->c->restrict($error);
		$this->assertSame($error, $res);
	}

	public function testWithProblemBuilder() {
		$new_c = $this->c->withProblemBuilder(function() { return "This was a fault"; });
		$this->assertEquals("This was a fault", $new_c->problemWith(2));
	}
}
