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

    private $popQuestions = [];
    private $scienceQuestions = [];
    private $sportsQuestions = [];
    private $rockQuestions = [];

    private $currentPlayer = 0;
    private $isGettingOutOfPenaltyBox;

    public function __construct()
    {
        $this->initializeQuestions();
    }

    private function initializeQuestions(): void
    {
        for ($i = 0; $i < 50; $i++) {
            $this->popQuestions[] = "Pop Question " . $i;
            $this->scienceQuestions[] = "Science Question " . $i;
            $this->sportsQuestions[] = "Sports Question " . $i;
            $this->rockQuestions[] = "Rock Question " . $i;
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

        if (!$this->inPenaltyBox[$this->currentPlayer] || $this->isGettingOutOfPenaltyBox)
        {
            $this->advanceCurrentPlayer($roll);
        }

    }


    private function askQuestion()
    {
        if ($this->currentCategory() == "Pop") {
            echoln(array_shift($this->popQuestions));
        }
        if ($this->currentCategory() == "Science") {
            echoln(array_shift($this->scienceQuestions));
        }
        if ($this->currentCategory() == "Sports") {
            echoln(array_shift($this->sportsQuestions));
        }
        if ($this->currentCategory() == "Rock") {
            echoln(array_shift($this->rockQuestions));
        }
    }

    private function currentCategory()
    {
        $categories = ['Pop','Science', 'Sports', 'Rock'];

        // FIXME: Move places into place
        
        $place = $this->places[$this->currentPlayer];

        $popPlaces = [0, 4, 8];
        $sciencePlace = [1, 5, 9];
        $sportsPlaces = [2, 6, 10];
        $rockPlaces = [3, 7, 11];

        if (in_array($place, $popPlaces, true)) {
            return $categories[0];
        }
        if (in_array($place, $sciencePlace, true)) {
            return $categories[1];
        }
        if (in_array($place, $sportsPlaces, true)) {
            return $categories[2];
        }
        if (in_array($place, $rockPlaces, true)) {
            return $categories[3];
        }


    }

    public function wasCorrectlyAnswered()
    {
        if ($this->inPenaltyBox[$this->currentPlayer]) {
            if ($this->isGettingOutOfPenaltyBox) {
                echoln("Answer was correct!!!!");
                $this->purses[$this->currentPlayer]++;
                echoln($this->players[$this->currentPlayer]
                    . " now has "
                    . $this->purses[$this->currentPlayer]
                    . " Gold Coins.");

                $winner = $this->didPlayerWin();
                $this->currentPlayer++;
                if ($this->currentPlayer == $this->countPlayers()) {
                    $this->currentPlayer = 0;
                }

                return $winner;
            } else {
                $this->currentPlayer++;
                if ($this->currentPlayer == $this->countPlayers()) {
                    $this->currentPlayer = 0;
                }

                return true;
            }


        } else {

            echoln("Answer was corrent!!!!");
            $this->purses[$this->currentPlayer]++;
            echoln($this->players[$this->currentPlayer]
                . " now has "
                . $this->purses[$this->currentPlayer]
                . " Gold Coins.");

            $winner = $this->didPlayerWin();
            $this->currentPlayer++;
            if ($this->currentPlayer == $this->countPlayers()) {
                $this->currentPlayer = 0;
            }

            return $winner;
        }
    }


    public function wrongAnswer()
    {
        echoln("Question was incorrectly answered");
        echoln($this->players[$this->currentPlayer] . " was sent to the penalty box");
        $this->inPenaltyBox[$this->currentPlayer] = true;

        $this->currentPlayer++;
        if ($this->currentPlayer == $this->countPlayers()) {
            $this->currentPlayer = 0;
        }

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
}
