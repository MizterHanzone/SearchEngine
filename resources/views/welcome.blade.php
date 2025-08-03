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
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: url("{{ asset('images/bg.jpg') }}"); 
            background-size: cover; 
            background-position: center; 
        }

        .login-form {
            background: rgba(64, 64, 64, 0.15);
            border: 3px solid rgba(255, 255, 255, 0.3);
            padding: 30px;
            border-radius: 16px;
            backdrop-filter: blur(25px);
            text-align: center;
            color: white;
            max-width: 500px;
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

</style>

</head>
<body>
    
    <form id="searchForm" novalidate class="login-form">
        <h1 class="login-title">Autocomplete Typo Corrector</h1>

        <div class="input-box">
            <i class='bx bxs-user'></i>
            <input type="text" id="myInput" name="q" class="form-control" placeholder="Search..." required autocomplete="off">
        </div>

        <div id="autocomplete" class="autocomplete-items d-none"></div>

        <!--     <button class="login-btn">Correct Word</button> -->

        <br>
        <p class="register">
            @GROUP1 - Autocomplete and Typo Corrector
        </p>
    </form>

</body>
<script>
    const dataFileUrl = "{{ asset('data/collection_datas.txt') }}";

    function levenshtein(a, b) {
        const matrix = Array.from({ length: b.length + 1 }, (_, i) => [i]);
        for (let j = 1; j <= a.length; j++) matrix[0][j] = j;
        for (let i = 1; i <= b.length; i++) {
            for (let j = 1; j <= a.length; j++) {
                if (b[i - 1] === a[j - 1]) {
                    matrix[i][j] = matrix[i - 1][j - 1];
                } else {
                    matrix[i][j] = Math.min(
                        matrix[i - 1][j] + 1,
                        matrix[i][j - 1] + 1,
                        matrix[i - 1][j - 1] + 1
                    );
                }
            }
        }
        return matrix[b.length][a.length];
    }

    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('myInput');
        const form = document.getElementById('searchForm');
        const autocomplete = document.getElementById('autocomplete');
        let data = [];

        fetch(dataFileUrl)
            .then(response => response.text())
            .then(text => {
                data = text.split('\n').map(line => line.trim().toLowerCase()).filter(Boolean);
            });

        input.addEventListener('input', function () {
            const val = this.value.trim().toLowerCase();
            autocomplete.innerHTML = '';

            if (val === '') {
                autocomplete.classList.add('d-none');
                input.classList.remove('is-valid', 'is-invalid');
                return;
            }

            const suggestions = data.filter(item => {
                const distance = levenshtein(val, item);
                return item.includes(val) || distance <= 2;
            });

            if (suggestions.length > 0) {
                suggestions.forEach(item => {
                    const div = document.createElement('div');
                    div.classList.add('autocomplete-item');
                    div.textContent = item;
                    div.onclick = () => {
                        input.value = item;
                        input.classList.remove('is-invalid');
                        input.classList.add('is-valid');
                        autocomplete.classList.add('d-none');
                    };
                    autocomplete.appendChild(div);
                });
                autocomplete.classList.remove('d-none');
            } else {
                autocomplete.classList.add('d-none');
            }

            if (data.includes(val)) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            } else {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
            }
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const val = input.value.trim().toLowerCase();
            if (data.includes(val)) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                // alert("Form submitted: " + input.value);
            } else {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
            }
        });

        document.addEventListener('click', function (e) {
            if (!autocomplete.contains(e.target) && e.target !== input) {
                autocomplete.classList.add('d-none');
            }
        });
    });
</script>
</html>