<!DOCTYPE html>
<html>

<head>
    <title>Weather App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="container text-center p-4 shadow-lg rounded bg-light" style="max-width: 500px;">
        <h1 class="mb-4">Weather App</h1>
        <form method="POST" action="/weather" id="weatherForm">
            @csrf
            <div class="mb-3">
                <input type="text" class="form-control" name="city" placeholder="Enter city name" value="{{ old('city') }}">
            </div>
            <div class="mb-3">
                <label for="units" class="form-label">Select Temperature Unit:</label>
                <select class="form-select" name="units" id="units">
                    <option value="metric">Celsius</option>
                    <option value="imperial">Fahrenheit</option>
                    <option value="">Kelvin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-lg bg-gradient">Get Weather</button>
        </form>

        @if ($errors->any())
        <div class="alert alert-danger mt-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if (isset($weather))
        @if (isset($weather['error']))
        <div class="alert alert-danger mt-3">
            <p class="mb-0">{{ $weather['error'] }}</p>
        </div>
        @else
        <div class="result alert alert-primary mt-3">
            <p><strong>Temperature:</strong> {{ $weather['temp'] }}</p>
            <p><strong>Humidity:</strong> {{ $weather['humidity'] }}</p>
            <p><strong>Description:</strong> {{ $weather['description'] }}</p>
        </div>
        @endif
        @endif
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("weatherForm");

            if (window.location.hash) {
                const hash = window.location.hash.substring(1);
                const cityParam = new URLSearchParams(hash).get('city');
                if (cityParam) {
                    const cityInput = form.querySelector('input[name="city"]');
                    cityInput.value = cityParam;
                    form.submit();
                }
            }
        });
    </script>
</body>

</html>