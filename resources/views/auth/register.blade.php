<!DOCTYPE html>
<!--
Template Name: Icewall - HTML Admin Dashboard Template
Author: Left4code
Website: http://www.left4code.com/
Contact: muhammadrizki@left4code.com
Purchase: https://themeforest.net/user/left4code/portfolio
Renew Support: https://themeforest.net/user/left4code/portfolio
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<html lang="en" class="light">
    <!-- BEGIN: Head -->
    <head>
        <meta charset="utf-8">
        <link href="dist/images/logo.svg" rel="shortcut icon">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="DSSC Inventory Management System">
        <meta name="keywords" content="dssc, inventory, management, system">
        <meta name="author" content="DSSC">
        <title>Register - DSSC Inventory Management System</title>
        <link href="{{ asset('dist/images/logodssc.png') }}" rel="shortcut icon">
        <!-- BEGIN: CSS Assets-->
        <link rel="stylesheet" href="dist/css/app.css" />
        <!-- Add Font Awesome for password toggle icon -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <style>
            .password-container {
                position: relative;
            }
            .toggle-password {
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
                color: #888;
            }
        </style>
        <!-- END: CSS Assets-->
    </head>
    <!-- END: Head -->
    <body class="login">
        <div class="container sm:px-10">
            <div class="block xl:grid grid-cols-2 gap-4">
                <!-- BEGIN: Register Info -->
                <div class="hidden xl:flex flex-col min-h-screen">
                    <a href="https://dssc.edu.ph/" class="-intro-x flex items-center pt-5" target="_blank" rel="noopener noreferrer">
                        <img alt="DSSC Logo" height="300" width="300" src="http://202.137.126.204:4455/uploads/settings/1_theG1690183835.gif">
                    </a>
                    <div class="my-auto">
                        <img alt="Inventory System" class="-intro-x w-1/2 -mt-16" src="dist/images/bg.png">
                        <div class="-intro-x text-white font-small text-3xl leading-tight mt-10">
                            Office of the Supply and Property Unit
                            <br>
                            Inventory Management System
                        </div>
                    </div>
                </div>
                <!-- END: Register Info -->
                <!-- BEGIN: Register Form -->
                <div class="h-screen xl:h-auto flex py-5 xl:py-0 my-10 xl:my-0">
                    <div class="my-auto mx-auto xl:ml-20 bg-white dark:bg-darkmode-600 xl:bg-transparent px-5 sm:px-8 py-8 xl:p-0 rounded-md shadow-md xl:shadow-none w-full sm:w-3/4 lg:w-2/4 xl:w-auto">
                        <h2 class="intro-x font-bold text-2xl xl:text-3xl text-center xl:text-left">
                            Sign Up
                        </h2>

                        <form method="POST" action="{{ route('register') }}">
    @csrf

                <div class="mt-4">
                        <select name="role" required
                            class="intro-x login__input form-control py-3 px-4 block w-full @error('role') border-red-500 @enderror">
                            <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select Role</option>
                            <option value="Inventory Manager" {{ old('role') == 'Inventory Manager' ? 'selected' : '' }}>Inventory Manager</option>
                            <option value="Inspector" {{ old('role') == 'Inspector' ? 'selected' : '' }}>Inspector</option>
                            <option value="Department User" {{ old('role') == 'Department User' ? 'selected' : '' }}>Department User</option>
                        </select>
                        @error('role')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

    
    @if ($errors->any())
    <div class="intro-x mt-4">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <div class="intro-x mt-8">
        <input type="text" name="name" required 
            class="intro-x login__input form-control py-3 px-4 block w-full @error('name') border-red-500 @enderror" 
            placeholder="Full Name" value="{{ old('name') }}">
        @error('name')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror

        <input type="email" name="email" required 
            class="intro-x login__input form-control py-3 px-4 block w-full mt-4 @error('email') border-red-500 @enderror" 
            placeholder="Email Address" value="{{ old('email') }}">
        @error('email')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror

        
        <div class="password-container mt-4">
            <input type="password" name="password" required 
                class="intro-x login__input form-control py-3 px-4 block w-full @error('password') border-red-500 @enderror" 
                placeholder="Password" id="password">
            <i class="fas fa-eye-slash toggle-password" onclick="togglePassword('password')"></i>
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="password-container mt-4">
            <input type="password" name="password_confirmation" required 
                class="intro-x login__input form-control py-3 px-4 block w-full" 
                placeholder="Confirm Password" id="password_confirmation">
            <i class="fas fa-eye-slash toggle-password" onclick="togglePassword('password_confirmation')"></i>
        </div>
    </div>

    <div class="intro-x mt-5 xl:mt-8 text-center xl:text-left">
        <button type="submit" class="btn btn-primary py-3 px-4 w-full xl:w-32 xl:mr-3 align-top">Register</button>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary py-3 px-4 w-full xl:w-32 mt-3 xl:mt-0 align-top">Back</a>
    </div>
</form>

<div class="intro-x mt-10 xl:mt-24 text-slate-600 dark:text-slate-500 text-center xl:text-left">
                            Powered by BSIT-4C: Raynaldo Ayangco and Neil Marvin Recaport
                        </div>

                    </div>
                </div>
                <!-- END: Register Form -->
            </div>
        </div>
        
        
        <!-- BEGIN: JS Assets-->
        <script src="dist/js/app.js"></script>
        <script>
            function togglePassword(inputId) {
                const passwordInput = document.getElementById(inputId);
                const toggleIcon = passwordInput.nextElementSibling;
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleIcon.classList.remove('fa-eye-slash');
                    toggleIcon.classList.add('fa-eye');
                } else {
                    passwordInput.type = 'password';
                    toggleIcon.classList.remove('fa-eye');
                    toggleIcon.classList.add('fa-eye-slash');
                }
            }
        </script>
        <!-- END: JS Assets-->
    </body>
</html>