<?php
function echoln($string)
{
    echo $string . "\n";
}

class Game
{
    /**
     * @var Player[]
     */
    private $players = [];

    private $places = [0];
    private $purses = [0];
    private $inPenaltyBox = [0];

    private $currentPlayer = 0;
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
        $this->players[] = new Player($playerName);
        $this->places[$this->countPlayers()] = 0;
        $this->purses[$this->countPlayers()] = 0;
        $this->inPenaltyBox[$this->countPlayers()] = false;

        echoln($playerName . ' was added');
        echoln('They are player number ' . $this->countPlayers());
    }

    private function countPlayers(): int
    {
        return count($this->players);
    }

    public function roll($roll)
    {
        echoln($this->currentPlayerName() . ' is the current player');
        echoln('They have rolled a ' . $roll);

        if ($this->inPenaltyBox[$this->currentPlayer]) {
            $this->maybeLetOutOfPenaltyBox($roll);
        }

        if ( ! $this->inPenaltyBox[$this->currentPlayer]) {
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

    public function rightAnswer(): void
    {
        if (!$this->inPenaltyBox[$this->currentPlayer]) {
            echoln('Answer was correct!!!!');
            $this->purses[$this->currentPlayer]++;
            echoln($this->currentPlayerName() . ' now has '
                . $this->purses[$this->currentPlayer] . ' Gold Coins.');
        }

        $this->nextPlayer();

    }


    public function wrongAnswer(): void
    {
        echoln('Question was incorrectly answered');
        echoln($this->currentPlayerName() . ' was sent to the penalty box');
        $this->inPenaltyBox[$this->currentPlayer] = true;

        $this->nextPlayer();
    }

    private function isWinner(int $playerNumber): bool {
        return ($this->purses[$playerNumber] === 6);
    }
    public function isFinished(): bool
    {
        foreach($this->players as $i=>$player) {
            if ($this->isWinner($i)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $roll
     */
    private function advanceCurrentPlayer(int $roll): void
    {
        $this->places[$this->currentPlayer] = ($this->places[$this->currentPlayer] + $roll) % 12;

        echoln($this->currentPlayerName() . '\'s new location is '
            . $this->places[$this->currentPlayer]);
        echoln('The category is ' . $this->currentCategory());
        $this->askQuestion();
    }

    /**
     * @param $roll
     */
    private function maybeLetOutOfPenaltyBox($roll): void
    {
        if ($roll % 2 != 0) {
            $this->inPenaltyBox[$this->currentPlayer] = false;
            echoln($this->currentPlayerName() . ' is getting out of the penalty box');
        } else {
            echoln($this->currentPlayerName() . ' is not getting out of the penalty box');
        }
    }

    private function nextPlayer(): void
    {
        $this->currentPlayer++;
        if ($this->currentPlayer == $this->countPlayers()) {
            $this->currentPlayer = 0;
        }
    }

    /**
     * @return mixed
     */
    private function currentPlayerName(): string
    {
        return $this->players[$this->currentPlayer]->name;
    }
}

class Player
{
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }


}