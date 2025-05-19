<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController; 
use App\Http\Controllers\Auth\PasswordResetLinkController;
// use App\Http\Controllers\Auth\RegisteredUserController; // Ligne commentée car enregistrement désactivé
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// Routes d'enregistrement - DÉSACTIVÉES
// Route::get('register', [RegisteredUserController::class, 'create'])
//                 ->middleware('guest')
//                 ->name('register');

// Route::post('register', [RegisteredUserController::class, 'store'])
//                 ->middleware('guest');

// Routes de connexion
Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->middleware('guest')
                ->name('login');

Route::post('login', [AuthenticatedSessionController::class, 'store'])
                ->middleware('guest');

// Routes pour mot de passe oublié
Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                ->middleware('guest')
                ->name('password.request');

Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                ->middleware('guest')
                ->name('password.email');

Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                ->middleware('guest')
                ->name('password.reset');

Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->middleware('guest')
                ->name('password.store');

// Routes pour la vérification d'email (si activée)
Route::get('verify-email', EmailVerificationPromptController::class)
                ->middleware('auth')
                ->name('verification.notice');

Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
                ->middleware(['auth', 'signed', 'throttle:6,1'])
                ->name('verification.verify');

Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware(['auth', 'throttle:6,1'])
                ->name('verification.send');

// Routes pour la confirmation de mot de passe
Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->middleware('auth')
                ->name('password.confirm');

Route::post('confirm-password', [ConfirmablePasswordController::class, 'store'])
                ->middleware('auth');

// Route pour la mise à jour du mot de passe (depuis le profil utilisateur)
Route::put('password', [PasswordController::class, 'update'])->middleware('auth')->name('password.update');

// Route de déconnexion
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->middleware('auth')
                ->name('logout');