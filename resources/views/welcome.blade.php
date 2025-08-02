<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Search Autocomplete</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f5f7fa;
            padding: 50px;
        }

        .search-container {
            position: relative;
            max-width: 400px;
            margin: 0 auto;
        }

        .search-box {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            transition: border 0.3s;
        }

        .search-box:focus {
            border-color: #007bff;
            outline: none;
        }

        .autocomplete {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 10;
            background-color: #fff;
            border: 1px solid #ccc;
            border-top: none;
            border-radius: 0 0 8px 8px;
            max-height: 250px;
            overflow-y: auto;
        }

        .autocomplete-item {
            padding: 10px 16px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .autocomplete-item:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>

    <div class="search-container" >
        <form action="/search" method="GET" autocomplete="off">
            <input id="myInput" type="text" name="q" class="search-box" placeholder="Search...">
        </form>
        <div id="autocomplete" class="autocomplete" style="display: none;"></div>
    </div>

    <script>
        < script >
            const input = document.getElementById('myInput');
        const autocomplete = document.getElementById('autocomplete');
        let data = [];

        // Load data from file.txt
        fetch('{{ asset('data/collection_datas.txt') }}')
            .then(response => response.text())
            .then(text => {
                data = text.split('\n').map(line => line.trim()).filter(Boolean);
            });

        input.addEventListener('input', function() {
            const val = this.value.toLowerCase();
            autocomplete.innerHTML = '';
            if (val === '') {
                autocomplete.style.display = 'none';
                return;
            }

            const filtered = data.filter(item => item.toLowerCase().includes(val));

            if (filtered.length === 0) {
                autocomplete.style.display = 'none';
                return;
            }

            filtered.forEach(item => {
                const div = document.createElement('div');
                div.classList.add('autocomplete-item');
                div.textContent = item;
                div.onclick = () => {
                    input.value = item;
                    autocomplete.style.display = 'none';
                };
                autocomplete.appendChild(div);
            });

            autocomplete.style.display = 'block';
        });

        document.addEventListener('click', function(e) {
            if (!document.querySelector('.search-container').contains(e.target)) {
                autocomplete.style.display = 'none';
            }
        });
    </script>
    </script>

</body>

</html>
