<?php

require __DIR__ . '/../index.php';

use App\Managers\FileManager;
use PHPUnit\Framework\TestCase;

final class ApplicationTest extends TestCase
{
	public function __construct()
	{
		parent::__construct();

		global $app;
		$this->app = $app;
	}

    /**
     * Test the application has been loaded properly
     *
     * @return void
     */
    public function testAreManagersInitialized(): void
    {
        $this->assertInstanceOf(
            FileManager::class,
            $this->app['managers']['file']
        );
    }
}
