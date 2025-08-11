<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autocomplete and Typo Corrector</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
    
    @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            background-image: url("{{ asset('images/bg.jpg') }}"); 
            background-size: cover; 
            background-position: center; 
        }

        .subject {
            width: 600px;
            height: 60px;
            margin-left: 382px;
            color: #FFFFFF;
            margin-top: 60px;
            overflow: hidden;
            position: relative;
        }

        .marquee-content {
            position: absolute;
            width: 100%;
            animation: scroll-left-right 10s linear infinite;
        }

        @keyframes scroll-left-right {
            0% {
                left: -100%;
            }
            100% {
                left: 100%;
            }
        }

        .login-form {
            margin-left:444px;
            margin-top: 40px;
            background: rgba(64, 64, 64, 0.15);
            border: 3px solid rgba(255, 255, 255, 0.3);
            padding: 30px;
            border-radius: 16px;
            backdrop-filter: blur(25px);
            text-align: center;
            color: white;
            max-width: 600px;
            box-shadow: 0px 0px 20px 10px rgba(0, 0, 0, 0.15);
        }

        .login-title {
            font-size: 40px;
            margin-bottom: 40px;
        }

        .input-box {
            margin: 20px 0;
            position: relative;
        }
        .input-box input {
            width: 100%;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            padding: 12px 12px 12px 45px;
            border-radius: 99px;
            outline: 3px solid transparent;
            transition: 0.3s;
            font-size: 17px;
            color: white;
            font-weight: 600;
        }
        .input-box input::placeholder {
            color: rgba(255, 255, 255, 0.8);
            font-size: 17px;
            font-weight: 500;
        }
        .input-box input:focus {
            outline: 3px solid rgba(255, 255, 255, 0.3);
        }
        .input-box input::-ms-reveal {
            filter: invert(100%);
        }

        .input-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            color: rgba(255, 255, 255, 0.8);
        }

        .remember-forgot-box {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            font-size: 15px;
        }

        .remember-forgot-box label {
            display: flex;
            gap: 8px;
            cursor: pointer;
        }
        .remember-forgot-box input {
            accent-color: white;
            cursor: pointer;
        }

        .remember-forgot-box a {
            color: white;
            text-decoration: none;
        }
        .remember-forgot-box a:hover {
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;
            padding: 10px 0;
            background: #2F9CF4;
            border: none;
            border-radius: 99px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        .login-btn:hover {
            background: #0B87EC;
        }

        .register {
            margin-top: 15px;
            font-size: 15px;
        }
        .register a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }
        .register a:hover {
            text-decoration: underline;
        }

        .autocomplete {
            position: absolute;
            z-index: 1000;
            background-color: #ffffff;
            border: 1px solid #ccc;
            border-top: none;
            max-height: 60px;
            overflow-x: auto;
            overflow-y: hidden;
            width: 100%;
            white-space: nowrap;
            display: flex;
            gap: 10px;
            padding: 5px 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            font-size: 14px;
            border-radius: 0 0 10px 10px;
            scrollbar-width: none;
        }

        .autocomplete-item {
            display: inline-block;
            padding: 0px 8px;
            background-color: transparent;
            border-radius: 20px;
            border: -0.1px solid #e9ecef;
            cursor: pointer;
            white-space: nowrap;
            font-weight: 500;
            color: #e9ecef;
            transition: background-color 0.2s;
        }

        .autocomplete::-webkit-scrollbar {
            display: none;
        }

        .autocomplete-item:hover {
            background-color: #e9ecef;
            color: #333;
        }

        .is-valid {
            border: 2px solid #28a745 !important;         
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .is-invalid {
            border: 2px solid #dc3545 !important;    
            border-radius: 6px;
            transition: all 0.3s ease;
        }

</style>

</head>
<body>

    <div class="subject">
        <div class="marquee-content">
            <h1>Artificial Intelligence Concepts</h1>
        </div>
    </div>

    <form id="searchForm" novalidate class="login-form">
        <h1 class="login-title">Autocomplete Typo Corrector</h1>

        <div class="input-box" id="inputBox">
            <i class='bx bxs-keyboard'></i>
            <input type="text" id="myInput" name="q" class="form-control" placeholder="Search..." required autocomplete="off">
        </div>

        <div id="autocomplete" class="d-none"></div>

        <br>
        <p class="register">
            @GROUP 2 - Autocomplete and Typo Corrector
        </p>
    </form>

</body>
<script>
class TextHelper {
    static levenshtein(a, b) {
        const dp = Array.from({ length: b.length + 1 }, () => []);
        for (let i = 0; i <= b.length; i++) dp[i][0] = i;
        for (let j = 0; j <= a.length; j++) dp[0][j] = j;

        for (let i = 1; i <= b.length; i++) {
            for (let j = 1; j <= a.length; j++) {
                if (b[i - 1] === a[j - 1]) {
                    dp[i][j] = dp[i - 1][j - 1];
                } else {
                    dp[i][j] = Math.min(
                        dp[i - 1][j] + 1,
                        dp[i][j - 1] + 1,
                        dp[i - 1][j - 1] + 1
                    );
                }
            }
        }
        return dp[b.length][a.length];
    }

    static ngramSimilarity(a, b, n = 2) {
        if (!a.length || !b.length) return 0;
        const ngrams = str => {
            const grams = [];
            for (let i = 0; i < str.length - n + 1; i++) {
                grams.push(str.slice(i, i + n));
            }
            return grams;
        };
        const aGrams = ngrams(a);
        const bGrams = ngrams(b);
        const intersection = aGrams.filter(g => bGrams.includes(g)).length;
        return (2 * intersection) / (aGrams.length + bGrams.length);
    }
}

class AutocompleteCorrector {
    constructor(dataset) {
        this.dataset = dataset.map(w => w.toLowerCase());
        this.input = document.getElementById("myInput");
        this.autocompleteBox = document.getElementById("autocomplete");
        this.bindEvents();
    }

    bindEvents() {
        this.input.addEventListener("input", () => this.showSuggestions());
        this.input.addEventListener("keydown", e => {
            if (e.key === "Tab" || e.key === "Enter") {
                e.preventDefault();
                this.applyBestSuggestion();
            }
        });
    }

    getBestMatch(word) {
        let best = { term: word, score: Infinity, similarity: 0 };
        for (let term of this.dataset) {
            const distance = TextHelper.levenshtein(word, term);
            const similarity = TextHelper.ngramSimilarity(word, term);
            if (
                similarity > best.similarity ||
                (similarity === best.similarity && distance < best.score)
            ) {
                best = { term, score: distance, similarity };
            }
        }
        return best.term;
    }

    showSuggestions() {
        const value = this.input.value.trim().toLowerCase();
        const words = value.split(/\s+/);
        const lastWord = words[words.length - 1];
        this.autocompleteBox.innerHTML = "";

        if (!lastWord) {
            this.autocompleteBox.style.display = "none";
            return;
        }

        const suggestions = this.dataset
            .map(term => ({
                term,
                dist: TextHelper.levenshtein(lastWord, term),
                sim: TextHelper.ngramSimilarity(lastWord, term)
            }))
            .filter(s => s.term.includes(lastWord) || s.dist <= 2 || s.sim >= 0.5)
            .sort((a, b) => b.sim - a.sim || a.dist - b.dist)
            .slice(0, 5);

        if (suggestions.length) {
            suggestions.forEach(s => {
                const div = document.createElement("div");
                div.classList.add("autocomplete-item");
                div.textContent = s.term;
                div.onclick = () => {
                    words[words.length - 1] = s.term;
                    this.input.value = words.join(" ") + " ";
                    this.autocompleteBox.style.display = "none";
                    this.validateWord(s.term);
                };
                this.autocompleteBox.appendChild(div);
            });
            this.autocompleteBox.style.display = "block";
        } else {
            this.autocompleteBox.style.display = "none";
        }

        this.validateWord(lastWord);
    }

    applyBestSuggestion() {
        const value = this.input.value.trim().toLowerCase();
        const words = value.split(/\s+/);
        const lastWord = words[words.length - 1];
        const best = this.getBestMatch(lastWord);
        words[words.length - 1] = best;
        this.input.value = words.join(" ") + " ";
        this.autocompleteBox.style.display = "none";
        this.validateWord(best);
    }

    validateWord(word) {
        if (this.dataset.includes(word.toLowerCase())) {
            this.input.classList.add("is-valid");
            this.input.classList.remove("is-invalid");
        } else {
            this.input.classList.remove("is-valid");
            this.input.classList.add("is-invalid");
        }
    }
}

const collection_corpus = "{{ asset('data/collection_datas.txt') }}";

fetch(collection_corpus)
    .then(response => response.text())
    .then(text => {
        // Assuming words are separated by spaces or newlines
        const dataset = text.split(/\s+/).filter(Boolean);
        new AutocompleteCorrector(dataset);
    })
    .catch(err => console.error("Error loading dataset:", err));
</script>

</html>