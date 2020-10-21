// yatze by markaa
var game;

class Yahtzee {
    constructor() {
        this.header = document.getElementById("header");
        this.header.innerHTML = "Loading Game";

        this.table = {
            "Ones": [0, 0],
            "Twos": [0, 0],
            "Threes": [0, 0],
            "Fours": [0, 0],
            "Fives": [0, 0],
            "Sixes": [0, 0],
            "Three of a Kind": [0, 0],
            "Four of a Kind": [0, 0],
            "Small Straight": [0, 0],
            "Large Straight": [0, 0],
            "Full House": [0, 0],
            "Chance": [0, 0],
            "Yahtzee": [0, 0]
        };

        this.header.innerHTML = "How many rounds?";
        this.rounds = 0;
        this.board = document.getElementById("boardbody")
        this.pickdice = document.getElementById("dieces")

        /*
        this.board.style.visibility = "hidden";
        this.pickdice.style.visibility = "hidden"
        */
        this.dice = []
    }

    createRow(column0, column1, column2) {
        var tr = document.createElement("tr");
        var choice = document.createElement("td");
        var potential = document.createElement("td");
        var points = document.createElement("td");
        choice.innerHTML = column0;
        potential.innerHTML = column1;
        points.innerHTML = column2;
        tr.appendChild(choice);
        tr.appendChild(potential);
        tr.appendChild(points);
        return tr;
    }

    createTable() {
        let n = 0;
        this.createRow("Type", "Potential", "Points");
        for (var key in this.table) {
            if (n == this.rounds) break; n++;
            row = this.createRow(key, 0, 0);
            this.board.appendChild(row);
        }
        this.board.removeAttribute("hidden");
    }

    ask_rounds(n) {
        this.rounds = n;
        document.getElementById("pickrounds").remove();
        this.header.innerHTML = "Let's play!";
        this.createTable()
        this.pickdice.removeAttribute("hidden");
    }

    roll_dice() {
        let roll = document.getElementById("dieces").getElementsByTagName("td")
        this.dice = []
        for (let i = 0; i < 5; i++) {
            let rand = Math.ceil(Math.random() * 6);
            this.dice.push(rand);
            roll[i].innerHTML = rand;
        }
        this.updateTable();
    }

    updateTable() {
        let board = document.getElementById("board").getElementsByTagName("td");

        let n = 0;
        for (var key in this.table) {
            if (n == this.rounds) break; 
            this.table[key][0] = this.calculatePotential(key);
            board[1 + n*3].innerHTML = this.table[key][0]
            board[2 + n*3].innerHTML = this.table[key][1];
            n++;
        }
    }

    calculatePotential(key) {
        switch (key) {
            case "Ones":
                return this.dice.filter(x => x == 1).length;
            case "Twos":
                return this.dice.filter(x => x == 2).length;
            case "Threes":
                return this.dice.filter(x => x == 3).length;
            case "Fours":
                return this.dice.filter(x => x == 4).length;
            case "Fives":
                return this.dice.filter(x => x == 5).length;
            case "Sixes":
                return this.dice.filter(x => x == 6).length;
            case "Three of a Kind":
                break;
            case "Four of a Kind":
                break;
            case "Small Straight":
                break;
            case "Large Straight":
                break;
            case "Full House":
                break;
            case "Chance":
                break;
            case "Yahtzee":
                break;
        }
    }

}

window.onload = function () {
    game = new Yahtzee();
}



function roll() {
    return Math.ceil(Math.random() * 6);
}

function numbers(dice, n) {
    let sum = 0;
    for (let i = 0; i < dice.length; i++) {
        if (dice[i] == n) {
            sum += n;
        }
    }
    return sum;
}

function threeOfAKind(dice) {
    sum = chance(dice);
    for (let i = 0; i < dice.length; i++) {
        for (let j = i + 1; j < dice.length; j++) {
            for (let k = j + 1; k < dice.length; k++) {
                if (dice[i] == dice[j] && dice[j] == dice[k]) {
                    return sum;
                }
            }
        }
    }
    return 0;
}

