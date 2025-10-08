<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel Fatal Error Lab (PHP 8.2)</title>
    <style>
        body { font-family: system-ui, sans-serif; padding: 2rem; background: #f5f5f5; color: #222; }
        h1 { color: #111; }
        .btn-grid { display: grid; gap: 1rem; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-top: 2rem; }
        a.button { display: block; background: #007bff; color: #fff; padding: 0.8rem; border-radius: 8px; text-align: center; text-decoration: none; font-weight: bold; transition: 0.2s; }
        a.button:hover { background: #0056b3; }
        #logContainer { display: none; margin-top: 2rem; background: #1e1e1e; color: #dcdcdc; padding: 1rem; border-radius: 10px; max-height: 400px; overflow-y: auto; white-space: pre-wrap; font-family: monospace; }
        .toggle-container { margin-top: 2rem; }
        button.toggle-btn { background: #28a745; color: #fff; padding: 0.6rem 1rem; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; }
        button.toggle-btn:hover { background: #218838; }
    </style>
</head>
<body>
    <h1>🔥 Laravel Fatal Error Lab (PHP 8.2)</h1>
    <p>Click a button to trigger a specific fatal or catchable error type.</p>

    <div class="btn-grid">
        <a href="/trigger/exception" class="button">Throw Exception</a>
        <a href="/trigger/type" class="button">Trigger TypeError</a>
        <a href="/trigger/division" class="button">DivisionByZeroError</a>
        <a href="/trigger/assert" class="button">AssertionError</a>
        <a href="/trigger/undefined-function" class="button">Undefined Function</a>
        <a href="/trigger/undefined-method" class="button">Undefined Method</a>
        <a href="/trigger/parse" class="button">Parse Error (eval)</a>
        <a href="/trigger/memory" class="button" style="background:#dc3545;">⚠️ Memory Exhaustion</a>
    </div>

    <div class="toggle-container">
        <button class="toggle-btn" id="toggleLogs">🪵 Show Logs</button>
    </div>

    <div id="logContainer">
        <h3>📜 Latest Laravel Log Output</h3>
        <div id="logs">Loading...</div>
    </div>

    <script>
        const toggleBtn = document.getElementById('toggleLogs');
        const logContainer = document.getElementById('logContainer');
        const logsDiv = document.getElementById('logs');
        let interval = null;

        toggleBtn.addEventListener('click', () => {
            if (logContainer.style.display === 'none') {
                logContainer.style.display = 'block';
                toggleBtn.textContent = '🔒 Hide Logs';
                fetchLogs();
                interval = setInterval(fetchLogs, 3000); // refresh every 3s
            } else {
                logContainer.style.display = 'none';
                toggleBtn.textContent = '🪵 Show Logs';
                clearInterval(interval);
            }
        });

        async function fetchLogs() {
            const res = await fetch('/logs');
            const data = await res.json();
            logsDiv.textContent = data.logs || 'No logs yet.';
            logContainer.scrollTop = logContainer.scrollHeight;
        }
    </script>
</body>
</html>
