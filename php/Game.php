<?php
function echoln($string)
{
    echo $string . "\n";
}

class Game
{
    private $players = [];
    private $places = [0];
    private $purses = [0];
    private $inPenaltyBox = [0];

    private $currentPlayer = 0;
    private $isGettingOutOfPenaltyBox;
    private static $categories =
        [
            'Pop' => [0, 4, 8], 'Science' => [1, 5, 9], 'Sports' => [2, 6, 10], 'Rock' => [3, 7, 11]
        ];

    private $questions = [];

    public function __construct()
    {
        $this->initializeQuestions();
    }

    private function initializeQuestions(): void
    {
        for ($i = 0; $i < 50; $i++) {
            foreach (array_keys(self::$categories) as $category) {
                $this->questions[$category][] = $category . ' Question ' . $i;
            }
        }
    }

    public function add(string $playerName): void
    {
        $this->players[] = $playerName;
        $this->places[$this->countPlayers()] = 0;
        $this->purses[$this->countPlayers()] = 0;
        $this->inPenaltyBox[$this->countPlayers()] = false;

        echoln($playerName . " was added");
        echoln("They are player number " . $this->countPlayers());
    }

    private function countPlayers(): int
    {
        return count($this->players);
    }

    public function roll($roll)
    {
        echoln($this->players[$this->currentPlayer] . " is the current player");
        echoln("They have rolled a " . $roll);

        if ($this->inPenaltyBox[$this->currentPlayer]) {
            $this->maybeLetOutOfPenaltyBox($roll);
        }

        if ( ! $this->inPenaltyBox[$this->currentPlayer] || $this->isGettingOutOfPenaltyBox) {
            $this->advanceCurrentPlayer($roll);
        }

    }


    private function askQuestion(): void
    {
        echoln(array_shift($this->questions[$this->currentCategory()]));
    }

    private function currentCategory(): string
    {
        $place = $this->places[$this->currentPlayer];

        foreach(self::$categories as $category=>$fields) {
            if (in_array($place, $fields, true)) {
                return $category;
            }
        }
    }

    public function wasCorrectlyAnswered()
    {
        if ($this->inPenaltyBox[$this->currentPlayer] && !  $this->isGettingOutOfPenaltyBox) {
            $this->nextPlayer();

            return true;
        }

        if ($this->inPenaltyBox[$this->currentPlayer]) {
            if ($this->isGettingOutOfPenaltyBox) {
                echoln("Answer was correct!!!!");
                $this->purses[$this->currentPlayer]++;
                echoln($this->players[$this->currentPlayer] . " now has "
                    . $this->purses[$this->currentPlayer] . " Gold Coins.");

                $winner = $this->didPlayerWin();
                $this->nextPlayer();

                return $winner;
            }
        } else {

            echoln("Answer was corrent!!!!");
            $this->purses[$this->currentPlayer]++;
            echoln($this->players[$this->currentPlayer]
                . " now has "
                . $this->purses[$this->currentPlayer]
                . " Gold Coins.");

            $winner = $this->didPlayerWin();
            $this->nextPlayer();

            return $winner;
        }
    }


    public function wrongAnswer()
    {
        echoln("Question was incorrectly answered");
        echoln($this->players[$this->currentPlayer] . " was sent to the penalty box");
        $this->inPenaltyBox[$this->currentPlayer] = true;

        $this->nextPlayer();

        return true;
    }

    private function didPlayerWin()
    {
        return ! ($this->purses[$this->currentPlayer] == 6);
    }

    /**
     * @param $roll
     */
    private function advanceCurrentPlayer(int $roll): void
    {
        $this->places[$this->currentPlayer] = ($this->places[$this->currentPlayer] + $roll) % 12;

        echoln($this->players[$this->currentPlayer] . "'s new location is "
            . $this->places[$this->currentPlayer]);
        echoln("The category is " . $this->currentCategory());
        $this->askQuestion();
    }

    /**
     * @param $roll
     */
    private function maybeLetOutOfPenaltyBox($roll): void
    {
        if ($roll % 2 != 0) {
            $this->isGettingOutOfPenaltyBox = true;
            echoln($this->players[$this->currentPlayer] . " is getting out of the penalty box");
        } else {
            $this->isGettingOutOfPenaltyBox = false;
            echoln($this->players[$this->currentPlayer] . " is not getting out of the penalty box");
        }
    }

    private function nextPlayer(): void
    {
        $this->currentPlayer++;
        if ($this->currentPlayer == $this->countPlayers()) {
            $this->currentPlayer = 0;
        }
    }
}
