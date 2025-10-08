<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('errors-lab');
});

Route::get('/trigger/exception', function () {
    try {
        throw new Exception("This is a normal Exception!");
    } catch (Throwable $e) {
        Log::error("Caught Exception: " . $e->getMessage());
        return "Caught Exception: " . $e->getMessage();
    } finally {
        Log::info("Finally block executed after Exception.");
    }
});

Route::get('/trigger/type', function () {
    try {
        // TypeError
        $num = intdiv("hello", 2);
    } catch (Throwable $e) {
        Log::error("Caught TypeError: " . $e->getMessage());
        return "Caught TypeError: " . $e->getMessage();
    } finally {
        Log::info("Finally block executed after TypeError.");
    }
});

Route::get('/trigger/division', function () {
    try {
        $result = intdiv(5, 0);
    } catch (Throwable $e) {
        Log::error("Caught DivisionByZeroError: " . $e->getMessage());
        return "Caught DivisionByZeroError: " . $e->getMessage();
    } finally {
        Log::info("Finally block executed after DivisionByZeroError.");
    }
});

Route::get('/trigger/assert', function () {
    try {
        assert(false, "Assertion failed!");
    } catch (Throwable $e) {
        Log::error("Caught AssertionError: " . $e->getMessage());
        return "Caught AssertionError: " . $e->getMessage();
    } finally {
        Log::info("Finally block executed after AssertionError.");
    }
});

Route::get('/trigger/undefined-function', function () {
    try {
        missing_function();
    } catch (Throwable $e) {
        Log::error("Caught Error: " . $e->getMessage());
        return "Caught Error: " . $e->getMessage();
    } finally {
        Log::info("Finally block executed after Undefined Function Error.");
    }
});

Route::get('/trigger/undefined-method', function () {
    try {
        $obj = new stdClass();
        $obj->missingMethod();
    } catch (Throwable $e) {
        Log::error("Caught Error: " . $e->getMessage());
        return "Caught Error: " . $e->getMessage();
    } finally {
        Log::info("Finally block executed after Undefined Method Error.");
    }
});

Route::get('/trigger/parse', function () {
    try {
        eval('echo "Hello;'); // Missing quote = ParseError
    } catch (Throwable $e) {
        Log::error("Caught ParseError: " . $e->getMessage());
        return "Caught ParseError: " . $e->getMessage();
    } finally {
        Log::info("Finally block executed after ParseError.");
    }
});

Route::get('/trigger/memory', function () {
    try {
        echo "<h3>Simulating Memory Exhaustion</h3>";

        // Instead of truly exhausting memory, simulate it
        $maxChunks = 1000000; // adjust as needed
        $chunkSize = 1024*1024; // 1MB per chunk
        $simulatedMemoryUsage = 0;

        for ($i=0; $i<$maxChunks; $i++) {
            $simulatedMemoryUsage += $chunkSize;
            if ($simulatedMemoryUsage > 50*1024*1024) { // simulate 50MB limit
                throw new \Error("Simulated Memory Exhaustion at {$simulatedMemoryUsage} bytes");
            }
        }

    } catch (\Throwable $e) {
        Log::error("Caught Throwable: " . $e->getMessage());
        echo "<h3 style='color:green'>Caught Throwable:</h3>";
        echo $e->getMessage();
    } finally {
        echo "<hr><strong>Finally block executed.</strong>";
    }
});

// Route to fetch the last few lines of laravel.log (for AJAX refresh)
Route::get('/logs', function () {
    $path = storage_path('logs/laravel.log');
    if (!File::exists($path)) {
        return response("No log file found.", 200);
    }

    $lines = array_slice(file($path), -50); // last 50 lines
    return response()->json([
        'logs' => implode('', $lines)
    ]);
});

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && $error['type'] === E_ERROR) {
        if (str_contains($error['message'], 'Allowed memory size')) {
            echo "<h3 style='color:red'>💥 Memory Exhaustion Detected!</h3>";
            echo $error['message'];
            Log::critical("Memory Exhaustion: " . $error['message']);
        } elseif (str_contains($error['message'], 'Parse error')) {
            echo "<h3 style='color:red'>💥 Parse Error Detected!</h3>";
            echo $error['message'];
            Log::critical("Parse Error: " . $error['message']);
        }
    }
});
