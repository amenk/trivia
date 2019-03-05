<?php declare(strict_types=1);


class GameTest extends \PHPUnit\Framework\TestCase
{

    public function testCanRunTheGameDeterministically()
    {
        ob_start();
        $this->runGame();
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertNotEmpty($output);
    }

    /**
     * @return mixed
     */
    protected function runGame()
    {
        return include __DIR__ . '/../GameRunner.php';
    }

}