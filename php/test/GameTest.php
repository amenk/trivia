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

    public function testGoldenMaster()
    {

        for ($seed = 0; $seed < 500; $seed++) {
            $output = $this->getGameOutputForSeed($seed);
            $goldenMaster = $this->getGoldenMasterForSeed($seed);
            $this->assertSame($goldenMaster, $output);
        }
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
        } while (!$aGame->isGameFinished());
    }

    /**
     * @param $seed
     *
     * @return false|string
     */
    protected function getGameOutputForSeed($seed): string
    {
        srand($seed);

        ob_start();
        $this->runGame();
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    private function getGoldenMasterForSeed(int $seed): string
    {
        $file = __DIR__ . '/golden-master/seed-' . $seed . '.txt';
        if (! file_exists(dirname($file))) {
            mkdir(dirname($file), 0700, true);
        }

        if (! file_exists($file)) {
            $this->addWarning('Golden master for seed ' . $seed . ' did not exist');
            file_put_contents($file, $this->getGameOutputForSeed($seed));
        }

        return file_get_contents($file);
    }

}