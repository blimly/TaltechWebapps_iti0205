
// yatzee by markaa
const gameState = {
    player: {
        name: "",
        points: "",
        choosing: false,
    }, 
    board: {

    }
}
const socket = io("http://localhost:3000/");
socket.on('init', handleInit);
socket.on('gamestate', handleGamestate);


function handleInit(msg) {
    console.log(msg);
}

function handleGamestate(gameState) {
    gameState = JSON.parse(gameState);
    
}

var game;
class Yahtzee {
    constructor() {

        this.board = document.getElementById("boardbody");
        this.board2 = document.getElementById("boardbody2");
        this.pickdice = document.getElementById("dieces");
        this.header = document.getElementById("header");
        this.tableelem = document.getElementById("table1");
        this.tableelem2 = document.getElementById("table2");
        //this.header.innerHTML = "How many rounds?";
        this.header.innerHTML = "What is your name?";
        this.p1_score = document.getElementById("p1points")
        this.p2_score = document.getElementById("p2points")

        this.rounds = 0;
        this.rounds_left = 0;
        this.player1_score = 0;
        this.player2_score = 0;

        this.canchoose = true;
        this.canchoose2 = false;
        this.palayer1round = true;

        this.rolls_this_round = 0;
        this.dice = [];
        this.dice_imgs = [];
        this.load_dice_imgs();
        
        this.gamestate = {
            "rolling": "Player 1"
        }

        // potential, p1 points, taken
        this.table1 = {
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

        this.table2 = {
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
        this.board.appendChild(this.createRow("       Type      ", "Potential", "Points"));
        this.board2.appendChild(this.createRow("       Type      ", "Potential", "Points"));
        for (var key in this.table1) {
            let row1 = this.createRow(key, 0, 0);
            let row2 = this.createRow(key, 0, 0);
            row1.setAttribute("onclick", "game.choose1(this, this.canchoose)");
            row2.setAttribute("onclick", "game.choose2(this, this.canchoose2)");
            this.board.appendChild(row1);
            this.board2.appendChild(row2);
        }
        this.tableelem.removeAttribute("hidden");
        this.tableelem2.removeAttribute("hidden");
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
            
            if (this.palayer1round) {
                this.updateTable(this.table1);
                this.canchoose = true;
                this.canchoose2 = false;
            } else {
                this.canchoose1 = false;
                this.canchoose2 = true;
                this.updateTable(this.table2);
            }
        } else {
            this.canchoose = false;
            this.canchoose2 = false;
        }
        this.updateHeader();
    }

    updateTable(table) {
        let board = 0;
        if (this.palayer1round) {
            board = this.board.getElementsByTagName("td");
        } else {
            board = this.board2.getElementsByTagName("td");
        }
        let n = 0;
        for (var key in table) {
			n++;
            if (table[key][2] === false) {
                table[key][0] = this.calculatePotential(key);
            }
            board[1 + n * 3].innerHTML = table[key][0]
            board[2 + n * 3].innerHTML = table[key][1];
        }
        this.updateHeader();
        /*
        const options = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(table)

        }
        fetch('/api', options)
            .then(res => {
                console.log(res);
        });
        */
    }

    choose1(row) {
        let row_td = row.getElementsByTagName("td");
        let rep = this.table1[row_td[0].innerHTML];
        if (this.canchoose == true && rep[2] == false) {
            row.style["background-color"] = "lightgray";
            this.canchoose = false;
            rep[2] = true;
            rep[1] = rep[0];
            this.player1_score += rep[1];
            this.rolls_this_round = 0;

            this.updateTable(this.table1);
            this.updateHeader();
            this.palayer1round = false;
        }
    }

    choose2(row) {
        let row_td = row.getElementsByTagName("td");
        let rep = this.table2[row_td[0].innerHTML];
        if (this.canchoose2 == true && rep[2] == false) {
            row.style["background-color"] = "lightgray";
            this.canchoose2 = false;
            rep[2] = true;
            rep[1] = rep[0];
            this.player2_score += rep[1];
            this.rolls_this_round = 0;
            this.rounds_left--;

            this.updateTable(this.table2);
            this.updateHeader();
            this.palayer1round = true;
        }
    }
    updateHeader() {
        let action = "";
        if (this.rolls_this_round < 3 && this.rounds_left > 0) {
            action = "roll";
        } else {
            let winner = (this.player1_score > this.player2_score) ? "Player 1" : "Player 2"
            action = "Game over " + winner + " won";
        }
        this.header.innerHTML = "Rounds left: " + this.rounds_left + ". " + action;
        this.p1_score.innerHTML = this.player1_score;
        this.p2_score.innerHTML = this.player2_score;
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

    postNewGameScore(score1, score2) {

        let scoreboard = {
            "score1": score1,
            "score2": score2,
            "time": Date.now
        }
        const options = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(scoreboard)

        }
        fetch('/new_gamescore', options)
            .then(res => {
                console.log(res);
        });

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
