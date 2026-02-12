<?php
class BankAccount {
    public $Balance = 958.20;
    public $CustomerName = "Don Gosselin";
    private $AccountNumber;

    
    function __construct($accountNum = 0, $name = "Don Gosselin", $balance = 958.20) {
        $this->AccountNumber = $accountNum;
        $this->CustomerName = $name;
        $this->Balance = $balance;
    }

    
    public function withdrawal($Amount) {
        $this->Balance -= $Amount;
    }
}


if (class_exists("BankAccount")) {
    $Checking = new BankAccount(1001, "Don Gosselin", 958.20);
} else {
    exit("<p>The BankAccount class is not available!</p>");
}


printf("<p>Your checking account balance is $%.2f.</p>", $Checking->Balance);

$Cash = 200;
$Checking->withdrawal($Cash);
printf("<p>After withdrawing $%.2f, your checking account balance is $%.2f.</p>", $Cash, $Checking->Balance);
?>
