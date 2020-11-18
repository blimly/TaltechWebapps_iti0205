// yatzee by markaa
var game;

class Yahtzee {
    constructor() {
        this.board = document.getElementById("boardbody");
        this.pickdice = document.getElementById("dieces");
        this.header = document.getElementById("header");
        this.tableelem = document.getElementById("table");
        this.header.innerHTML = "How many rounds?";

        this.rounds = 0;
        this.rounds_left = 0;
        this.play_score = 0;

        this.canchoose = false;
        this.rolls_this_round = 0;
        this.dice = [];
        this.dice_imgs = [];
        this.load_dice_imgs();

        this.table = {
            "Ones": [0, 0, false],
            "Twos": [0, 0, false],
            "Threes": [0, 0, false],
            "Fours": [0, 0, false],
            "Fives": [0, 0, false],
            "Sixes": [0, 0, false],
            "Three of a Kind": [0, 0, false],
            "Four of a Kind": [0, 0, false],
            "Small Straight": [0, 0, false],
            "Large Straight": [0, 0, false],
            "Full House": [0, 0, false],
            "Chance": [0, 0, false],
            "Yahtzee": [0, 0, false]
        };
    }

    load_dice_imgs() {
        for (let i = 1; i <= 6; i++) {
            let img = document.createElement("img");
            img.src = "img/dice" + i + ".png";
            this.dice_imgs.push(img);
        }
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
        this.board.appendChild(this.createRow("Type", "Potential", "Points"));
        for (var key in this.table) {
            if (n == this.rounds) break; n++;
            let row = this.createRow(key, 0, 0);
            row.setAttribute("onclick", "game.choose(this)");
            this.board.appendChild(row);
        }
        this.tableelem.removeAttribute("hidden");
    }

    ask_rounds(n) {
        this.rounds = n;
        this.rounds_left = n;
        document.getElementById("pickrounds").remove();
        this.header.innerHTML = "Roll";
        this.createTable()
        this.pickdice.removeAttribute("hidden");
    }

    roll_dice() {
        if (this.rolls_this_round < 3 && this.rounds_left > 0) {
            let roll = this.pickdice.getElementsByTagName("td")
            for (let i = 0; i < 5; i++) {
                let rand = Math.ceil(Math.random() * 6);
                this.dice[i] = rand;
                roll[i].innerHTML = null;
                roll[i].appendChild(this.dice_imgs[rand - 1].cloneNode(true));
            }
            this.rolls_this_round += 1;
            this.canchoose = true;
            this.updateTable();
        }
    }

    updateTable() {
        let board = this.board.getElementsByTagName("td");
        let n = 0;
        for (var key in this.table) {
            if (n == this.rounds) break; n++;
            if (this.table[key][2] === false) {
                this.table[key][0] = this.calculatePotential(key);
            }
            board[1 + n * 3].innerHTML = this.table[key][0]
            board[2 + n * 3].innerHTML = this.table[key][1];
        }
        this.updateHeader();
    }

    choose(row) {
        row.style["background-color"] = "lightgray";
        row = row.getElementsByTagName("td");
        let rep = this.table[row[0].innerHTML];
        if (this.canchoose == true && rep[2] == false) {
            this.canchoose = false;
            rep[2] = true;
            rep[1] = rep[0];
            this.play_score += rep[1];
            this.rolls_this_round = 0;
            this.rounds_left--;

            this.updateTable();
            this.updateHeader();
        }
    }

    updateHeader() {
        let action = "";
        if (this.canchoose) {
            if (this.rolls_this_round < 3) {
                action = "choose or roll";
            } else {
                action = "choose";
            }
        } else {
            if (this.rolls_this_round < 3 && this.rounds_left > 0) {
                action = "roll";
            } else {
                action = "Game over";
            }
        }
        this.header.innerHTML = "Rounds left: " + this.rounds_left +
            " . Score: " + this.play_score + ". " + action;
    }

    calculatePotential(key) {
        switch (key) {
            case "Ones":
                return numbers(this.dice, 1);
            case "Twos":
                return numbers(this.dice, 2);
            case "Threes":
                return numbers(this.dice, 3);
            case "Fours":
                return numbers(this.dice, 4);
            case "Fives":
                return numbers(this.dice, 5);
            case "Sixes":
                return numbers(this.dice, 6);
            case "Three of a Kind":
                return threeOfAKind(this.dice);
            case "Four of a Kind":
                return fourOfAKind(this.dice);
            case "Small Straight":
                return smallStraight(this.dice);
            case "Large Straight":
                return largeStraight(this.dice);
            case "Full House":
                return fullHouse(this.dice);
            case "Chance":
                return chance(this.dice);
            case "Yahtzee":
                return yahtzee(this.dice);
        }
    }
}

window.onload = function () {
    game = new Yahtzee();
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
    if (dice.includes(5) && dice.includes(6) || 
        dice.includes(1) && dice.includes(2) ||
        dice.includes(2) && dice.includes(5)) {
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