function fourOfAKind(dice) {
    sum = chance(dice);
    for (let i = 0; i < dice.length; i++) {
        for (let j = i + 1; j < dice.length; j++) {
            for (let k = j + 1; k < dice.length; k++) {
                for (let l = k + 1; l < dice.length; l++) {
                    if (dice[i] == dice[j] && dice[j] == dice[k] && dice[k] == dice[l]) {
                        return sum;
                    }
                }
            }
        }
    }
    return 0;
}

function smallStraight(dice) {
    if (!dice.includes(3) || !dice.includes(4)) {
        return 0;
    }
    if (dice.includes(5) && dice.includes(6)) {
        return 30;
    }
    if (dice.includes(1) && dice.includes(2)) {
        return 30;
    }
    if (dice.includes(2) && dice.includes(5)) {
        return 30;
    }
    return 0;
}

function largeStraight(dice) {
    for (let i = 2; i < 6; i++) {
        if (!dice.includes(i)) {
            return 0;
        }
    }
    if (dice.includes(1) || dice.includes(6)) {
        return 40;
    }
    return 0;
}

function fullHouse(dice) {
    triplet = 0;
    for (let i = 0; i < dice.length; i++) {
        for (let j = i + 1; j < dice.length; j++) {
            for (let k = j + 1; k < dice.length; k++) {
                if (dice[i] == dice[j] && dice[j] == dice[k] && dice[i] > triplet) {
                    triplet = dice[i];
                }
            }
        }
    }
    if (triplet == 0) {
        return 0;
    }

    diceCopy = dice.slice();
    diceCopy.splice(diceCopy.indexOf(triplet), 1);
    diceCopy.splice(diceCopy.indexOf(triplet), 1);
    diceCopy.splice(diceCopy.indexOf(triplet), 1);

    if (diceCopy[0] == diceCopy[1] && diceCopy[0] != triplet) {
        return 25;
    }
    return 0;
}

function chance(dice) {
    let sum = 0;
    for (let i = 0; i < dice.length; i++) {
        sum += dice[i];
    }
    return sum;
}

function yahtzee(dice) {
    for (let i = 0; i < dice.length - 1; i++) {
        if (dice[i] != dice[i + 1]) {
            return 0;
        }
    }
    return 50;
}

function rollAll(dice) {
    for (let i = 0; i < dice.length; i++) {
        dice[i] = roll();
    }
    refresh();
    return dice;
}

function refresh() {
    for (let key in scores) {
        document.getElementById(key).innerHTML = scores[key];
    }

    document.getElementById("diceValues").innerHTML = "dice: " + dice.toString();

    document.getElementById("pOnes").innerHTML = numbers(dice, 1).toString();
    document.getElementById("pTwos").innerHTML = numbers(dice, 2).toString();
    document.getElementById("pThrees").innerHTML = numbers(dice, 3).toString();
    document.getElementById("pFours").innerHTML = numbers(dice, 4).toString();
    document.getElementById("pFives").innerHTML = numbers(dice, 5).toString();
    document.getElementById("pSixes").innerHTML = numbers(dice, 6).toString();
    document.getElementById("pThree Of A Kind").innerHTML = threeOfAKind(dice).toString();
    document.getElementById("pFour Of A Kind").innerHTML = fourOfAKind(dice).toString();
    document.getElementById("pSmall Straight").innerHTML = smallStraight(dice).toString();
    document.getElementById("pLarge Straight").innerHTML = largeStraight(dice).toString();
    document.getElementById("pFull House").innerHTML = fullHouse(dice).toString();
    document.getElementById("pChance").innerHTML = chance(dice).toString();
    document.getElementById("pYahtzee").innerHTML = yahtzee(dice).toString();
}
