<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PainelController;
use App\Http\Controllers\JobTaskController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/', [PainelController::class, 'dashboard'])->name('dash');

    Route::get('job/{job}/{list?}/{task?}', [PainelController::class, 'indexJob'])->name('job');

    //Trabalho
    Route::post('new-job', [JobTaskController::class, 'newJob'])->name('newJob');
    Route::post('edit-job', [JobTaskController::class, 'editJob'])->name('editJob');
    Route::post('delete-job', [JobTaskController::class, 'deleteJob'])->name('deleteJob');

    //Lista
    Route::post('new-list', [JobTaskController::class, 'newList'])->name('newList');
    Route::post('list-update', [JobTaskController::class, 'listUpdate'])->name('listUpdate');
    Route::post('delete-list', [JobTaskController::class, 'deleteList'])->name('deleteList');

    //Tarefas
    Route::post('new-task', [JobTaskController::class, 'newTask'])->name('newTask');
    Route::post('task-update', [JobTaskController::class, 'taskUpdate'])->name('taskUpdate');
    Route::post('delete-task', [JobTaskController::class, 'deleteTask'])->name('deleteTask');

    //Etiquetas
    Route::post('new-tag', [JobTaskController::class, 'newTag'])->name('newTag');
    Route::post('delete-tag', [JobTaskController::class, 'deleteTag'])->name('deleteTag');

    //Custom Fields
    Route::post('task-field', [JobTaskController::class, 'taskField'])->name('taskField');
    Route::post('task-field-update', [JobTaskController::class, 'taskFieldUpdate'])->name('taskFieldUpdate');
    Route::post('delete-field', [JobTaskController::class, 'deleteField'])->name('deleteField');

    Route::post('associate-user-job', [JobTaskController::class, 'associateuserJob'])->name('associateuserJob');
    Route::post('disassociate-user-job', [JobTaskController::class, 'disassociateuserJob'])->name('disassociateuserJob');
    Route::post('search-user-job', [JobTaskController::class, 'searchUserJob'])->name('searchUserJob');

    Route::post('search-controller', [PainelController::class, 'searchController'])->name('searchController');
});

Route::get('login', [LoginController::class, 'index'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login');
Route::get('register', [RegisterController::class, 'index'])->name('register');
Route::post('register', [RegisterController::class, 'register'])->name('register');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');