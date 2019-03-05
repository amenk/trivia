<?php declare(strict_types=1);


class GameTest extends \PHPUnit\Framework\TestCase
{

    public function testCanRunTheGameDeterministically()
    {
        $outputA = $this->getGameOutputForSeed(1);
        $outputB = $this->getGameOutputForSeed(1);
        $outputC = $this->getGameOutputForSeed(2);

        $this->assertSame($outputA, $outputB);
        $this->assertNotSame($outputA, $outputC);
    }

    /**
     * @return mixed
     */
    protected function runGame(): void
    {
        include_once __DIR__ . '/../Game.php';

        $aGame = new Game();
        $aGame->add("Chet");
        $aGame->add("Pat");
        $aGame->add("Sue");

        do {
            $aGame->roll(rand(0, 5) + 1);

            if (rand(0, 9) == 7) {
                $notAWinner = $aGame->wrongAnswer();
            } else {
                $notAWinner = $aGame->wasCorrectlyAnswered();
            }
        } while ($notAWinner);
    }

    /**
     * @param $seed
     *
     * @return false|string
     */
    protected function getGameOutputForSeed($seed)
    {
        srand($seed);

        ob_start();
        $this->runGame();
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

}