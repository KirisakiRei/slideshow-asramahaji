<?php

use App\Http\Controllers\DisplayController;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Keep public kiosk displays alive: never dump stack traces to /display*.
        $exceptions->render(function (Throwable $e, $request) {
            if (! $request->is('display', 'display/*')) {
                return null;
            }

            report($e);

            if ($request->is('display/status') || $request->expectsJson()) {
                return response()->json([
                    'hash' => DisplayController::HASH_ERROR,
                    'message' => 'Display status unavailable',
                ], 200);
            }

            try {
                return response()->view('display.show', [
                    'slides' => [],
                    'config' => DisplayController::defaultConfig(),
                    'facilitySlots' => [],
                    'eventSlides' => [],
                    'runningTexts' => collect(),
                    // Stable sentinel — never use time() here (causes reload thrash).
                    'statusHash' => DisplayController::HASH_ERROR,
                    'error' => 'Display sementara tidak tersedia. Mencoba memuat ulang…',
                    'previewGroupName' => null,
                ], 200);
            } catch (Throwable $viewError) {
                return response(
                    '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8">'
                    .'<meta http-equiv="refresh" content="30">'
                    .'<title>Display</title>'
                    .'<style>body{margin:0;min-height:100vh;display:flex;align-items:center;'
                    .'justify-content:center;background:#f7f1e8;color:#2f281f;font-family:system-ui,sans-serif;text-align:center;padding:2rem}'
                    .'</style></head><body><div><h1>Display sementara tidak tersedia</h1>'
                    .'<p>Halaman akan dimuat ulang otomatis.</p></div></body></html>',
                    200,
                    ['Content-Type' => 'text/html; charset=UTF-8']
                );
            }
        });
    })->create();
