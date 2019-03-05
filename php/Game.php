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

        if ($this->currentPlayer()->isInPenaltyBox) {
            $this->maybeLetOutOfPenaltyBox($roll);
        }

        if ( ! $this->currentPlayer()->isInPenaltyBox) {
            $this->advanceCurrentPlayer($roll);
        }

    }


    private function askQuestion(): void
    {
        echoln(array_shift($this->questions[$this->currentCategory()]));
    }

    private function currentCategory(): string
    {
        $place = $this->currentPlayer()->getPlace();

        foreach(self::$categories as $category=>$fields) {
            if (in_array($place, $fields, true)) {
                return $category;
            }
        }
    }

    public function rightAnswer(): void
    {
        if (!$this->currentPlayer()->isInPenaltyBox) {
            echoln('Answer was correct!!!!');
            $this->currentPlayer()->giveCoin();
            echoln($this->currentPlayerName() . ' now has '
                . $this->currentPlayer()->getPurse() . ' Gold Coins.');
        }

        $this->nextPlayer();

    }


    public function wrongAnswer(): void
    {
        echoln('Question was incorrectly answered');
        echoln($this->currentPlayerName() . ' was sent to the penalty box');
        $this->currentPlayer()->isInPenaltyBox = true;

        $this->nextPlayer();
    }

    private function isWinner(int $playerNumber): bool {
        return ($this->players[$playerNumber]->getPurse() === 6);
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
        $newPlace = ($this->currentPlayer()->getPlace() + $roll) % 12;
        $this->players[$this->currentPlayer]->moveTo($newPlace);

        echoln($this->currentPlayerName() . '\'s new location is '
            . $this->currentPlayer()->getPlace());
        echoln('The category is ' . $this->currentCategory());
        $this->askQuestion();
    }

    /**
     * @param $roll
     */
    private function maybeLetOutOfPenaltyBox($roll): void
    {
        if ($roll % 2 != 0) {
            $this->currentPlayer()->isInPenaltyBox = false;
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
        return $this->currentPlayer()->name;
    }

    /**
     * @return Player
     */
    private function currentPlayer(): Player
    {
        return $this->players[$this->currentPlayer];
    }
}

dsdsa


class Player
{
    public $name;

    public $isInPenaltyBox = false;

    private $place = 0;

    private $purse = 0;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function giveCoin(): void
    {
        $this->purse++;
    }

    public function moveTo(int $place): void
    {
        $this->place = $place;
    }

    public function getPlace(): int
    {
        return $this->place;
    }

    /**
     * @return int
     */
    public function getPurse(): int
    {
        return $this->purse;
    }
}